<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class JournauxController extends Controller
{
    /**
     * Display activity logs dashboard.
     */
    public function index(Request $request)
    {
        /* ── KPIs ─────────────────────────────────── */
        $stats = [
            'today'        => ActivityLog::today()->count(),
            'week'         => ActivityLog::thisWeek()->count(),
            'month'        => ActivityLog::thisMonth()->count(),
            'active_admins'=> ActivityLog::today()
                                ->whereNotNull('causer_id')
                                ->distinct('causer_id')
                                ->count('causer_id'),
        ];

        /* ── Liste des admins (pour filtre) ─────────── */
        $admins = ActivityLog::whereNotNull('causer_id')
            ->select('causer_type', 'causer_id')
            ->with('causer')
            ->distinct()
            ->get()
            ->map(fn($l) => [
                'id'   => $l->causer_id,
                'type' => $l->causer_type,
                'name' => $l->causer_name,
            ])
            ->unique('id')
            ->values();

        /* ── Logs filtrés + paginés ───────────────── */
        $query = ActivityLog::with(['causer', 'subject'])->orderByDesc('occurred_at');

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('description', 'like', "%{$s}%")
                  ->orWhere('action', 'like', "%{$s}%")
                  ->orWhere('ip_address', 'like', "%{$s}%");
            });
        }

        if ($request->filled('action_group') && $request->action_group !== 'all') {
            $g = $request->action_group;
            $query->where(function ($q) use ($g) {
                $q->where('action', 'like', "{$g}_%")
                  ->orWhere('action', 'like', "%_{$g}");
            });
        }

        if ($request->filled('causer_id') && $request->causer_id !== 'all') {
            $query->where('causer_id', $request->causer_id);
        }

        if ($request->filled('date')) {
            $query->whereDate('occurred_at', $request->date);
        }

        if ($request->filled('level') && $request->level !== 'all') {
            $keywords = match ($request->level) {
                'success' => ['login', 'created', 'approved', 'verified', 'activated', 'published'],
                'info'    => ['updated', 'viewed', 'exported'],
                'warning' => ['suspended', 'cancelled', 'disabled'],
                'error'   => ['deleted', 'failed', 'error'],
                default   => [],
            };
            if ($keywords) {
                $query->where(function ($q) use ($keywords) {
                    foreach ($keywords as $kw) {
                        $q->orWhere('action', 'like', "%{$kw}%");
                    }
                });
            }
        }

        $logs = $query->paginate(20);

        return view('pages.activity-logs', compact('stats', 'admins', 'logs'));
    }

    /**
     * Return one log as JSON for the detail modal.
     */
    public function show(string $id)
    {
        $log = ActivityLog::with(['causer', 'subject'])->findOrFail($id);
        return response()->json([
            'occurred_at'   => $log->occurred_at->format('d M Y — H:i:s'),
            'level_badge'   => $log->level_badge,
            'level_label'   => $log->level_label,
            'action'        => $log->action,
            'action_group'  => $log->action_group,
            'description'   => $log->description,
            'causer_name'   => $log->causer_name,
            'causer_id'     => $log->causer_id,
            'ip_address'    => $log->ip_address ?? '—',
            'user_agent'    => $log->user_agent ?? '—',
            'old_values'    => $log->old_values,
            'new_values'    => $log->new_values,
        ]);
    }

    /**
     * Export logs as CSV.
     */
    public function export(Request $request): StreamedResponse
    {
        $query = ActivityLog::with('causer')->orderByDesc('occurred_at');

        if ($request->filled('date')) {
            $query->whereDate('occurred_at', $request->date);
        }

        return response()->streamDownload(function () use ($query) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['ID', 'Date', 'Niveau', 'Action', 'Description', 'Admin', 'IP']);

            $query->chunk(500, function ($rows) use ($handle) {
                foreach ($rows as $log) {
                    fputcsv($handle, [
                        $log->id_log,
                        $log->occurred_at->format('d/m/Y H:i:s'),
                        $log->level_label,
                        $log->action,
                        $log->description,
                        $log->causer_name,
                        $log->ip_address,
                    ]);
                }
            });

            fclose($handle);
        }, 'activity_logs_' . now()->format('Ymd') . '.csv', [
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
