<?php

namespace App\Http\Controllers;

use App\Models\PaymentTransaction;
use App\Models\Subscription;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\StreamedResponse;

class PaiementsController extends Controller
{
    /**
     * Display transactions dashboard.
     */
    public function index(Request $request)
    {
        $days = (int) $request->input('days', 30);

        /* ── KPIs ─────────────────────────────────── */
        $base = PaymentTransaction::inPeriod($days);

        $encaisse     = (clone $base)->success()->sum('amount');
        $failed       = (clone $base)->failed()->sum('amount');
        $pendingCount = (clone $base)->pending()->count();
        $totalCount   = (clone $base)->count();
        $successRate  = $totalCount > 0
            ? round(((clone $base)->success()->count() / $totalCount) * 100, 1)
            : 0;
        $failRate = $totalCount > 0
            ? round(((clone $base)->failed()->count() / $totalCount) * 100, 1)
            : 0;

        // vs période précédente (growth)
        $prevEncaisse = PaymentTransaction::where('status', 'success')
            ->whereBetween('created_at', [now()->subDays($days * 2), now()->subDays($days)])
            ->sum('amount');
        $growth = $prevEncaisse > 0
            ? round((($encaisse - $prevEncaisse) / $prevEncaisse) * 100)
            : 0;

        $stats = compact('encaisse', 'failed', 'pendingCount', 'successRate', 'failRate', 'growth');

        /* ── Graphe 30 jours (succès + échecs par jour) ─ */
        $chartDays = [];
        for ($i = 29; $i >= 0; $i--) {
            $day   = now()->subDays($i)->format('Y-m-d');
            $label = now()->subDays($i)->format('d/m');
            $chartDays[] = [
                'label'   => $label,
                'success' => PaymentTransaction::success()->whereDate('created_at', $day)->sum('amount'),
                'failed'  => PaymentTransaction::failed()->whereDate('created_at', $day)->sum('amount'),
            ];
        }

        /* ── Répartition par opérateur ─────────────── */
        $operatorStats = PaymentTransaction::success()
            ->inPeriod($days)
            ->select('payment_method', DB::raw('SUM(amount) as total'), DB::raw('COUNT(*) as count'))
            ->groupBy('payment_method')
            ->get()
            ->keyBy('payment_method');

        $totalOp = $operatorStats->sum('total') ?: 1;

        /* ── Tableau transactions (filtré + paginé) ── */
        $query = PaymentTransaction::with(['payer', 'subscription'])
            ->latest();

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('reference', 'like', "%{$s}%")
                  ->orWhere('phone_payer', 'like', "%{$s}%")
                  ->orWhere('operator_reference', 'like', "%{$s}%");
            });
        }
        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }
        if ($request->filled('operator') && $request->operator !== 'all') {
            $query->where('payment_method', $request->operator);
        }
        if ($request->filled('plan') && $request->plan !== 'all') {
            $query->whereHas('subscription', fn($q) => $q->where('plan', $request->plan));
        }
        if ($request->filled('date')) {
            $query->whereDate('created_at', $request->date);
        }

        // Filtre période
        $query->inPeriod($days);

        $transactions = $query->paginate(20);

        return view('pages.payments', compact(
            'stats', 'chartDays', 'operatorStats', 'totalOp', 'transactions', 'days'
        ));
    }

    /**
     * Show one transaction as JSON (modal).
     */
    public function show(string $id)
    {
        $tx = PaymentTransaction::with(['payer', 'subscription'])->findOrFail($id);
        return response()->json([
            'reference'           => $tx->reference,
            'status'              => $tx->status,
            'status_label'        => $tx->status_label,
            'status_badge'        => $tx->status_badge,
            'amount'              => number_format($tx->amount, 0, ',', ' '),
            'method_label'        => $tx->method_label,
            'method_color'        => $tx->method_color,
            'phone_payer'         => $tx->phone_payer ?? '—',
            'plan_label'          => $tx->plan_label ?? '—',
            'plan_badge'          => $tx->plan_badge ?? 'badge-gray',
            'paid_at'             => $tx->paid_at?->format('d M Y — H:i:s') ?? '—',
            'operator_reference'  => $tx->operator_reference ?? '—',
            'operator_transaction_id' => $tx->operator_transaction_id ?? '—',
            'failure_reason'      => $tx->failure_reason,
            'payer_name'          => $tx->payer_name,
        ]);
    }

    /**
     * Mark transaction as refunded.
     */
    public function refund(Request $request, string $id)
    {
        $tx = PaymentTransaction::findOrFail($id);

        $request->validate([
            'amount' => 'required|numeric|min:1|max:' . $tx->amount,
            'reason' => 'required|string',
        ]);

        $tx->update([
            'status'         => 'refunded',
            'failure_reason' => 'Remboursement : ' . $request->reason,
        ]);

        // Si lié à un abonnement, annuler aussi l'abonnement
        if ($tx->subscription) {
            $tx->subscription->update([
                'status'              => 'cancelled',
                'cancellation_reason' => $request->reason,
                'cancelled_at'        => now(),
            ]);
        }

        return redirect()->route('payments.index')
            ->with('toast_success', 'Remboursement initié avec succès.');
    }

    /**
     * Retry a failed/pending transaction (mark pending for re-processing).
     */
    public function retry(string $id)
    {
        $tx = PaymentTransaction::findOrFail($id);
        $tx->update(['status' => 'pending', 'failure_reason' => null]);

        return redirect()->route('payments.index')
            ->with('toast_info', 'Transaction relancée.');
    }

    /**
     * Cancel a pending transaction.
     */
    public function cancel(string $id)
    {
        $tx = PaymentTransaction::findOrFail($id);
        $tx->update(['status' => 'cancelled']);

        return redirect()->route('payments.index')
            ->with('toast_warning', 'Transaction annulée.');
    }

    /**
     * Export filtered transactions as CSV.
     */
    public function export(Request $request): StreamedResponse
    {
        $days  = (int) $request->input('days', 30);
        $query = PaymentTransaction::with(['payer', 'subscription'])
            ->inPeriod($days)
            ->latest();

        return response()->streamDownload(function () use ($query) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['Référence', 'Payeur', 'Téléphone', 'Opérateur', 'Montant (FCFA)', 'Plan', 'Statut', 'Date']);

            $query->chunk(500, function ($rows) use ($handle) {
                foreach ($rows as $tx) {
                    fputcsv($handle, [
                        $tx->reference,
                        $tx->payer_name,
                        $tx->phone_payer,
                        $tx->method_label,
                        $tx->amount,
                        $tx->plan_label ?? '—',
                        $tx->status_label,
                        $tx->created_at->format('d/m/Y H:i'),
                    ]);
                }
            });

            fclose($handle);
        }, 'transactions_' . now()->format('Ymd') . '.csv', [
            'Content-Type' => 'text/csv',
        ]);
    }

    /* ── Resource stubs ──────────────────────────── */
    public function create() {}
    public function store(Request $request) {}
    public function edit(string $id) {}
    public function update(Request $request, string $id) {}
    public function destroy(string $id) {}
}
