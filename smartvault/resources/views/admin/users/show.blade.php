<x-app-layout>
    <div class="space-y-8">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <a href="{{ route('admin.users.index') }}" class="inline-flex items-center gap-2 text-sm font-semibold text-indigo-600 hover:text-indigo-800 transition">
                <i class="fas fa-chevron-left text-xs"></i> Retour à la liste des utilisateurs
            </a>
            @if (session('error'))
                <div class="rounded-lg bg-red-50 border border-red-200 text-red-700 px-4 py-3 shadow-sm">
                    {{ session('error') }}
                </div>
            @endif
        </div>

        <section class="rounded-3xl bg-gradient-to-r from-indigo-500 via-purple-500 to-pink-500 text-white shadow-xl">
            <div class="px-8 py-8">
                <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6">
                    <div class="flex items-center gap-4">
                        <div class="flex h-16 w-16 items-center justify-center rounded-full bg-white/20 text-white text-2xl font-semibold">
                            {{ strtoupper(substr($managedUser->name, 0, 1)) }}
                        </div>
                        <div>
                            <p class="text-sm uppercase tracking-[0.3em] font-semibold text-white/70">Profil Utilisateur</p>
                            <h1 class="mt-1 text-3xl md:text-4xl font-bold">{{ $managedUser->name }}</h1>
                            <p class="mt-2 text-white/80">{{ $managedUser->email }}</p>
                        </div>
                    </div>
                    <span class="inline-flex items-center gap-2 px-4 py-2 rounded-xl border border-white/25 bg-white/10 text-sm font-semibold {{ $managedUser->isAdmin() ? 'text-yellow-200' : 'text-white' }}">
                        <i class="fas {{ $managedUser->isAdmin() ? 'fa-shield-alt' : 'fa-user' }} text-xs"></i>
                        {{ $managedUser->isAdmin() ? 'Administrateur' : 'Membre standard' }}
                    </span>
                </div>

                <div class="mt-8 grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
                    <div class="rounded-2xl bg-white/15 backdrop-blur px-5 py-4 shadow-lg">
                        <p class="text-sm text-white/70">Création du compte</p>
                        <p class="mt-2 text-xl font-semibold">{{ $managedUser->created_at->translatedFormat('d F Y') }}</p>
                        <p class="mt-1 text-xs text-white/70">À {{ $managedUser->created_at->format('H:i') }}</p>
                    </div>
                    <div class="rounded-2xl bg-white/15 backdrop-blur px-5 py-4 shadow-lg">
                        <p class="text-sm text-white/70">Dernière mise à jour</p>
                        <p class="mt-2 text-xl font-semibold">{{ $managedUser->updated_at->translatedFormat('d F Y') }}</p>
                        <p class="mt-1 text-xs text-white/70">{{ $managedUser->updated_at->diffForHumans() }}</p>
                    </div>
                    <div class="rounded-2xl bg-white/15 backdrop-blur px-5 py-4 shadow-lg">
                        <p class="text-sm text-white/70">Dernier accès</p>
                        <p class="mt-2 text-xl font-semibold">
                            {{ $lastAccessAt ? $lastAccessAt->diffForHumans() : 'Non disponible' }}
                        </p>
                        <p class="mt-1 text-xs text-white/70">Historique de session</p>
                    </div>
                    <div class="rounded-2xl bg-white/15 backdrop-blur px-5 py-4 shadow-lg">
                        <p class="text-sm text-white/70">Dernier upload</p>
                        <p class="mt-2 text-xl font-semibold">
                            {{ $managedUser->last_upload_at ? $managedUser->last_upload_at->diffForHumans() : 'Aucun upload' }}
                        </p>
                        <p class="mt-1 text-xs text-white/70">Activité fichiers</p>
                    </div>
                </div>
            </div>
        </section>

        <div class="grid gap-8 xl:grid-cols-3">
            <div class="xl:col-span-2 space-y-8">
                <div class="bg-white dark:bg-gray-900 shadow-2xl shadow-indigo-100/40 dark:shadow-none border border-gray-100 dark:border-gray-800 rounded-3xl p-8 space-y-6">
                    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                        <div>
                            <h2 class="text-xl font-semibold text-gray-900 dark:text-gray-100">Résumé du compte</h2>
                            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                                Statistiques principales liées à l’utilisation de SmartDataVault par cet utilisateur.
                            </p>
                        </div>
                        <span class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-indigo-50 text-indigo-600 text-sm font-semibold dark:bg-indigo-500/10 dark:text-indigo-300">
                            <i class="fas fa-database text-xs"></i> Stockage utilisé : {{ $totalStorage }}
                        </span>
                    </div>

                    <dl class="grid gap-4 sm:grid-cols-2">
                        <div class="rounded-2xl border border-gray-100 dark:border-gray-800 p-5">
                            <dt class="text-sm text-gray-500 dark:text-gray-400">Fichiers chiffrés</dt>
                            <dd class="mt-2 text-2xl font-semibold text-gray-900 dark:text-gray-100">{{ $managedUser->encrypted_files_count }}</dd>
                            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Documents stockés par cet utilisateur.</p>
                        </div>
                        <div class="rounded-2xl border border-gray-100 dark:border-gray-800 p-5">
                            <dt class="text-sm text-gray-500 dark:text-gray-400">Dernier fichier modifié</dt>
                            <dd class="mt-2 text-sm font-semibold text-gray-900 dark:text-gray-100">
                                {{ $lastFile?->original_name ?? 'Aucun fichier' }}
                            </dd>
                            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                                @if($lastFile)
                                    Mis à jour le {{ $lastFile->updated_at->translatedFormat('d M Y H:i') }}
                                @else
                                    Pas encore de fichiers uploadés.
                                @endif
                            </p>
                        </div>
                    </dl>
                </div>

                <div class="bg-white dark:bg-gray-900 shadow-2xl shadow-indigo-100/40 dark:shadow-none border border-gray-100 dark:border-gray-800 rounded-3xl overflow-hidden">
                    <div class="px-6 md:px-8 py-6 border-b border-gray-100 dark:border-gray-800 flex items-center justify-between">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Fichiers chiffrés</h3>
                        <span class="inline-flex items-center gap-2 px-3 py-1 rounded-xl bg-indigo-50 text-indigo-600 text-xs font-semibold dark:bg-indigo-500/10 dark:text-indigo-300">
                            <i class="fas fa-file-lock text-xs"></i> {{ $managedUser->encrypted_files_count }} fichiers
                        </span>
                    </div>
                    <div class="divide-y divide-gray-100 dark:divide-gray-800">
                        @forelse ($files as $file)
                            <div class="px-6 md:px-8 py-4 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                                <div>
                                    <p class="text-sm font-semibold text-gray-900 dark:text-gray-100">{{ $file->original_name }}</p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                        Uploadé le {{ $file->created_at->translatedFormat('d M Y H:i') }} • Algorithme : {{ $file->algorithm_name }}
                                    </p>
                                </div>
                                <div class="text-sm text-gray-500 dark:text-gray-400">
                                    Taille : {{ $file->formatted_size }}
                                </div>
                            </div>
                        @empty
                            <div class="px-6 md:px-8 py-10 text-center text-sm text-gray-500 dark:text-gray-400">
                                Aucun fichier chiffré pour ce compte.
                            </div>
                        @endforelse
                    </div>
                    @if($files->hasPages())
                        <div class="px-6 md:px-8 py-4 border-t border-gray-100 dark:border-gray-800">
                            {{ $files->links() }}
                        </div>
                    @endif
                </div>
            </div>

            <div class="space-y-8">
                <div class="bg-white dark:bg-gray-900 shadow-2xl shadow-indigo-100/40 dark:shadow-none border border-gray-100 dark:border-gray-800 rounded-3xl p-6 space-y-4">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Actions rapides</h3>
                    @if(!$managedUser->isAdmin())
                        <p class="text-sm text-gray-500 dark:text-gray-400">
                            Supprimez le compte et tous ses fichiers chiffrés associés. Action définitive.
                        </p>
                        <form action="{{ route('admin.users.destroy', $managedUser) }}" method="POST" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer ce compte ? Cette action est irréversible.');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="w-full inline-flex justify-center items-center gap-2 px-4 py-2 bg-red-600 border border-transparent rounded-xl font-semibold text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                                <i class="fas fa-user-slash text-xs"></i> Supprimer le compte
                            </button>
                        </form>
                    @else
                        <div class="rounded-xl bg-gray-50 dark:bg-gray-800/70 px-4 py-3 text-sm text-gray-600 dark:text-gray-300">
                            <i class="fas fa-info-circle text-indigo-500 mr-2"></i>
                            Les comptes administrateurs sont protégés contre la suppression pour éviter toute perte d’accès.
                        </div>
                    @endif
                </div>

                @if ($lastFile)
                    <div class="bg-white dark:bg-gray-900 shadow-2xl shadow-indigo-100/40 dark:shadow-none border border-gray-100 dark:border-gray-800 rounded-3xl p-6 space-y-3">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Dernier fichier modifié</h3>
                        <p class="text-sm font-semibold text-gray-900 dark:text-gray-100">{{ $lastFile->original_name }}</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400">
                            Mis à jour le {{ $lastFile->updated_at->translatedFormat('d M Y H:i') }}<br>
                            Algorithme : {{ $lastFile->algorithm_name }}
                        </p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
