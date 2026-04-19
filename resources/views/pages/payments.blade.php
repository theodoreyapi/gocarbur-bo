@extends('layouts.master', ['title' => 'Paiements Mobile Money', 'subTitle' => 'Paiements Mobile Money'])

@push('scripts')
    <script>
        // Charts
        const days30 = Array.from({
            length: 30
        }, (_, i) => {
            const d = new Date();
            d.setDate(d.getDate() - (29 - i));
            return d.getDate() + '/' + (d.getMonth() + 1);
        });
        new Chart(document.getElementById('txChart'), {
            type: 'line',
            data: {
                labels: days30,
                datasets: [{
                        label: 'Succès (FCFA)',
                        data: Array.from({
                            length: 30
                        }, () => Math.floor(Math.random() * 200000) + 50000),
                        borderColor: '#10B981',
                        backgroundColor: 'rgba(16,185,129,.08)',
                        fill: true,
                        tension: .4,
                        borderWidth: 2,
                        pointRadius: 0
                    },
                    {
                        label: 'Échoués (FCFA)',
                        data: Array.from({
                            length: 30
                        }, () => Math.floor(Math.random() * 15000) + 2000),
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

        new Chart(document.getElementById('operatorChart'), {
            type: 'doughnut',
            data: {
                labels: ['Orange', 'Wave', 'MTN', 'Moov'],
                datasets: [{
                    data: [44, 34, 16, 6],
                    backgroundColor: ['#FF6600', '#1CB5E0', '#FFCC00', '#00A651'],
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

        function filterByPeriod(v) {
            showToast('Période : ' + v + ' jours', 'info');
        }

        function exportCSV() {
            showToast('Export CSV en cours...', 'info');
        }

        function viewTx(ref) {
            document.getElementById('modalTxRef').textContent = ref;
            openModal('modalTxDetail');
        }

        function refundTx(ref) {
            closeModal('modalTxDetail');
            openModal('modalRefund');
        }

        function retryTx(ref) {
            confirmAction('Relancer cette transaction ?', () => showToast('Transaction relancée', 'info'));
        }

        function cancelTx(ref) {
            confirmAction('Annuler cette transaction ?', () => showToast('Transaction annulée', 'warning'));
        }

        function refundFromModal() {
            closeModal('modalTxDetail');
            openModal('modalRefund');
        }

        function confirmRefund() {
            showToast('Remboursement initié avec succès', 'success');
            closeModal('modalRefund');
        }
    </script>
@endpush

@push('csss')
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
@endpush

@section('content')
    <main class="page-content">

        <div class="page-header"
            style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:12px">
            <div>
                <h1>Transactions & Paiements</h1>
                <p>Journal de tous les paiements Mobile Money — Orange, MTN, Wave, Moov.</p>
            </div>
            <div style="display:flex;gap:10px">
                <select class="form-select" style="width:160px" onchange="filterByPeriod(this.value)">
                    <option value="7">7 derniers jours</option>
                    <option value="30" selected>30 derniers jours</option>
                    <option value="90">3 derniers mois</option>
                    <option value="365">Cette année</option>
                </select>
                <button class="btn btn-secondary" onclick="exportCSV()"><i class="fa-solid fa-download"></i>
                    Export CSV</button>
            </div>
        </div>

        <!-- KPIs -->
        <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(200px,1fr));gap:14px;margin-bottom:24px">
            <div class="stat-card">
                <div class="stat-icon" style="background:#D1FAE5"><i class="fa-solid fa-circle-check"
                        style="color:var(--success)"></i></div>
                <div class="stat-value">4 230 000</div>
                <div class="stat-label">FCFA encaissés (30j)</div>
                <div class="stat-change up"><i class="fa-solid fa-arrow-up"></i> +18% vs mois dernier</div>
            </div>
            <div class="stat-card">
                <div class="stat-icon" style="background:#FEE2E2"><i class="fa-solid fa-circle-xmark"
                        style="color:var(--danger)"></i></div>
                <div class="stat-value">187 500</div>
                <div class="stat-label">FCFA échoués (30j)</div>
                <div class="stat-change down"><i class="fa-solid fa-exclamation"></i> 4.3% taux échec</div>
            </div>
            <div class="stat-card">
                <div class="stat-icon" style="background:#FEF3C7"><i class="fa-solid fa-clock"
                        style="color:var(--warning)"></i></div>
                <div class="stat-value">12</div>
                <div class="stat-label">Transactions en attente</div>
                <div class="stat-change down"><i class="fa-solid fa-triangle-exclamation"></i> À traiter</div>
            </div>
            <div class="stat-card">
                <div class="stat-icon" style="background:#DBEAFE"><i class="fa-solid fa-chart-pie"
                        style="color:var(--info)"></i></div>
                <div class="stat-value">95.7%</div>
                <div class="stat-label">Taux de succès global</div>
                <div class="stat-change up"><i class="fa-solid fa-arrow-up"></i> Excellent</div>
            </div>
        </div>

        <!-- Graphe + répartition par opérateur -->
        <div style="display:grid;grid-template-columns:2fr 1fr;gap:20px;margin-bottom:24px">

            <div class="card">
                <div class="card-header">
                    <div class="card-title"><i class="fa-solid fa-chart-bar" style="color:var(--primary)"></i>
                        Volume de transactions (30 jours)</div>
                </div>
                <div class="card-body">
                    <div class="chart-container"><canvas id="txChart"></canvas></div>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <div class="card-title"><i class="fa-solid fa-mobile-screen-button" style="color:var(--info)"></i>
                        Répartition par opérateur</div>
                </div>
                <div class="card-body">
                    <div style="margin-bottom:16px"><canvas id="operatorChart" style="max-height:180px"></canvas>
                    </div>
                    <div style="display:flex;flex-direction:column;gap:10px">
                        <div style="display:flex;align-items:center;justify-content:space-between">
                            <span style="display:flex;align-items:center;gap:7px;font-size:13px">
                                <span
                                    style="width:10px;height:10px;background:#FF6600;border-radius:50%;display:inline-block"></span>Orange
                                Money
                            </span>
                            <div style="text-align:right">
                                <div class="fw-700" style="font-size:13px">1 872 000 F</div>
                                <div style="font-size:11px;color:var(--text-muted)">44%</div>
                            </div>
                        </div>
                        <div style="display:flex;align-items:center;justify-content:space-between">
                            <span style="display:flex;align-items:center;gap:7px;font-size:13px">
                                <span
                                    style="width:10px;height:10px;background:#1CB5E0;border-radius:50%;display:inline-block"></span>Wave
                            </span>
                            <div style="text-align:right">
                                <div class="fw-700" style="font-size:13px">1 439 000 F</div>
                                <div style="font-size:11px;color:var(--text-muted)">34%</div>
                            </div>
                        </div>
                        <div style="display:flex;align-items:center;justify-content:space-between">
                            <span style="display:flex;align-items:center;gap:7px;font-size:13px">
                                <span
                                    style="width:10px;height:10px;background:#FFCC00;border-radius:50%;display:inline-block"></span>MTN
                                MoMo
                            </span>
                            <div style="text-align:right">
                                <div class="fw-700" style="font-size:13px">676 000 F</div>
                                <div style="font-size:11px;color:var(--text-muted)">16%</div>
                            </div>
                        </div>
                        <div style="display:flex;align-items:center;justify-content:space-between">
                            <span style="display:flex;align-items:center;gap:7px;font-size:13px">
                                <span
                                    style="width:10px;height:10px;background:#00A651;border-radius:50%;display:inline-block"></span>Moov
                                Money
                            </span>
                            <div style="text-align:right">
                                <div class="fw-700" style="font-size:13px">243 000 F</div>
                                <div style="font-size:11px;color:var(--text-muted)">6%</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Table transactions -->
        <div class="card">
            <div class="filter-bar">
                <div style="position:relative;flex:1;min-width:220px">
                    <i class="fa-solid fa-magnifying-glass"
                        style="position:absolute;left:11px;top:50%;transform:translateY(-50%);color:var(--text-muted)"></i>
                    <input type="text" placeholder="Référence, numéro de téléphone..."
                        style="padding:8px 12px 8px 34px;border:1.5px solid var(--border);border-radius:var(--radius-sm);font-size:13px;width:100%;outline:none;background:var(--bg)">
                </div>
                <select class="form-select" style="width:140px">
                    <option value="">Tous statuts</option>
                    <option>Succès</option>
                    <option>Échoué</option>
                    <option>En attente</option>
                    <option>Remboursé</option>
                </select>
                <select class="form-select" style="width:150px">
                    <option value="">Tous opérateurs</option>
                    <option>Orange Money</option>
                    <option>Wave</option>
                    <option>MTN MoMo</option>
                    <option>Moov Money</option>
                </select>
                <select class="form-select" style="width:160px">
                    <option value="">Tous les plans</option>
                    <option>User Premium</option>
                    <option>Station Pro</option>
                    <option>Station Premium</option>
                    <option>Garage Pro</option>
                    <option>Garage Premium</option>
                </select>
                <input type="date" class="form-control" style="width:150px">
            </div>

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
                        <tr>
                            <td><code
                                    style="font-size:11px;background:var(--bg);padding:3px 7px;border-radius:5px;letter-spacing:.03em">SUB-KA8X2P1Q</code>
                            </td>
                            <td>
                                <div style="display:flex;align-items:center;gap:9px">
                                    <div class="user-avatar">KA</div>
                                    <div>
                                        <div class="fw-600" style="font-size:13px">Kouassi Aya</div>
                                        <div style="font-size:11px;color:var(--text-muted)">+225 07 12 34 56</div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span
                                    style="display:inline-flex;align-items:center;gap:6px;font-size:12px;font-weight:600">
                                    <span
                                        style="width:9px;height:9px;background:#FF6600;border-radius:50%;display:inline-block"></span>Orange
                                    Money
                                </span>
                            </td>
                            <td><span class="fw-700" style="color:var(--success);font-size:14px">1 500 FCFA</span>
                            </td>
                            <td><span class="badge badge-success"><i class="fa-solid fa-user" style="font-size:9px"></i>
                                    User Premium</span></td>
                            <td><span class="badge badge-success"><i class="fa-solid fa-circle"
                                        style="font-size:7px"></i> Succès</span></td>
                            <td>
                                <div style="font-size:13px">18 Mar 2024</div>
                                <div style="font-size:11px;color:var(--text-muted)">14:32:08</div>
                            </td>
                            <td>
                                <div class="dropdown">
                                    <button class="btn btn-sm btn-secondary" data-toggle="dropdown"><i
                                            class="fa-solid fa-ellipsis"></i></button>
                                    <div class="dropdown-menu">
                                        <a class="dropdown-item" href="#" onclick="viewTx('SUB-KA8X2P1Q')"><i
                                                class="fa-solid fa-eye"></i> Voir
                                            détail</a>
                                        <a class="dropdown-item text-danger" href="#"
                                            onclick="refundTx('SUB-KA8X2P1Q')"><i class="fa-solid fa-rotate-left"></i>
                                            Rembourser</a>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td><code
                                    style="font-size:11px;background:var(--bg);padding:3px 7px;border-radius:5px;letter-spacing:.03em">SUB-TE9P2X4R</code>
                            </td>
                            <td>
                                <div style="display:flex;align-items:center;gap:9px">
                                    <div class="user-avatar" style="background:linear-gradient(135deg,#3B82F6,#1D4ED8)">TE
                                    </div>
                                    <div>
                                        <div class="fw-600" style="font-size:13px">Total Énergies Cocody</div>
                                        <div style="font-size:11px;color:var(--text-muted)">+225 22 44 55 66</div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span
                                    style="display:inline-flex;align-items:center;gap:6px;font-size:12px;font-weight:600">
                                    <span
                                        style="width:9px;height:9px;background:#1CB5E0;border-radius:50%;display:inline-block"></span>Wave
                                </span>
                            </td>
                            <td><span class="fw-700" style="color:var(--success);font-size:14px">32 500
                                    FCFA</span></td>
                            <td><span class="badge badge-purple"><i class="fa-solid fa-gas-pump"
                                        style="font-size:9px"></i> Station Premium</span></td>
                            <td><span class="badge badge-success"><i class="fa-solid fa-circle"
                                        style="font-size:7px"></i> Succès</span></td>
                            <td>
                                <div style="font-size:13px">01 Mar 2024</div>
                                <div style="font-size:11px;color:var(--text-muted)">09:15:41</div>
                            </td>
                            <td>
                                <div class="dropdown">
                                    <button class="btn btn-sm btn-secondary" data-toggle="dropdown"><i
                                            class="fa-solid fa-ellipsis"></i></button>
                                    <div class="dropdown-menu">
                                        <a class="dropdown-item" href="#" onclick="viewTx('SUB-TE9P2X4R')"><i
                                                class="fa-solid fa-eye"></i> Voir
                                            détail</a>
                                        <a class="dropdown-item text-danger" href="#"
                                            onclick="refundTx('SUB-TE9P2X4R')"><i class="fa-solid fa-rotate-left"></i>
                                            Rembourser</a>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td><code
                                    style="font-size:11px;background:var(--bg);padding:3px 7px;border-radius:5px;letter-spacing:.03em">SUB-BK3M7N2S</code>
                            </td>
                            <td>
                                <div style="display:flex;align-items:center;gap:9px">
                                    <div class="user-avatar" style="background:linear-gradient(135deg,#EF4444,#B91C1C)">BK
                                    </div>
                                    <div>
                                        <div class="fw-600" style="font-size:13px">Bamba Koné</div>
                                        <div style="font-size:11px;color:var(--text-muted)">+225 05 98 76 54</div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span
                                    style="display:inline-flex;align-items:center;gap:6px;font-size:12px;font-weight:600">
                                    <span
                                        style="width:9px;height:9px;background:#FFCC00;border-radius:50%;display:inline-block"></span>MTN
                                    MoMo
                                </span>
                            </td>
                            <td><span class="fw-700" style="color:var(--danger);font-size:14px">1 500 FCFA</span>
                            </td>
                            <td><span class="badge badge-success"><i class="fa-solid fa-user" style="font-size:9px"></i>
                                    User Premium</span></td>
                            <td><span class="badge badge-danger"><i class="fa-solid fa-circle" style="font-size:7px"></i>
                                    Échoué</span></td>
                            <td>
                                <div style="font-size:13px">17 Mar 2024</div>
                                <div style="font-size:11px;color:var(--text-muted)">11:45:22</div>
                            </td>
                            <td>
                                <div class="dropdown">
                                    <button class="btn btn-sm btn-secondary" data-toggle="dropdown"><i
                                            class="fa-solid fa-ellipsis"></i></button>
                                    <div class="dropdown-menu">
                                        <a class="dropdown-item" href="#" onclick="viewTx('SUB-BK3M7N2S')"><i
                                                class="fa-solid fa-eye"></i> Voir
                                            détail</a>
                                        <a class="dropdown-item" href="#" onclick="retryTx('SUB-BK3M7N2S')"><i
                                                class="fa-solid fa-rotate-right"></i> Relancer</a>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td><code
                                    style="font-size:11px;background:var(--bg);padding:3px 7px;border-radius:5px;letter-spacing:.03em">SUB-GA5R1Q9W</code>
                            </td>
                            <td>
                                <div style="display:flex;align-items:center;gap:9px">
                                    <div class="user-avatar" style="background:linear-gradient(135deg,#10B981,#059669)">GA
                                    </div>
                                    <div>
                                        <div class="fw-600" style="font-size:13px">Garage Auto Plus</div>
                                        <div style="font-size:11px;color:var(--text-muted)">+225 27 33 44 55</div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span
                                    style="display:inline-flex;align-items:center;gap:6px;font-size:12px;font-weight:600">
                                    <span
                                        style="width:9px;height:9px;background:#FFCC00;border-radius:50%;display:inline-block"></span>MTN
                                    MoMo
                                </span>
                            </td>
                            <td><span class="fw-700" style="color:var(--success);font-size:14px">12 500
                                    FCFA</span></td>
                            <td><span class="badge badge-info"><i class="fa-solid fa-wrench" style="font-size:9px"></i>
                                    Garage Pro</span></td>
                            <td><span class="badge badge-success"><i class="fa-solid fa-circle"
                                        style="font-size:7px"></i> Succès</span></td>
                            <td>
                                <div style="font-size:13px">05 Mar 2024</div>
                                <div style="font-size:11px;color:var(--text-muted)">16:02:55</div>
                            </td>
                            <td>
                                <div class="dropdown">
                                    <button class="btn btn-sm btn-secondary" data-toggle="dropdown"><i
                                            class="fa-solid fa-ellipsis"></i></button>
                                    <div class="dropdown-menu">
                                        <a class="dropdown-item" href="#" onclick="viewTx('SUB-GA5R1Q9W')"><i
                                                class="fa-solid fa-eye"></i> Voir
                                            détail</a>
                                        <a class="dropdown-item text-danger" href="#"
                                            onclick="refundTx('SUB-GA5R1Q9W')"><i class="fa-solid fa-rotate-left"></i>
                                            Rembourser</a>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td><code
                                    style="font-size:11px;background:var(--bg);padding:3px 7px;border-radius:5px;letter-spacing:.03em">SUB-NA2K8J7X</code>
                            </td>
                            <td>
                                <div style="display:flex;align-items:center;gap:9px">
                                    <div class="user-avatar" style="background:linear-gradient(135deg,#8B5CF6,#6D28D9)">NA
                                    </div>
                                    <div>
                                        <div class="fw-600" style="font-size:13px">N'Guessan Ahou</div>
                                        <div style="font-size:11px;color:var(--text-muted)">+225 01 23 45 67</div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span
                                    style="display:inline-flex;align-items:center;gap:6px;font-size:12px;font-weight:600">
                                    <span
                                        style="width:9px;height:9px;background:#00A651;border-radius:50%;display:inline-block"></span>Moov
                                    Money
                                </span>
                            </td>
                            <td><span class="fw-700" style="color:var(--text-muted);font-size:14px">1 500
                                    FCFA</span></td>
                            <td><span class="badge badge-success"><i class="fa-solid fa-user" style="font-size:9px"></i>
                                    User Premium</span></td>
                            <td><span class="badge badge-warning"><i class="fa-solid fa-circle"
                                        style="font-size:7px"></i> En attente</span></td>
                            <td>
                                <div style="font-size:13px">18 Mar 2024</div>
                                <div style="font-size:11px;color:var(--text-muted)">14:55:01</div>
                            </td>
                            <td>
                                <div class="dropdown">
                                    <button class="btn btn-sm btn-secondary" data-toggle="dropdown"><i
                                            class="fa-solid fa-ellipsis"></i></button>
                                    <div class="dropdown-menu">
                                        <a class="dropdown-item" href="#" onclick="viewTx('SUB-NA2K8J7X')"><i
                                                class="fa-solid fa-eye"></i> Voir
                                            détail</a>
                                        <a class="dropdown-item" href="#" onclick="retryTx('SUB-NA2K8J7X')"><i
                                                class="fa-solid fa-rotate-right"></i> Relancer</a>
                                        <a class="dropdown-item text-danger" href="#"
                                            onclick="cancelTx('SUB-NA2K8J7X')"><i class="fa-solid fa-ban"></i>
                                            Annuler</a>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div
                style="padding:16px 20px;display:flex;align-items:center;justify-content:space-between;border-top:1px solid var(--border);flex-wrap:wrap;gap:10px">
                <span style="font-size:13px;color:var(--text-muted)">Affichage 1–20 sur 847 transactions</span>
                <div class="pagination">
                    <div class="page-item disabled"><i class="fa-solid fa-chevron-left" style="font-size:11px"></i></div>
                    <div class="page-item active">1</div>
                    <div class="page-item">2</div>
                    <div class="page-item">3</div>
                    <div class="page-item">...</div>
                    <div class="page-item">43</div>
                    <div class="page-item"><i class="fa-solid fa-chevron-right" style="font-size:11px"></i></div>
                </div>
            </div>
        </div>
    </main>

    <!-- Modal Détail transaction -->
    <div class="modal-overlay" id="modalTxDetail">
        <div class="modal-box" style="max-width:480px">
            <div class="modal-header">
                <h5><i class="fa-solid fa-receipt" style="color:var(--primary)"></i> Détail de la transaction</h5>
                <button class="modal-close" data-modal-close="modalTxDetail">✕</button>
            </div>
            <div class="modal-body">
                <div style="background:var(--bg);border-radius:var(--radius-sm);padding:16px;margin-bottom:16px">
                    <div style="display:flex;justify-content:space-between;margin-bottom:10px">
                        <span style="font-size:13px;color:var(--text-muted)">Référence</span>
                        <code style="font-size:12px;font-weight:700" id="modalTxRef">SUB-KA8X2P1Q</code>
                    </div>
                    <div style="display:flex;justify-content:space-between;margin-bottom:10px">
                        <span style="font-size:13px;color:var(--text-muted)">Statut</span>
                        <span class="badge badge-success"><i class="fa-solid fa-circle" style="font-size:7px"></i>
                            Succès</span>
                    </div>
                    <div style="display:flex;justify-content:space-between;margin-bottom:10px">
                        <span style="font-size:13px;color:var(--text-muted)">Montant</span>
                        <span class="fw-700" style="color:var(--success);font-size:15px">1 500 FCFA</span>
                    </div>
                    <div style="display:flex;justify-content:space-between;margin-bottom:10px">
                        <span style="font-size:13px;color:var(--text-muted)">Opérateur</span>
                        <span class="fw-600" style="font-size:13px">🟠 Orange Money</span>
                    </div>
                    <div style="display:flex;justify-content:space-between;margin-bottom:10px">
                        <span style="font-size:13px;color:var(--text-muted)">Numéro payeur</span>
                        <span class="fw-600" style="font-size:13px">+225 07 12 34 56</span>
                    </div>
                    <div style="display:flex;justify-content:space-between;margin-bottom:10px">
                        <span style="font-size:13px;color:var(--text-muted)">Plan</span>
                        <span class="badge badge-success">User Premium</span>
                    </div>
                    <div style="display:flex;justify-content:space-between;margin-bottom:10px">
                        <span style="font-size:13px;color:var(--text-muted)">Date</span>
                        <span style="font-size:13px;font-weight:600">18 Mar 2024 — 14:32:08</span>
                    </div>
                    <div style="display:flex;justify-content:space-between">
                        <span style="font-size:13px;color:var(--text-muted)">ID opérateur</span>
                        <code style="font-size:11px">CI241803142247890</code>
                    </div>
                </div>
                <button class="btn btn-danger" style="width:100%" onclick="refundFromModal()">
                    <i class="fa-solid fa-rotate-left"></i> Initier un remboursement
                </button>
            </div>
        </div>
    </div>

    <!-- Modal Remboursement -->
    <div class="modal-overlay" id="modalRefund">
        <div class="modal-box" style="max-width:420px">
            <div class="modal-header">
                <h5><i class="fa-solid fa-rotate-left" style="color:var(--danger)"></i> Remboursement</h5>
                <button class="modal-close" data-modal-close="modalRefund">✕</button>
            </div>
            <div class="modal-body">
                <div class="alert alert-warning" style="margin-bottom:16px">
                    <i class="fa-solid fa-triangle-exclamation"></i>
                    <span>Le remboursement sera effectué sur le numéro Mobile Money d'origine. Cette action est
                        irréversible.</span>
                </div>
                <div style="margin-bottom:14px">
                    <label class="form-label">Montant à rembourser (FCFA)</label>
                    <input type="number" class="form-control" value="1500">
                </div>
                <div>
                    <label class="form-label">Raison du remboursement *</label>
                    <select class="form-select" style="margin-bottom:10px">
                        <option>Erreur de paiement</option>
                        <option>Abonnement annulé par admin</option>
                        <option>Demande du client</option>
                        <option>Doublon de transaction</option>
                        <option>Autre</option>
                    </select>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" data-modal-close="modalRefund">Annuler</button>
                <button class="btn btn-danger" onclick="confirmRefund()"><i class="fa-solid fa-check"></i> Confirmer
                    le remboursement</button>
            </div>
        </div>
    </div>

    <div class="toast-container"></div>
@endsection
