@extends('layouts.master', ['title' => 'Paiements Mobile Money', 'subTitle' => 'Paiements Mobile Money'])

@push('css')
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
@endpush

@push('scripts')
    <script>
        /* ── Flash toasts ────────────────────────────── */
        @if (session('toast_success'))
            showToast(@json(session('toast_success')), 'success');
        @endif
        @if (session('toast_info'))
            showToast(@json(session('toast_info')), 'info');
        @endif
        @if (session('toast_warning'))
            showToast(@json(session('toast_warning')), 'warning');
        @endif

        /* ── Graphe volume 30 jours ─────────────────── */
        new Chart(document.getElementById('txChart'), {
            type: 'line',
            data: {
                labels: @json(array_column($chartDays, 'label')),
                datasets: [{
                        label: 'Succès (FCFA)',
                        data: @json(array_column($chartDays, 'success')),
                        borderColor: '#10B981',
                        backgroundColor: 'rgba(16,185,129,.08)',
                        fill: true,
                        tension: .4,
                        borderWidth: 2,
                        pointRadius: 0
                    },
                    {
                        label: 'Échoués (FCFA)',
                        data: @json(array_column($chartDays, 'failed')),
                        borderColor: '#EF4444',
                        backgroundColor: 'rgba(239,68,68,.06)',
                        fill: true,
                        tension: .4,
                        borderWidth: 2,
                        pointRadius: 0
                    },
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            font: {
                                size: 11
                            },
                            boxWidth: 10
                        }
                    }
                },
                scales: {
                    x: {
                        grid: {
                            display: false
                        },
                        ticks: {
                            maxTicksLimit: 8,
                            font: {
                                size: 11
                            }
                        }
                    },
                    y: {
                        grid: {
                            color: '#F1F5F9'
                        },
                        ticks: {
                            callback: v => v >= 1000 ? (v / 1000).toFixed(0) + 'k' : v,
                            font: {
                                size: 11
                            }
                        }
                    }
                }
            }
        });

        /* ── Graphe donut opérateurs ────────────────── */
        const opData = @json($operatorStats->values()->map(fn($o) => $o->total));
        const opLabels = @json($operatorStats->keys()->map(fn($k) => \App\Models\PaymentTransaction::methodLabels()[$k] ?? $k));
        const opColors = @json($operatorStats->keys()->map(fn($k) => \App\Models\PaymentTransaction::methodColors()[$k] ?? '#6B7280'));

        new Chart(document.getElementById('operatorChart'), {
            type: 'doughnut',
            data: {
                labels: opLabels,
                datasets: [{
                    data: opData,
                    backgroundColor: opColors,
                    borderWidth: 0
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '70%',
                plugins: {
                    legend: {
                        display: false
                    }
                }
            }
        });

        /* ── Filtrage période ────────────────────────── */
        function filterByPeriod(v) {
            const url = new URL(window.location.href);
            url.searchParams.set('days', v);
            window.location.href = url.toString();
        }

        /* ── Modal détail ────────────────────────────── */
        function viewTx(id) {
            fetch(`/payments/${id}`)
                .then(r => r.json())
                .then(tx => {
                    document.getElementById('modalTxRef').textContent = tx.reference;
                    document.getElementById('modalTxStatus').innerHTML =
                        `<span class="badge ${tx.status_badge}"><i class="fa-solid fa-circle" style="font-size:7px"></i> ${tx.status_label}</span>`;
                    document.getElementById('modalTxAmount').textContent = tx.amount + ' FCFA';
                    document.getElementById('modalTxOperator').innerHTML =
                        `<span style="display:inline-flex;align-items:center;gap:6px"><span style="width:10px;height:10px;background:${tx.method_color};border-radius:50%;display:inline-block"></span>${tx.method_label}</span>`;
                    document.getElementById('modalTxPhone').textContent = tx.phone_payer;
                    document.getElementById('modalTxPlan').innerHTML =
                        `<span class="badge ${tx.plan_badge}">${tx.plan_label}</span>`;
                    document.getElementById('modalTxDate').textContent = tx.paid_at;
                    document.getElementById('modalTxOpRef').textContent = tx.operator_reference;
                    document.getElementById('modalTxOpId').textContent = tx.operator_transaction_id;
                    document.getElementById('modalRefundId').value = id;
                    document.getElementById('modalRefundAmount').value = tx.amount.replace(/\s/g, '');

                    // Bouton rembourser visible que si succès
                    const btnRefund = document.getElementById('btnRefundFromDetail');
                    btnRefund.style.display = tx.status === 'success' ? '' : 'none';

                    openModal('modalTxDetail');
                });
        }

        function openRefundModal() {
            closeModal('modalTxDetail');
            openModal('modalRefund');
        }

        /* ── Retry / Cancel (forms cachés) ─────────────── */
        function retryTx(id) {
            confirmAction('Relancer cette transaction ?', () => {
                document.getElementById(`retryForm_${id}`).submit();
            });
        }

        function cancelTx(id) {
            confirmAction('Annuler cette transaction ?', () => {
                document.getElementById(`cancelForm_${id}`).submit();
            });
        }
    </script>
@endpush

@section('content')
    <main class="page-content">

        {{-- ── Page header ─────────────────────────────── --}}
        <div class="page-header"
            style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:12px">
            <div>
                <h1>Transactions & Paiements</h1>
                <p>Journal de tous les paiements Mobile Money — Orange, MTN, Wave, Moov.</p>
            </div>
            <div style="display:flex;gap:10px">
                <select class="form-select" style="width:180px" onchange="filterByPeriod(this.value)">
                    <option value="7" {{ $days == 7 ? 'selected' : '' }}>7 derniers jours</option>
                    <option value="30" {{ $days == 30 ? 'selected' : '' }}>30 derniers jours</option>
                    <option value="90" {{ $days == 90 ? 'selected' : '' }}>3 derniers mois</option>
                    <option value="365" {{ $days == 365 ? 'selected' : '' }}>Cette année</option>
                </select>
                <a href="{{ route('payments.export', array_merge(request()->all(), ['days' => $days])) }}"
                    class="btn btn-secondary">
                    <i class="fa-solid fa-download"></i> Export CSV
                </a>
            </div>
        </div>

        {{-- ── KPIs ─────────────────────────────────────── --}}
        <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(200px,1fr));gap:14px;margin-bottom:24px">
            <div class="stat-card">
                <div class="stat-icon" style="background:#D1FAE5">
                    <i class="fa-solid fa-circle-check" style="color:var(--success)"></i>
                </div>
                <div class="stat-value">{{ number_format($stats['encaisse'], 0, ',', ' ') }}</div>
                <div class="stat-label">FCFA encaissés ({{ $days }}j)</div>
                <div class="stat-change {{ $stats['growth'] >= 0 ? 'up' : 'down' }}">
                    <i class="fa-solid fa-arrow-{{ $stats['growth'] >= 0 ? 'up' : 'down' }}"></i>
                    {{ $stats['growth'] >= 0 ? '+' : '' }}{{ $stats['growth'] }}% vs période préc.
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon" style="background:#FEE2E2">
                    <i class="fa-solid fa-circle-xmark" style="color:var(--danger)"></i>
                </div>
                <div class="stat-value">{{ number_format($stats['failed'], 0, ',', ' ') }}</div>
                <div class="stat-label">FCFA échoués ({{ $days }}j)</div>
                <div class="stat-change down">
                    <i class="fa-solid fa-exclamation"></i> {{ $stats['failRate'] }}% taux échec
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon" style="background:#FEF3C7">
                    <i class="fa-solid fa-clock" style="color:var(--warning)"></i>
                </div>
                <div class="stat-value">{{ $stats['pendingCount'] }}</div>
                <div class="stat-label">Transactions en attente</div>
                <div class="stat-change down">
                    <i class="fa-solid fa-triangle-exclamation"></i> À traiter
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon" style="background:#DBEAFE">
                    <i class="fa-solid fa-chart-pie" style="color:var(--info)"></i>
                </div>
                <div class="stat-value">{{ $stats['successRate'] }}%</div>
                <div class="stat-label">Taux de succès global</div>
                <div class="stat-change up">
                    <i class="fa-solid fa-arrow-up"></i>
                    {{ $stats['successRate'] >= 90 ? 'Excellent' : 'À surveiller' }}
                </div>
            </div>
        </div>

        {{-- ── Graphe + donut opérateurs ───────────────── --}}
        <div style="display:grid;grid-template-columns:2fr 1fr;gap:20px;margin-bottom:24px">

            <div class="card">
                <div class="card-header">
                    <div class="card-title">
                        <i class="fa-solid fa-chart-bar" style="color:var(--primary)"></i>
                        Volume de transactions ({{ $days }} jours)
                    </div>
                </div>
                <div class="card-body">
                    <div class="chart-container"><canvas id="txChart"></canvas></div>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <div class="card-title">
                        <i class="fa-solid fa-mobile-screen-button" style="color:var(--info)"></i>
                        Répartition par opérateur
                    </div>
                </div>
                <div class="card-body">
                    @if ($operatorStats->isEmpty())
                        <p style="text-align:center;color:var(--text-muted);padding:20px 0">Aucune donnée</p>
                    @else
                        <div style="margin-bottom:16px"><canvas id="operatorChart" style="max-height:180px"></canvas></div>
                        <div style="display:flex;flex-direction:column;gap:10px">
                            @foreach ($operatorStats as $method => $op)
                                @php
                                    $color = \App\Models\PaymentTransaction::methodColors()[$method] ?? '#6B7280';
                                    $label = \App\Models\PaymentTransaction::methodLabels()[$method] ?? $method;
                                    $pct = $totalOp > 0 ? round(($op->total / $totalOp) * 100) : 0;
                                @endphp
                                <div style="display:flex;align-items:center;justify-content:space-between">
                                    <span style="display:flex;align-items:center;gap:7px;font-size:13px">
                                        <span
                                            style="width:10px;height:10px;background:{{ $color }};border-radius:50%;display:inline-block"></span>
                                        {{ $label }}
                                    </span>
                                    <div style="text-align:right">
                                        <div class="fw-700" style="font-size:13px">
                                            {{ number_format($op->total, 0, ',', ' ') }} F</div>
                                        <div style="font-size:11px;color:var(--text-muted)">{{ $pct }}%</div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- ── Table transactions ──────────────────────── --}}
        <div class="card">

            {{-- Filtres --}}
            <form method="GET" action="{{ route('payments.index') }}" class="filter-bar">
                <input type="hidden" name="days" value="{{ $days }}">

                <div style="position:relative;flex:1;min-width:220px">
                    <i class="fa-solid fa-magnifying-glass"
                        style="position:absolute;left:11px;top:50%;transform:translateY(-50%);color:var(--text-muted)"></i>
                    <input type="text" name="search" placeholder="Référence, téléphone..."
                        value="{{ request('search') }}"
                        style="padding:8px 12px 8px 34px;border:1.5px solid var(--border);border-radius:var(--radius-sm);font-size:13px;width:100%;outline:none;background:var(--bg)">
                </div>

                <select name="status" class="form-select" style="width:140px" onchange="this.form.submit()">
                    <option value="all" {{ request('status', 'all') === 'all' ? 'selected' : '' }}>Tous statuts</option>
                    <option value="success" {{ request('status') === 'success' ? 'selected' : '' }}>Succès</option>
                    <option value="failed" {{ request('status') === 'failed' ? 'selected' : '' }}>Échoué</option>
                    <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>En attente</option>
                    <option value="refunded" {{ request('status') === 'refunded' ? 'selected' : '' }}>Remboursé</option>
                    <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>Annulé</option>
                </select>

                <select name="operator" class="form-select" style="width:160px" onchange="this.form.submit()">
                    <option value="all" {{ request('operator', 'all') === 'all' ? 'selected' : '' }}>Tous opérateurs
                    </option>
                    @foreach (\App\Models\PaymentTransaction::methodLabels() as $val => $label)
                        <option value="{{ $val }}" {{ request('operator') === $val ? 'selected' : '' }}>
                            {{ $label }}</option>
                    @endforeach
                </select>

                <select name="plan" class="form-select" style="width:160px" onchange="this.form.submit()">
                    <option value="all" {{ request('plan', 'all') === 'all' ? 'selected' : '' }}>Tous les plans
                    </option>
                    @foreach (\App\Models\Subscription::planLabels() as $val => $label)
                        <option value="{{ $val }}" {{ request('plan') === $val ? 'selected' : '' }}>
                            {{ $label }}</option>
                    @endforeach
                </select>

                <input type="date" name="date" class="form-control" style="width:155px"
                    value="{{ request('date') }}" onchange="this.form.submit()">

                <button type="submit" class="btn btn-secondary btn-sm">
                    <i class="fa-solid fa-magnifying-glass"></i>
                </button>
            </form>

            <div class="table-wrapper">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Référence</th>
                            <th>Payeur</th>
                            <th>Opérateur</th>
                            <th>Montant</th>
                            <th>Plan</th>
                            <th>Statut</th>
                            <th>Date & heure</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($transactions as $tx)
                            @php
                                $avatarColors = [
                                    '#3B82F6,#1D4ED8',
                                    '#EF4444,#B91C1C',
                                    '#10B981,#059669',
                                    '#F59E0B,#D97706',
                                    '#8B5CF6,#6D28D9',
                                    '#EC4899,#BE185D',
                                ];
                                $color = $avatarColors[$tx->id_pay_transac % count($avatarColors)];
                            @endphp
                            <tr>
                                {{-- Référence --}}
                                <td>
                                    <code
                                        style="font-size:11px;background:var(--bg);padding:3px 7px;border-radius:5px;letter-spacing:.03em">
                                        {{ $tx->reference }}
                                    </code>
                                </td>

                                {{-- Payeur --}}
                                <td>
                                    <div style="display:flex;align-items:center;gap:9px">
                                        <div
                                            style="width:34px;height:34px;background:linear-gradient(135deg,{{ $color }});border-radius:50%;display:flex;align-items:center;justify-content:center;color:#fff;font-size:12px;font-weight:700;flex-shrink:0">
                                            {{ $tx->payer_initials }}
                                        </div>
                                        <div>
                                            <div class="fw-600" style="font-size:13px">{{ $tx->payer_name }}</div>
                                            <div style="font-size:11px;color:var(--text-muted)">
                                                {{ $tx->phone_payer ?? '—' }}</div>
                                        </div>
                                    </div>
                                </td>

                                {{-- Opérateur --}}
                                <td>
                                    <span
                                        style="display:inline-flex;align-items:center;gap:6px;font-size:12px;font-weight:600">
                                        <span
                                            style="width:9px;height:9px;background:{{ $tx->method_color }};border-radius:50%;display:inline-block"></span>
                                        {{ $tx->method_label }}
                                    </span>
                                </td>

                                {{-- Montant --}}
                                <td>
                                    <span class="fw-700" style="color:{{ $tx->amount_color }};font-size:14px">
                                        {{ number_format($tx->amount, 0, ',', ' ') }} FCFA
                                    </span>
                                </td>

                                {{-- Plan --}}
                                <td>
                                    @if ($tx->plan_label)
                                        <span class="badge {{ $tx->plan_badge }}">{{ $tx->plan_label }}</span>
                                    @else
                                        <span style="color:var(--text-muted);font-size:12px">—</span>
                                    @endif
                                </td>

                                {{-- Statut --}}
                                <td>
                                    <span class="badge {{ $tx->status_badge }}">
                                        <i class="fa-solid fa-circle" style="font-size:7px"></i>
                                        {{ $tx->status_label }}
                                    </span>
                                </td>

                                {{-- Date --}}
                                <td>
                                    <div style="font-size:13px">{{ $tx->created_at->format('d M Y') }}</div>
                                    <div style="font-size:11px;color:var(--text-muted)">
                                        {{ $tx->created_at->format('H:i:s') }}</div>
                                </td>

                                {{-- Actions --}}
                                <td>
                                    <div class="dropdown">
                                        <button class="btn btn-sm btn-secondary" data-toggle="dropdown">
                                            <i class="fa-solid fa-ellipsis"></i>
                                        </button>
                                        <div class="dropdown-menu">
                                            <a class="dropdown-item" href="#"
                                                onclick="viewTx({{ $tx->id_pay_transac }})">
                                                <i class="fa-solid fa-eye"></i> Voir détail
                                            </a>
                                            @if ($tx->status === 'success')
                                                <a class="dropdown-item text-danger" href="#"
                                                    onclick="viewTx({{ $tx->id_pay_transac }})">
                                                    <i class="fa-solid fa-rotate-left"></i> Rembourser
                                                </a>
                                            @endif
                                            @if (in_array($tx->status, ['failed', 'pending']))
                                                <a class="dropdown-item" href="#"
                                                    onclick="retryTx({{ $tx->id_pay_transac }})">
                                                    <i class="fa-solid fa-rotate-right"></i> Relancer
                                                </a>
                                            @endif
                                            @if ($tx->status === 'pending')
                                                <a class="dropdown-item text-danger" href="#"
                                                    onclick="cancelTx({{ $tx->id_pay_transac }})">
                                                    <i class="fa-solid fa-ban"></i> Annuler
                                                </a>
                                            @endif
                                        </div>
                                    </div>

                                    {{-- Formulaires cachés --}}
                                    <form id="retryForm_{{ $tx->id_pay_transac }}" method="POST"
                                        action="{{ route('payments.retry', $tx->id_pay_transac) }}" style="display:none">
                                        @csrf</form>
                                    <form id="cancelForm_{{ $tx->id_pay_transac }}" method="POST"
                                        action="{{ route('payments.cancel', $tx->id_pay_transac) }}"
                                        style="display:none">@csrf</form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" style="text-align:center;color:var(--text-muted);padding:32px">
                                    Aucune transaction trouvée.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            <div
                style="padding:16px 20px;display:flex;align-items:center;justify-content:space-between;border-top:1px solid var(--border);flex-wrap:wrap;gap:10px">
                <span style="font-size:13px;color:var(--text-muted)">
                    {{ $transactions->firstItem() }}–{{ $transactions->lastItem() }} sur {{ $transactions->total() }}
                    transactions
                </span>
                {{ $transactions->appends(request()->all())->links('vendor.pagination.simple') }}
            </div>
        </div>

    </main>

    {{-- ── Modal Détail transaction ─────────────────── --}}
    <div class="modal-overlay" id="modalTxDetail">
        <div class="modal-box" style="max-width:480px">
            <div class="modal-header">
                <h5><i class="fa-solid fa-receipt" style="color:var(--primary)"></i> Détail de la transaction</h5>
                <button class="modal-close" data-modal-close="modalTxDetail">✕</button>
            </div>
            <div class="modal-body">
                <div style="background:var(--bg);border-radius:var(--radius-sm);padding:16px;margin-bottom:16px">
                    @foreach ([['Référence', 'modalTxRef', 'code'], ['Statut', 'modalTxStatus', 'html'], ['Montant', 'modalTxAmount', 'strong'], ['Opérateur', 'modalTxOperator', 'html'], ['Numéro payeur', 'modalTxPhone', 'text'], ['Plan', 'modalTxPlan', 'html'], ['Date', 'modalTxDate', 'text'], ['Réf. opérateur', 'modalTxOpRef', 'code'], ['ID opérateur', 'modalTxOpId', 'code']] as [$label, $id, $type])
                        <div style="display:flex;justify-content:space-between;margin-bottom:10px;align-items:center">
                            <span style="font-size:13px;color:var(--text-muted)">{{ $label }}</span>
                            @if ($type === 'code')
                                <code style="font-size:11px;font-weight:700" id="{{ $id }}">—</code>
                            @elseif($type === 'strong')
                                <strong id="{{ $id }}" style="color:var(--success);font-size:15px">—</strong>
                            @else
                                <span id="{{ $id }}" style="font-size:13px;font-weight:600">—</span>
                            @endif
                        </div>
                    @endforeach
                </div>

                <button id="btnRefundFromDetail" class="btn btn-danger" style="width:100%" onclick="openRefundModal()">
                    <i class="fa-solid fa-rotate-left"></i> Initier un remboursement
                </button>
            </div>
        </div>
    </div>

    {{-- ── Modal Remboursement ──────────────────────── --}}
    <div class="modal-overlay" id="modalRefund">
        <div class="modal-box" style="max-width:420px">
            <div class="modal-header">
                <h5><i class="fa-solid fa-rotate-left" style="color:var(--danger)"></i> Remboursement</h5>
                <button class="modal-close" data-modal-close="modalRefund">✕</button>
            </div>
            <form id="refundForm" method="POST" action="">
                @csrf
                <div class="modal-body">
                    <div class="alert alert-warning" style="margin-bottom:16px">
                        <i class="fa-solid fa-triangle-exclamation"></i>
                        <span>Le remboursement sera effectué sur le numéro Mobile Money d'origine. Cette action est
                            irréversible.</span>
                    </div>
                    <input type="hidden" id="modalRefundId" name="_tx_id">
                    <div style="margin-bottom:14px">
                        <label class="form-label">Montant à rembourser (FCFA)</label>
                        <input type="number" name="amount" id="modalRefundAmount" class="form-control"
                            min="1">
                    </div>
                    <div>
                        <label class="form-label">Raison du remboursement *</label>
                        <select name="reason" class="form-select">
                            <option>Erreur de paiement</option>
                            <option>Abonnement annulé par admin</option>
                            <option>Demande du client</option>
                            <option>Doublon de transaction</option>
                            <option>Autre</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-modal-close="modalRefund">Annuler</button>
                    <button type="submit" class="btn btn-danger">
                        <i class="fa-solid fa-check"></i> Confirmer le remboursement
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Met à jour l'action du formulaire de remboursement dynamiquement
        document.getElementById('modalRefundId').addEventListener('change', function() {
            document.getElementById('refundForm').action = `/payments/${this.value}/refund`;
        });
        // Observateur MutationObserver pour détecter le changement de valeur via JS
        const refundIdInput = document.getElementById('modalRefundId');
        const observer = new MutationObserver(() => {
            document.getElementById('refundForm').action = `/payments/${refundIdInput.value}/refund`;
        });
        // Patch: set action when openRefundModal is called
        const _origOpenRefund = window.openRefundModal;
        window.openRefundModal = function() {
            const id = document.getElementById('modalRefundId').value;
            document.getElementById('refundForm').action = `/payments/${id}/refund`;
            closeModal('modalTxDetail');
            openModal('modalRefund');
        };
    </script>

    <div class="toast-container"></div>
@endsection
