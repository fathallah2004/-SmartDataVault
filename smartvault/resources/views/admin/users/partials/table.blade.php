<div class="p-8 space-y-4">
    <div class="hidden lg:flex items-center px-6 py-4 text-sm font-semibold text-gray-600 dark:text-gray-400 border-b border-gray-200 dark:border-gray-700 mb-6">
        <div class="flex-1">Email</div>
        <div class="w-32 text-center">Rôle</div>
        <div class="w-40 text-center">Créé le</div>
        <div class="w-40 text-center">Dernière connexion</div>
        <div class="w-40 text-center">Dernier upload</div>
        <div class="w-32 text-center">Fichiers</div>
        <div class="w-48 text-center">Actions</div>
    </div>

    <div class="space-y-4">
        @forelse ($users as $user)
            <div class="file-row group flex flex-col lg:flex-row items-center bg-white/90 dark:bg-gray-800/80 backdrop-blur-xl rounded-2xl border border-gray-200 dark:border-gray-700 p-5 hover:border-blue-400 hover:shadow-xl transition-all duration-300">
                <div class="flex-1 min-w-0 w-full lg:w-auto mb-4 lg:mb-0">
                    <p class="font-semibold text-gray-900 dark:text-white truncate text-base">
                        {{ $user->email }}
                    </p>
                </div>

                <div class="w-32 text-center mb-4 lg:mb-0">
                    <span class="inline-flex items-center px-3 py-1.5 rounded-full text-sm font-medium {{ $user->role === 'admin' ? 'bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-200 border border-purple-200' : 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200 border border-blue-200' }}">
                        <i class="fas {{ $user->role === 'admin' ? 'fa-crown' : 'fa-user' }} text-xs mr-1"></i>
                        {{ ucfirst($user->role) }}
                    </span>
                </div>

                <div class="w-40 text-center text-gray-600 dark:text-gray-300 text-sm mb-4 lg:mb-0">
                    {{ optional($user->created_at)->format('d/m/Y') ?? '—' }}
                </div>

                <div class="w-40 text-center text-gray-600 dark:text-gray-300 text-sm mb-4 lg:mb-0">
                    {{ optional($user->last_login_at)->format('d/m/Y') ?? '—' }}
                </div>

                <div class="w-40 text-center text-gray-600 dark:text-gray-300 text-sm mb-4 lg:mb-0">
                    {{ optional($user->last_upload_at)->format('d/m/Y') ?? '—' }}
                </div>

                <div class="w-32 text-center mb-4 lg:mb-0">
                    <div class="font-semibold text-gray-900 dark:text-white">{{ $user->encrypted_files_count }}</div>
                    <p class="text-xs text-gray-500 dark:text-gray-400">
                        {{ $user->formatted_storage }}
                    </p>
                </div>

                <div class="w-48 flex justify-center items-center gap-2">
                    <button type="button"
                            class="bg-blue-600 hover:bg-blue-700 text-white py-2 px-3 rounded-xl text-xs font-semibold transition-all duration-300 hover:scale-105 flex items-center gap-1 whitespace-nowrap"
                            data-user-detail="{{ route('admin.users.show', $user) }}">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                        </svg>
                        Détails
                    </button>

                    @if(!$user->isAdmin())
                        <button type="button"
                                class="bg-red-600 hover:bg-red-700 text-white py-2 px-3 rounded-xl text-xs font-semibold transition-all duration-300 hover:scale-105 flex items-center gap-1 whitespace-nowrap"
                                data-user-delete="{{ route('admin.users.destroy', $user) }}"
                                data-user-name="{{ $user->name }}">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                            </svg>
                            Supprimer
                        </button>
                    @else
                        <span class="bg-gray-100 dark:bg-gray-700 text-gray-400 dark:text-gray-500 py-2 px-3 rounded-xl text-xs font-semibold cursor-not-allowed flex items-center gap-1 whitespace-nowrap">
                            <i class="fas fa-lock text-xs"></i>
                            Protégé
                        </span>
                    @endif
                </div>
            </div>
        @empty
            <div class="text-center py-12">
                <div class="text-7xl mb-6">🔭</div>
                <h3 class="text-2xl font-bold text-gray-900 dark:text-gray-100 mb-4">
                    Aucun utilisateur trouvé
                </h3>
                <p class="text-gray-500 dark:text-gray-400 mb-8 max-w-md mx-auto text-lg">
                    Aucun utilisateur ne correspond à vos critères de recherche.
                </p>
            </div>
        @endforelse
    </div>

    @if($users->hasPages())
        <div class="mt-8 px-4">
            <div class="flex items-center justify-between text-sm text-gray-500 dark:text-gray-400">
                <div>
                    {{ $users->firstItem() ?? 0 }} – {{ $users->lastItem() ?? 0 }} sur {{ $users->total() }} utilisateur(s)
                </div>
                <div>
                    {{ $users->links() }}
                </div>
            </div>
        </div>
    @endif
</div>

