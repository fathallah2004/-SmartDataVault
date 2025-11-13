@php
    $filters = $filters ?? [
        'search' => '',
        'role' => '',
        'sort' => 'created_at',
        'direction' => 'desc',
    ];
@endphp

<x-app-layout>
    <div class="space-y-6">
        @if (session('status'))
            <div class="rounded-2xl bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 text-blue-800 dark:text-blue-200 px-5 py-4 shadow-lg">
                <div class="flex items-center gap-3">
                    <i class="fas fa-check-circle text-xl"></i>
                    <span class="font-medium">{{ session('status') }}</span>
                </div>
            </div>
        @endif

        @if (session('error'))
            <div class="rounded-2xl bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 text-red-800 dark:text-red-200 px-5 py-4 shadow-lg">
                <div class="flex items-center gap-3">
                    <i class="fas fa-exclamation-triangle text-xl"></i>
                    <span class="font-medium">{{ session('error') }}</span>
                </div>
            </div>
        @endif

        <!-- KPI Cards matching user dashboard card style -->
        <section class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg border border-gray-200 dark:border-gray-700">
            <div class="px-8 py-6 border-b border-gray-200 dark:border-gray-700">
                <div class="flex flex-col lg:flex-row items-start lg:items-center justify-between gap-4 mb-4">
                    <div>
                        <h3 class="text-xl font-bold text-gray-900 dark:text-gray-100">
                            📊 Statistiques globales
                        </h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                            Vue d'ensemble de l'activité de la plateforme
                        </p>
                    </div>
                    <div class="flex items-center gap-4">
                        <span class="text-gray-700 dark:text-gray-300 font-medium">{{ Auth::user()->name }}</span>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-xl flex items-center space-x-2 transition-all duration-300 font-semibold hover:scale-105">
                                <i class="fas fa-sign-out-alt"></i>
                                <span>Déconnexion</span>
                            </button>
                        </form>
                    </div>
                </div>
                
                <!-- Statistiques en haut alignées horizontalement -->
                <div class="flex flex-wrap lg:flex-nowrap items-stretch gap-4 pt-4 border-t border-gray-200 dark:border-gray-700">
                    <div class="flex-1 min-w-0 bg-gradient-to-br from-blue-50 to-blue-100 dark:from-blue-900/20 dark:to-blue-800/20 rounded-xl border border-blue-200 dark:border-blue-800 p-4 hover:shadow-lg transition-all duration-300">
                        <div class="flex items-center gap-3">
                            <div class="p-3 rounded-xl bg-blue-500 dark:bg-blue-600 shadow-md flex-shrink-0">
                                <i class="fas fa-folder-open text-2xl text-white"></i>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-xs font-semibold text-blue-600 dark:text-blue-400 uppercase tracking-wide mb-1">Fichiers</p>
                                <p class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ $stats['total_files'] }}</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="flex-1 min-w-0 bg-gradient-to-br from-green-50 to-green-100 dark:from-green-900/20 dark:to-green-800/20 rounded-xl border border-green-200 dark:border-green-800 p-4 hover:shadow-lg transition-all duration-300">
                        <div class="flex items-center gap-3">
                            <div class="p-3 rounded-xl bg-green-500 dark:bg-green-600 shadow-md flex-shrink-0">
                                <i class="fas fa-users text-2xl text-white"></i>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-xs font-semibold text-green-600 dark:text-green-400 uppercase tracking-wide mb-1">Utilisateurs</p>
                                <p class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ $stats['total_users'] }}</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="flex-1 min-w-0 bg-gradient-to-br from-purple-50 to-purple-100 dark:from-purple-900/20 dark:to-purple-800/20 rounded-xl border border-purple-200 dark:border-purple-800 p-4 hover:shadow-lg transition-all duration-300">
                        <div class="flex items-center gap-3">
                            <div class="p-3 rounded-xl bg-purple-500 dark:bg-purple-600 shadow-md flex-shrink-0">
                                <i class="fas fa-user-shield text-2xl text-white"></i>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-xs font-semibold text-purple-600 dark:text-purple-400 uppercase tracking-wide mb-1">Administrateurs</p>
                                <p class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ $stats['total_admins'] }}</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="flex-1 min-w-0 bg-gradient-to-br from-yellow-50 to-yellow-100 dark:from-yellow-900/20 dark:to-yellow-800/20 rounded-xl border border-yellow-200 dark:border-yellow-800 p-4 hover:shadow-lg transition-all duration-300">
                        <div class="flex items-center gap-3">
                            <div class="p-3 rounded-xl bg-yellow-500 dark:bg-yellow-600 shadow-md flex-shrink-0">
                                <i class="fas fa-hard-drive text-2xl text-white"></i>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-xs font-semibold text-yellow-600 dark:text-yellow-400 uppercase tracking-wide mb-1">Stockage</p>
                                <p class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ $stats['total_storage'] }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Search and Filter Section matching user dashboard -->
        <section class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg border border-gray-200 dark:border-gray-700">
            <div class="px-8 py-6 border-b border-gray-200 dark:border-gray-700">
                <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-4">
                    <div>
                        <h3 class="text-xl font-bold text-gray-900 dark:text-gray-100">
                            👥 Gestion des membres
                        </h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                            Recherchez, filtrez et triez pour trouver rapidement le bon utilisateur.
                        </p>
                    </div>
                </div>

                <form id="user-filter-form" class="mt-4 flex flex-row gap-3 items-center">
                    <div class="relative flex-1 min-w-0">
                        <input
                            type="text"
                            name="search"
                            placeholder="Rechercher nom ou email…"
                            value="{{ $filters['search'] }}"
                            class="w-full pl-12 pr-4 py-3 rounded-xl border-2 border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-all duration-300"
                        >
                        <svg class="w-5 h-5 absolute left-4 top-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                    </div>

                    <select
                        name="role"
                        class="px-4 py-3 rounded-xl border-2 border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-all duration-300 whitespace-nowrap"
                    >
                        <option value="">Tous les rôles</option>
                        <option value="admin" @selected($filters['role'] === 'admin')>Administrateurs</option>
                        <option value="user" @selected($filters['role'] === 'user')>Utilisateurs</option>
                    </select>

                    <select
                        name="sort"
                        class="px-4 py-3 rounded-xl border-2 border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-all duration-300 whitespace-nowrap"
                    >
                        <option value="created_at" @selected($filters['sort'] === 'created_at')>Date de création</option>
                        <option value="name" @selected($filters['sort'] === 'name')>Nom</option>
                        <option value="email" @selected($filters['sort'] === 'email')>Email</option>
                        <option value="last_login_at" @selected($filters['sort'] === 'last_login_at')>Dernière connexion</option>
                        <option value="last_upload_at" @selected($filters['sort'] === 'last_upload_at')>Dernier upload</option>
                        <option value="encrypted_files_count" @selected($filters['sort'] === 'encrypted_files_count')>Nombre de fichiers</option>
                    </select>

                    <select
                        name="direction"
                        class="px-4 py-3 rounded-xl border-2 border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-all duration-300 whitespace-nowrap"
                    >
                        <option value="desc" @selected($filters['direction'] === 'desc')>Desc</option>
                        <option value="asc" @selected($filters['direction'] === 'asc')>Asc</option>
                    </select>

                    <button type="submit"
                            class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-xl flex items-center space-x-2 transition-all duration-300 font-semibold hover:scale-105 whitespace-nowrap">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/>
                        </svg>
                        <span>Filtrer</span>
                    </button>
                    <button type="button" id="reset-filters"
                            class="bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 px-6 py-3 rounded-xl flex items-center space-x-2 transition-all duration-300 font-semibold hover:scale-105 whitespace-nowrap">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                        </svg>
                        <span>Réinitialiser</span>
                    </button>
                </form>
            </div>

            <div id="users-table"
                 data-url="{{ route('admin.users.index') }}"
                 class="p-0">
                @include('admin.users.partials.table', ['users' => $users])
            </div>
        </section>
    </div>

    <div id="user-detail-modal" class="fixed inset-0 z-50 hidden items-start justify-center bg-black bg-opacity-50 overflow-y-auto p-2">
        <div class="max-w-4xl w-full max-h-[98vh] bg-white dark:bg-gray-800 rounded-2xl shadow-2xl border border-gray-200 dark:border-gray-700 mt-2 mb-2 mx-auto overflow-hidden flex flex-col">
            <!-- Header avec gradient background amélioré -->
            <div class="bg-gradient-to-r from-emerald-500 via-teal-500 to-cyan-500 sticky top-0 z-10">
                <div class="flex items-center justify-between px-4 py-3">
                    <div>
                        <h3 class="text-base font-bold text-white">Détails utilisateur</h3>
                        <p class="text-sm font-medium text-white mt-0.5" id="user-detail-meta"></p>
                    </div>
                    <button type="button" class="text-white hover:text-gray-200 transition duration-300 p-2 hover:bg-white/20 rounded-xl" data-modal-close>
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
            </div>

            <!-- Contenu avec background subtil amélioré -->
            <div class="px-4 py-3 space-y-3 bg-gradient-to-br from-slate-50 to-gray-50 dark:from-gray-900 dark:to-gray-800 overflow-y-auto flex-1">
                <!-- Section fichiers avec filtres -->
                <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 mt-2">
                    <div class="px-4 py-3 border-b border-gray-200 dark:border-gray-700">
                        <div class="mb-2">
                            <h4 class="text-base font-bold text-gray-900 dark:text-gray-100">Fichiers chiffrés</h4>
                            <span class="text-xs text-gray-500 dark:text-gray-400" id="user-detail-files-count"></span>
                        </div>

                        <!-- Barre de recherche et filtres -->
                        <form id="file-filter-form" class="flex flex-row gap-2 items-center" onsubmit="event.preventDefault();">
                            <div class="relative flex-1 min-w-0">
                                <input
                                    type="text"
                                    id="file-search-input"
                                    placeholder="Rechercher un fichier…"
                                    class="w-full pl-10 pr-3 py-2 rounded-lg border-2 border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-all duration-300 text-sm"
                                >
                                <svg class="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                </svg>
                            </div>

                            <select
                                id="file-algorithm-filter"
                                class="px-3 py-2 rounded-lg border-2 border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-all duration-300 whitespace-nowrap text-sm"
                            >
                                <option value="">Tous les algorithmes</option>
                                <option value="cesar">César</option>
                                <option value="vigenere">Vigenère</option>
                                <option value="xor-text">XOR</option>
                                <option value="substitution">Substitution</option>
                                <option value="reverse">Reverse</option>
                            </select>

                            <select
                                id="file-date-filter"
                                class="px-3 py-2 rounded-lg border-2 border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-all duration-300 whitespace-nowrap text-sm"
                            >
                                <option value="">Toutes les dates</option>
                                <option value="today">Aujourd'hui</option>
                                <option value="week">Cette semaine</option>
                                <option value="month">Ce mois</option>
                            </select>

                            <button type="button" id="reset-file-filters"
                                    class="bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 px-4 py-2 rounded-lg flex items-center space-x-1 transition-all duration-300 font-semibold hover:scale-105 whitespace-nowrap text-sm">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                                </svg>
                                <span>Réinitialiser</span>
                            </button>
                        </form>
                    </div>
                    <div class="p-4">
                        <div id="user-detail-files" class="space-y-2"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div id="user-delete-modal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black bg-opacity-50 px-4">
        <div class="max-w-lg w-full bg-white dark:bg-gray-800 rounded-2xl shadow-2xl border border-gray-200 dark:border-gray-700 overflow-hidden">
            <div class="p-8 space-y-6">
                <div class="flex items-center gap-4">
                    <div class="p-4 rounded-xl bg-red-100 dark:bg-red-900/40">
                        <i class="fas fa-exclamation-triangle text-2xl text-red-600 dark:text-red-300"></i>
                    </div>
                    <div>
                        <h3 class="text-xl font-bold text-gray-900 dark:text-gray-100">Confirmer la suppression</h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Cette opération est irréversible et supprimera les fichiers associés.</p>
                    </div>
                </div>

                <div class="px-4 py-3 rounded-xl bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600">
                    <p class="text-sm text-gray-500 dark:text-gray-400">Utilisateur</p>
                    <p id="user-delete-name" class="text-lg font-semibold text-gray-900 dark:text-gray-100"></p>
                </div>

                <div class="flex gap-3 pt-2">
                    <button type="button" class="flex-1 px-8 py-4 text-lg font-semibold text-gray-700 dark:text-gray-300 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 rounded-2xl transition-all duration-300 hover:scale-105" data-modal-close>
                        Annuler
                    </button>
                    <button type="button" id="confirm-delete"
                            class="flex-1 px-8 py-4 text-lg font-semibold text-white bg-red-600 hover:bg-red-700 rounded-2xl transition-all duration-300 hover:scale-105 hover:shadow-xl">
                        Supprimer définitivement
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        (function () {
            const filterForm = document.getElementById('user-filter-form');
            const tableContainer = document.getElementById('users-table');
            const resetBtn = document.getElementById('reset-filters');
            const detailModal = document.getElementById('user-detail-modal');
            const deleteModal = document.getElementById('user-delete-modal');
            const modalCloseButtons = document.querySelectorAll('[data-modal-close]');
            const userDetailFiles = document.getElementById('user-detail-files');
            const userDetailMeta = document.getElementById('user-detail-meta');
            const userDetailFilesCount = document.getElementById('user-detail-files-count');
            const deleteName = document.getElementById('user-delete-name');
            const confirmDeleteBtn = document.getElementById('confirm-delete');
            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            let currentDeleteUrl = null;
            let currentUserDetailUrl = null;
            let currentParams = new URLSearchParams(new FormData(filterForm));

            function closeModal(modal) {
                modal.classList.add('hidden');
                modal.classList.remove('flex');
            }

            function openModal(modal) {
                modal.classList.remove('hidden');
                modal.classList.add('flex');
            }

            modalCloseButtons.forEach((btn) => {
                btn.addEventListener('click', () => {
                    closeModal(detailModal);
                    closeModal(deleteModal);
                });
            });

            resetBtn.addEventListener('click', () => {
                filterForm.reset();
                currentParams = new URLSearchParams();
                fetchUsers();
            });

            filterForm.addEventListener('submit', (event) => {
                event.preventDefault();
                currentParams = new URLSearchParams(new FormData(filterForm));
                fetchUsers();
            });

            async function fetchUsers() {
                const url = `${tableContainer.dataset.url}?${currentParams.toString()}`;
                const response = await fetch(url, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                });

                if (response.ok) {
                    const html = await response.text();
                    tableContainer.innerHTML = html;
                }
            }

            tableContainer.addEventListener('click', async (event) => {
                const detailButton = event.target.closest('[data-user-detail]');
                const deleteButton = event.target.closest('[data-user-delete]');

                if (detailButton) {
                    event.preventDefault();
                    event.stopPropagation();
                    
                    const url = detailButton.getAttribute('data-user-detail') || detailButton.dataset.userDetail;
                    if (!url) {
                        console.error('URL not found');
                        return;
                    }

                    currentUserDetailUrl = url;

                    try {
                        const response = await fetch(url, {
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest',
                                'Accept': 'application/json',
                            },
                        });

                        if (!response.ok) {
                            throw new Error('Erreur lors du chargement des détails');
                        }

                        const { user, files, files_pagination } = await response.json();
                        
                        // Stocker les fichiers originaux pour le filtrage et la pagination
                        window.currentUserFiles = files || [];
                        window.currentUserFilesPagination = files_pagination || null;
                        window.currentUserId = user.id;
                        
                        if (userDetailFilesCount) {
                            userDetailFilesCount.textContent = `${user.files_count} fichier(s)`;
                        }
                        if (userDetailMeta) {
                            userDetailMeta.textContent = `${user.name} • ${user.email}`;
                        }

                        if (userDetailFiles) {
                            renderFiles(files || [], files_pagination || null);
                        }
                        
                        openModal(detailModal);
                    } catch (error) {
                        console.error('Error loading user details:', error);
                        alert('Erreur lors du chargement des détails de l\'utilisateur');
                    }
                    return;
                }

                if (deleteButton) {
                    currentDeleteUrl = deleteButton.dataset.userDelete;
                    deleteName.textContent = deleteButton.dataset.userName;
                    openModal(deleteModal);
                }
            });

            confirmDeleteBtn.addEventListener('click', async () => {
                if (!currentDeleteUrl) return;

                const response = await fetch(currentDeleteUrl, {
                    method: 'DELETE',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json',
                    },
                });

                if (response.ok) {
                    closeModal(deleteModal);
                    currentDeleteUrl = null;
                    fetchUsers();
                }
            });

            function renderFiles(files, pagination = null) {
                const algorithmStyles = {
                    'cesar': 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200 border border-blue-200',
                    'substitution': 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200 border border-green-200',
                    'vigenere': 'bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-200 border border-purple-200',
                    'reverse': 'bg-orange-100 text-orange-800 dark:bg-orange-900 dark:text-orange-200 border border-orange-200',
                    'xor-text': 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200 border border-red-200',
                };

                if (files.length === 0) {
                    userDetailFiles.innerHTML = `<div class="text-center py-12">
                        <div class="text-7xl mb-6">🔭</div>
                        <h3 class="text-2xl font-bold text-gray-900 dark:text-gray-100 mb-4">
                            Aucun fichier trouvé
                        </h3>
                        <p class="text-gray-500 dark:text-gray-400 mb-8 max-w-md mx-auto text-lg">
                            Cet utilisateur n'a pas encore uploadé de fichiers.
                        </p>
                    </div>`;
                    return;
                }

                let paginationHTML = '';
                if (pagination && pagination.last_page > 1) {
                    const currentPage = pagination.current_page;
                    const lastPage = pagination.last_page;
                    const from = pagination.from || 0;
                    const to = pagination.to || 0;
                    const total = pagination.total || 0;

                    // Previous button
                    let prevButton = '';
                    if (currentPage > 1) {
                        prevButton = `<button onclick="loadFilesPage(${currentPage - 1})" class="px-4 py-2 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-semibold transition-all duration-300 hover:scale-105">Précédent</button>`;
                    } else {
                        prevButton = `<button disabled class="px-4 py-2 rounded-xl bg-gray-300 dark:bg-gray-600 text-gray-500 dark:text-gray-400 font-semibold cursor-not-allowed">Précédent</button>`;
                    }

                    // Page numbers
                    let pageNumbers = '';
                    for (let i = 1; i <= lastPage; i++) {
                        if (i === 1 || i === lastPage || (i >= currentPage - 1 && i <= currentPage + 1)) {
                            if (i === currentPage) {
                                pageNumbers += `<button class="px-4 py-2 rounded-xl bg-blue-600 text-white font-semibold">${i}</button>`;
                            } else {
                                pageNumbers += `<button onclick="loadFilesPage(${i})" class="px-4 py-2 rounded-xl bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 font-semibold transition-all duration-300 hover:scale-105">${i}</button>`;
                            }
                        } else if (i === currentPage - 2 || i === currentPage + 2) {
                            pageNumbers += `<span class="px-2 text-gray-500 dark:text-gray-400">...</span>`;
                        }
                    }

                    // Next button
                    let nextButton = '';
                    if (currentPage < lastPage) {
                        nextButton = `<button onclick="loadFilesPage(${currentPage + 1})" class="px-4 py-2 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-semibold transition-all duration-300 hover:scale-105">Suivant</button>`;
                    } else {
                        nextButton = `<button disabled class="px-4 py-2 rounded-xl bg-gray-300 dark:bg-gray-600 text-gray-500 dark:text-gray-400 font-semibold cursor-not-allowed">Suivant</button>`;
                    }

                    paginationHTML = `
                        <div class="mt-6 pt-6 border-t border-gray-200 dark:border-gray-700">
                            <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
                                <div class="text-sm text-gray-600 dark:text-gray-400">
                                    Affichage de ${from} à ${to} sur ${total} fichier(s)
                                </div>
                                <div class="flex items-center gap-2 flex-wrap justify-center">
                                    ${prevButton}
                                    ${pageNumbers}
                                    ${nextButton}
                                </div>
                            </div>
                        </div>
                    `;
                }

                userDetailFiles.innerHTML = `
                    <div class="hidden lg:flex items-center px-6 py-4 text-sm font-semibold text-gray-600 dark:text-gray-400 border-b border-gray-200 dark:border-gray-700 mb-6">
                        <div class="flex-1">Nom du fichier</div>
                        <div class="flex items-center justify-end space-x-12 w-[580px]">
                            <div class="w-32 text-center">Algorithme</div>
                            <div class="w-24 text-center">Taille</div>
                            <div class="w-32 text-center">Date</div>
                            <div class="w-48 text-center">Actions</div>
                        </div>
                    </div>
                    <div class="space-y-4">
                        ${files.map(file => {
                            const style = algorithmStyles[file.method] || 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300 border border-gray-200';
                            return `
                                <div class="file-row group flex flex-col lg:flex-row items-center bg-white/90 dark:bg-gray-800/80 backdrop-blur-xl rounded-2xl border border-gray-200 dark:border-gray-700 p-5 hover:border-blue-400 hover:shadow-xl transition-all duration-300">
                                    <div class="flex-1 min-w-0 w-full lg:w-auto mb-4 lg:mb-0 flex items-center gap-3">
                                        <span class="text-3xl flex-shrink-0">${file.icon || '📄'}</span>
                                        <div class="min-w-0 flex-1">
                                            <h4 class="font-bold text-gray-900 dark:text-white truncate text-lg">
                                                ${file.name}
                                            </h4>
                                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                                ${file.algorithm}
                                            </p>
                                        </div>
                                    </div>
                                    <div class="flex items-center justify-end w-full lg:w-[580px] flex-shrink-0 space-x-6">
                                        <div class="w-32 text-center">
                                            <span class="inline-flex items-center px-3 py-1.5 rounded-full text-sm font-medium ${style}">
                                                ${file.algorithm}
                                            </span>
                                        </div>
                                        <div class="w-24 text-center text-gray-600 dark:text-gray-300 text-sm font-medium">
                                            ${file.size}
                                        </div>
                                        <div class="w-32 text-center text-gray-600 dark:text-gray-300 text-sm font-medium">
                                            ${file.created_at ?? '—'}
                                        </div>
                                        <div class="w-48 flex justify-center items-center gap-2">
                                            <a href="/admin/files/${file.id}/download" 
                                               class="bg-blue-600 hover:bg-blue-700 text-white py-2 px-3 rounded-xl text-xs font-semibold transition-all duration-300 hover:scale-105 flex items-center gap-1 whitespace-nowrap">
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                                </svg>
                                                Télécharger
                                            </a>
                                            <button type="button" 
                                                    class="bg-red-600 hover:bg-red-700 text-white py-2 px-3 rounded-xl text-xs font-semibold transition-all duration-300 hover:scale-105 flex items-center gap-1 whitespace-nowrap"
                                                    onclick="deleteFile(${file.id}, '${file.name.replace(/'/g, "\\'")}')">
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                </svg>
                                                Supprimer
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            `;
                        }).join('')}
                    </div>
                    ${paginationHTML}
                `;
            }

            async function loadFilesPage(page) {
                // Utiliser la fonction de chargement avec filtres pour préserver les filtres actifs
                await loadFilesWithFilters(page);
            }

            window.loadFilesPage = loadFilesPage;

            // Recherche et filtrage côté serveur
            const fileSearchInput = document.getElementById('file-search-input');
            const fileAlgorithmFilter = document.getElementById('file-algorithm-filter');
            const fileDateFilter = document.getElementById('file-date-filter');
            const resetFileFiltersBtn = document.getElementById('reset-file-filters');
            let searchTimeout = null;

            async function loadFilesWithFilters(page = 1) {
                if (!window.currentUserId || !currentUserDetailUrl) return;

                const searchTerm = fileSearchInput?.value.trim() || '';
                const algorithmFilter = fileAlgorithmFilter?.value || '';
                const dateFilter = fileDateFilter?.value || '';

                const params = new URLSearchParams();
                params.set('files_page', page);
                if (searchTerm) params.set('files_search', searchTerm);
                if (algorithmFilter) params.set('files_algorithm', algorithmFilter);
                if (dateFilter) params.set('files_date_filter', dateFilter);

                const url = `${currentUserDetailUrl}?${params.toString()}`;

                try {
                    const response = await fetch(url, {
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json',
                        },
                    });

                    if (!response.ok) {
                        throw new Error('Erreur lors du chargement des fichiers');
                    }

                    const { files, files_pagination } = await response.json();
                    window.currentUserFiles = files || [];
                    window.currentUserFilesPagination = files_pagination || null;
                    renderFiles(files, files_pagination);
                    
                    // Mettre à jour le compteur de fichiers
                    if (userDetailFilesCount && files_pagination) {
                        userDetailFilesCount.textContent = `${files_pagination.total} fichier(s)`;
                    }
                } catch (error) {
                    console.error('Error loading files:', error);
                    alert('Erreur lors du chargement des fichiers');
                }
            }

            // Debounce pour la recherche (attendre 500ms après la dernière frappe)
            fileSearchInput?.addEventListener('input', () => {
                clearTimeout(searchTimeout);
                searchTimeout = setTimeout(() => {
                    loadFilesWithFilters(1);
                }, 500);
            });

            fileAlgorithmFilter?.addEventListener('change', () => {
                loadFilesWithFilters(1);
            });

            fileDateFilter?.addEventListener('change', () => {
                loadFilesWithFilters(1);
            });

            resetFileFiltersBtn?.addEventListener('click', () => {
                if (fileSearchInput) fileSearchInput.value = '';
                if (fileAlgorithmFilter) fileAlgorithmFilter.value = '';
                if (fileDateFilter) fileDateFilter.value = '';
                loadFilesWithFilters(1);
            });

            async function deleteFile(fileId, fileName) {
                if (!confirm(`Êtes-vous sûr de vouloir supprimer le fichier "${fileName}" ?`)) {
                    return;
                }

                const response = await fetch(`/admin/files/${fileId}`, {
                    method: 'DELETE',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json',
                    },
                });

                if (response.ok) {
                    // Recharger les fichiers avec les filtres actuels
                    const currentPage = window.currentUserFilesPagination?.current_page || 1;
                    await loadFilesWithFilters(currentPage);
                }
            }

            window.deleteFile = deleteFile;
        })();
    </script>
</x-app-layout>