<x-app-layout>
    <div class="space-y-6">
        <div>
            <a href="{{ route('admin.users.index') }}" class="inline-flex items-center text-sm text-indigo-600 hover:text-indigo-800">
                ← Retour à la liste des utilisateurs
            </a>
        </div>

        @if (session('error'))
            <div class="rounded-md bg-red-50 border border-red-200 text-red-700 px-4 py-3">
                {{ session('error') }}
            </div>
        @endif

        <div class="grid gap-6 md:grid-cols-3">
            <div class="md:col-span-2 space-y-6">
                <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6 space-y-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <h2 class="text-2xl font-semibold text-gray-900 dark:text-gray-100">{{ $managedUser->name }}</h2>
                            <p class="text-sm text-gray-500 dark:text-gray-400">{{ $managedUser->email }}</p>
                        </div>
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium {{ $managedUser->role === 'admin' ? 'bg-purple-100 text-purple-800' : 'bg-blue-100 text-blue-800' }}">
                            {{ ucfirst($managedUser->role) }}
                        </span>
                    </div>

                    <div class="grid md:grid-cols-2 gap-6 text-sm">
                        <div class="space-y-2">
                            <div>
                                <span class="text-gray-500 dark:text-gray-400">Date de création</span>
                                <p class="font-medium text-gray-900 dark:text-gray-100">{{ $managedUser->created_at->translatedFormat('d F Y H:i') }}</p>
                            </div>
                            <div>
                                <span class="text-gray-500 dark:text-gray-400">Dernière mise à jour</span>
                                <p class="font-medium text-gray-900 dark:text-gray-100">{{ $managedUser->updated_at->translatedFormat('d F Y H:i') }}</p>
                            </div>
                            <div>
                                <span class="text-gray-500 dark:text-gray-400">Dernier accès</span>
                                <p class="font-medium text-gray-900 dark:text-gray-100">
                                    {{ $lastAccessAt ? $lastAccessAt->diffForHumans() : 'Aucun historique disponible' }}
                                </p>
                            </div>
                        </div>

                        <div class="space-y-2">
                            <div>
                                <span class="text-gray-500 dark:text-gray-400">Fichiers chiffrés</span>
                                <p class="font-medium text-gray-900 dark:text-gray-100">{{ $managedUser->encrypted_files_count }}</p>
                            </div>
                            <div>
                                <span class="text-gray-500 dark:text-gray-400">Stockage total utilisé</span>
                                <p class="font-medium text-gray-900 dark:text-gray-100">{{ $totalStorage }}</p>
                            </div>
                            <div>
                                <span class="text-gray-500 dark:text-gray-400">Dernier upload</span>
                                <p class="font-medium text-gray-900 dark:text-gray-100">
                                    {{ $managedUser->last_upload_at ? $managedUser->last_upload_at->diffForHumans() : 'Aucun upload enregistré' }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white dark:bg-gray-800 shadow rounded-lg">
                    <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 flex items-center justify-between">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Fichiers chiffrés</h3>
                        <span class="text-sm text-gray-500 dark:text-gray-400">{{ $managedUser->encrypted_files_count }} fichiers</span>
                    </div>
                    <div class="divide-y divide-gray-200 dark:divide-gray-700">
                        @forelse ($managedUser->encryptedFiles as $file)
                            <div class="px-6 py-4 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                                <div>
                                    <p class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ $file->original_name }}</p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">
                                        {{ $file->created_at->translatedFormat('d M Y H:i') }} • Algorithme : {{ $file->algorithm_name }}
                                    </p>
                                </div>
                                <div class="text-sm text-gray-500 dark:text-gray-400">
                                    Taille : {{ $file->formatted_size }}
                                </div>
                            </div>
                        @empty
                            <div class="px-6 py-8 text-center text-sm text-gray-500 dark:text-gray-400">
                                Aucun fichier chiffré pour ce compte.
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>

            <div class="space-y-6">
                <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6 space-y-3">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Actions rapides</h3>
                    @if(!$managedUser->isAdmin())
                        <form action="{{ route('admin.users.destroy', $managedUser) }}" method="POST" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer ce compte ? Cette action est irréversible.');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="w-full inline-flex justify-center items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                                Supprimer le compte
                            </button>
                        </form>
                    @else
                        <p class="text-sm text-gray-500 dark:text-gray-400">
                            Cette action est désactivée pour les comptes administrateurs.
                        </p>
                    @endif
                </div>

                @if ($lastFile)
                    <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6 space-y-3">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Dernier fichier modifié</h3>
                        <p class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ $lastFile->original_name }}</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400">
                            Mis à jour le {{ $lastFile->updated_at->translatedFormat('d M Y H:i') }}<br>
                            Algorithme : {{ $lastFile->algorithm_name }}
                        </p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>

