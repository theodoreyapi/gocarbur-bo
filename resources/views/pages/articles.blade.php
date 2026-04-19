@extends('layouts.master', ['title' => 'Articles & Conseils', 'subTitle' => 'Articles & Conseils'])

@push('scripts')
    <script>
        function toggleSponsor(cb) {
            document.getElementById('sponsorFields').style.display = cb.checked ? 'grid' : 'none';
        }

        function editArticle() {
            openModal('modalAddArticle');
        }

        function saveArticle() {
            showToast('Article publié avec succès', 'success');
            closeModal('modalAddArticle');
        }
    </script>
@endpush

@section('content')
    <main class="page-content">
        <div class="page-header" style="display:flex;align-items:center;justify-content:space-between">
            <div>
                <h1>Articles & Conseils</h1>
                <p>Gérez le contenu éditorial de l'application.</p>
            </div>
            <button class="btn btn-primary" data-modal-open="modalAddArticle"><i class="fa-solid fa-plus"></i>
                Nouvel article</button>
        </div>

        <div class="card">
            <div class="card-header" data-tabs>
                <div class="tab-nav">
                    <button class="tab-btn active" data-tab="tab-all">Tous (42)</button>
                    <button class="tab-btn" data-tab="tab-published">Publiés (35)</button>
                    <button class="tab-btn" data-tab="tab-draft">Brouillons (7)</button>
                    <button class="tab-btn" data-tab="tab-sponsored">Sponsorisés (8)</button>
                </div>
            </div>
            <div class="filter-bar">
                <div style="position:relative;flex:1"><i class="fa-solid fa-magnifying-glass"
                        style="position:absolute;left:11px;top:50%;transform:translateY(-50%);color:var(--text-muted)"></i><input
                        type="text" placeholder="Titre, catégorie..."
                        style="padding:8px 12px 8px 34px;border:1.5px solid var(--border);border-radius:var(--radius-sm);font-size:13px;width:100%;outline:none;background:var(--bg)">
                </div>
                <select class="form-select" style="width:180px">
                    <option>Toutes catégories</option>
                    <option>Entretien auto</option>
                    <option>Économie carburant</option>
                    <option>Sécurité</option>
                    <option>Documents admin</option>
                    <option>Astuces</option>
                </select>
            </div>
            <div class="table-wrapper">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Article</th>
                            <th>Catégorie</th>
                            <th>Vues</th>
                            <th>Lecture</th>
                            <th>Sponsorisé</th>
                            <th>Statut</th>
                            <th>Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>
                                <div class="fw-600" style="max-width:320px">5 signes que votre voiture a besoin
                                    d'une vidange</div>
                            </td>
                            <td><span class="badge badge-primary">Entretien auto</span></td>
                            <td>1 248</td>
                            <td>3 min</td>
                            <td><i class="fa-solid fa-times" style="color:var(--text-light)"></i></td>
                            <td><span class="badge badge-success">Publié</span></td>
                            <td>15 Mar 2024</td>
                            <td>
                                <div class="dropdown"><button class="btn btn-sm btn-secondary" data-toggle="dropdown"><i
                                            class="fa-solid fa-ellipsis"></i></button>
                                    <div class="dropdown-menu"><a class="dropdown-item" href="#"><i
                                                class="fa-solid fa-eye"></i> Prévisualiser</a><a class="dropdown-item"
                                            href="#" onclick="editArticle()"><i class="fa-solid fa-pen"></i>
                                            Modifier</a><a class="dropdown-item" href="#"><i
                                                class="fa-solid fa-eye-slash"></i> Dépublier</a>
                                        <div class="dropdown-divider"></div><a class="dropdown-item text-danger"
                                            href="#"><i class="fa-solid fa-trash"></i> Supprimer</a>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <div class="fw-600" style="max-width:320px">Comment réduire de 20% sa consommation
                                    de carburant</div>
                            </td>
                            <td><span class="badge badge-info">Économie carburant</span></td>
                            <td>987</td>
                            <td>5 min</td>
                            <td><i class="fa-solid fa-check" style="color:var(--warning)"></i></td>
                            <td><span class="badge badge-success">Publié</span></td>
                            <td>12 Mar 2024</td>
                            <td>
                                <div class="dropdown"><button class="btn btn-sm btn-secondary" data-toggle="dropdown"><i
                                            class="fa-solid fa-ellipsis"></i></button>
                                    <div class="dropdown-menu"><a class="dropdown-item" href="#"><i
                                                class="fa-solid fa-eye"></i> Prévisualiser</a><a class="dropdown-item"
                                            href="#"><i class="fa-solid fa-pen"></i>
                                            Modifier</a>
                                        <div class="dropdown-divider"></div><a class="dropdown-item text-danger"
                                            href="#"><i class="fa-solid fa-trash"></i> Supprimer</a>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <div class="fw-600" style="max-width:320px">Renouvellement visite technique : tout
                                    ce qu'il faut savoir</div>
                            </td>
                            <td><span class="badge badge-warning">Documents admin</span></td>
                            <td>0</td>
                            <td>6 min</td>
                            <td><i class="fa-solid fa-times" style="color:var(--text-light)"></i></td>
                            <td><span class="badge badge-gray">Brouillon</span></td>
                            <td>18 Mar 2024</td>
                            <td>
                                <div class="dropdown"><button class="btn btn-sm btn-secondary" data-toggle="dropdown"><i
                                            class="fa-solid fa-ellipsis"></i></button>
                                    <div class="dropdown-menu"><a class="dropdown-item" href="#"><i
                                                class="fa-solid fa-eye"></i> Prévisualiser</a><a class="dropdown-item"
                                            href="#"><i class="fa-solid fa-pen"></i>
                                            Modifier</a><a class="dropdown-item" href="#"><i
                                                class="fa-solid fa-upload"></i> Publier</a>
                                        <div class="dropdown-divider"></div><a class="dropdown-item text-danger"
                                            href="#"><i class="fa-solid fa-trash"></i> Supprimer</a>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div
                style="padding:16px 20px;display:flex;align-items:center;justify-content:space-between;border-top:1px solid var(--border)">
                <span style="font-size:13px;color:var(--text-muted)">42 articles au total</span>
                <div class="pagination">
                    <div class="page-item disabled"><i class="fa-solid fa-chevron-left" style="font-size:11px"></i></div>
                    <div class="page-item active">1</div>
                    <div class="page-item">2</div>
                    <div class="page-item"><i class="fa-solid fa-chevron-right" style="font-size:11px"></i></div>
                </div>
            </div>
        </div>
    </main>

    <!-- Modal Ajouter Article -->
    <div class="modal-overlay" id="modalAddArticle">
        <div class="modal-box" style="max-width:700px">
            <div class="modal-header">
                <h5><i class="fa-solid fa-newspaper" style="color:var(--primary)"></i> Nouvel article</h5><button
                    class="modal-close" data-modal-close="modalAddArticle">✕</button>
            </div>
            <div class="modal-body">
                <div style="display:flex;flex-direction:column;gap:14px">
                    <div><label class="form-label">Titre *</label><input type="text" class="form-control"
                            placeholder="Ex: 5 signes que votre voiture a besoin d'une vidange"></div>
                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:14px">
                        <div><label class="form-label">Catégorie *</label><select class="form-select">
                                <option>Entretien auto</option>
                                <option>Économie carburant</option>
                                <option>Sécurité</option>
                                <option>Documents admin</option>
                                <option>Astuces mécaniques</option>
                                <option>Actualités</option>
                            </select></div>
                        <div><label class="form-label">Temps de lecture (min)</label><input type="number"
                                class="form-control" value="5" min="1"></div>
                    </div>
                    <div><label class="form-label">Extrait</label>
                        <textarea class="form-control" rows="2" placeholder="Résumé court affiché dans la liste..."></textarea>
                    </div>
                    <div><label class="form-label">Contenu *</label>
                        <textarea class="form-control" rows="8" placeholder="Rédigez votre article ici..."></textarea>
                    </div>
                    <div
                        style="display:flex;align-items:center;gap:12px;padding:12px;background:var(--bg);border-radius:var(--radius-sm)">
                        <label class="toggle"><input type="checkbox" onchange="toggleSponsor(this)"><span
                                class="toggle-slider"></span></label>
                        <span class="fw-600" style="font-size:13px">Article sponsorisé</span>
                    </div>
                    <div id="sponsorFields" style="display:none;display:grid;grid-template-columns:1fr 1fr;gap:14px">
                        <div><label class="form-label">Nom du sponsor</label><input type="text" class="form-control"
                                placeholder="Ex: Castrol"></div>
                        <div><label class="form-label">URL sponsor</label><input type="url" class="form-control"
                                placeholder="https://..."></div>
                    </div>
                    <div style="display:flex;align-items:center;gap:12px">
                        <label class="toggle"><input type="checkbox"><span class="toggle-slider"></span></label>
                        <span class="fw-600" style="font-size:13px">Publier immédiatement</span>
                    </div>
                </div>
            </div>
            <div class="modal-footer"><button class="btn btn-secondary"
                    data-modal-close="modalAddArticle">Annuler</button><button class="btn btn-secondary"><i
                        class="fa-solid fa-floppy-disk"></i> Sauver brouillon</button><button class="btn btn-primary"
                    onclick="saveArticle()"><i class="fa-solid fa-upload"></i> Publier</button></div>
        </div>
    </div>

    <div class="toast-container"></div>
@endsection
