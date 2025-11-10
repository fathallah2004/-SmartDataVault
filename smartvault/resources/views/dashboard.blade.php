<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SmartDataVault</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .w-80 { width: 20rem; }
        
        @keyframes slideInRight {
            from {
                transform: translateX(400px);
                opacity: 0;
            }
            to {
                transform: translateX(0);
                opacity: 1;
            }
        }

        @keyframes slideOutRight {
            from {
                transform: translateX(0);
                opacity: 1;
            }
            to {
                transform: translateX(400px);
                opacity: 0;
            }
        }

        .toast-enter {
            animation: slideInRight 0.4s cubic-bezier(0.68, -0.55, 0.265, 1.55);
        }

        .toast-exit {
            animation: slideOutRight 0.3s ease-in;
        }
    </style>
</head>
<body class="bg-gray-100 dark:bg-gray-900">
    
    <!-- ✅ Container pour les notifications toast -->
    <div id="toastContainer" class="fixed top-4 right-4 z-50 space-y-3 max-w-md">
        <!-- Les notifications seront ajoutées ici -->
    </div>

    <!-- ✅ Notifications Laravel converties en toast -->
    @if(session('success'))
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                showToast('success', {!! json_encode(session('success')) !!});
            });
        </script>
    @endif

    @if(session('delete'))
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                showToast('delete', {!! json_encode(session('delete')) !!});
            });
        </script>
    @endif

    @if(session('error'))
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                showToast('error', {!! json_encode(session('error')) !!});
            });
        </script>
    @endif

    <div class="flex min-h-screen">
        <!-- ===== SIDEBAR ===== -->
        <div class="w-80 flex-shrink-0">
            @include('components.sidebar')
        </div>

        <!-- ===== CONTENU PRINCIPAL ===== -->
        <div class="flex-1 min-h-screen bg-gray-100 dark:bg-gray-900">
            <!-- Header -->
            <header class="bg-white dark:bg-gray-800 shadow rounded-tl-2xl">
                <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                    <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                        🔒 SmartDataVault - Mon Espace Sécurisé
                    </h2>
                </div>
            </header>

            <!-- Main Content -->
            <main class="py-6">
                <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                    <!-- Section unifiée Mes fichiers -->
                    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg border border-gray-200 dark:border-gray-700">
                        <!-- Header avec boutons -->
                        <div class="px-8 py-6 border-b border-gray-200 dark:border-gray-700">
                            <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-4">
                                <div>
                                    <h3 class="text-xl font-bold text-gray-900 dark:text-gray-100">
                                        📁 Mes fichiers
                                    </h3>
                                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                                        {{ $files->total() }} fichier(s) sécurisé(s)
                                    </p>
                                </div>
                                
                                <!-- Barre d'actions -->
                                <div class="flex flex-wrap items-center gap-3">
                                    <!-- Bouton Recherche -->
                                    <button onclick="toggleSearch()" 
                                            class="bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 px-4 py-3 rounded-xl flex items-center space-x-2 transition-all duration-300 font-semibold hover:scale-105">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                        </svg>
                                        <span>Rechercher</span>
                                    </button>
                                    
                                    <!-- Bouton Filtre -->
                                    <button onclick="toggleFilter()" 
                                            class="bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 px-4 py-3 rounded-xl flex items-center space-x-2 transition-all duration-300 font-semibold hover:scale-105">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/>
                                        </svg>
                                        <span>Filtrer</span>
                                    </button>
                                    
                                    <!-- Bouton Ajouter -->
                                    <button onclick="openUploadModal()" 
                                            class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-xl flex items-center space-x-3 transition-all duration-300 font-semibold hover:scale-105 hover:shadow-lg">
                                        <span class="text-xl">+</span>
                                        <span>Ajouter un fichier</span>
                                    </button>
                                </div>
                            </div>
                            
                            <!-- Zone de recherche (cachée par défaut) -->
                            <div id="searchBox" class="mt-4 {{ request('search') ? '' : 'hidden' }}">
                                <form action="{{ route('dashboard') }}" method="GET" class="flex gap-3">
                                    @if(request('algorithm'))
                                        <input type="hidden" name="algorithm" value="{{ request('algorithm') }}">
                                    @endif
                                    @if(request('date_filter'))
                                        <input type="hidden" name="date_filter" value="{{ request('date_filter') }}">
                                    @endif
                                    
                                    <div class="relative flex-1">
                                        <input type="text" 
                                               id="searchInput"
                                               name="search"
                                               value="{{ request('search') }}"
                                               placeholder="Rechercher un fichier par nom..."
                                               class="w-full pl-12 pr-4 py-3 rounded-xl border-2 border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-all duration-300">
                                        <svg class="w-5 h-5 absolute left-4 top-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                        </svg>
                                    </div>
                                    
                                    <button type="submit" 
                                            class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-xl flex items-center space-x-2 transition-all duration-300 font-semibold hover:scale-105 whitespace-nowrap">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                        </svg>
                                        <span>Rechercher</span>
                                    </button>
                                    
                                    @if(request('search'))
                                    <a href="{{ route('dashboard') }}{{ request('algorithm') || request('date_filter') ? '?' . http_build_query(request()->except('search')) : '' }}" 
                                       class="bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 px-6 py-3 rounded-xl flex items-center space-x-2 transition-all duration-300 font-semibold hover:scale-105 whitespace-nowrap">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                        </svg>
                                        <span>Effacer</span>
                                    </a>
                                    @endif
                                </form>
                            </div>
                            
                            <!-- Zone de filtres (cachée par défaut) -->
                            <div id="filterBox" class="mt-4 {{ request('algorithm') || request('date_filter') ? '' : 'hidden' }}">
                                <form action="{{ route('dashboard') }}" method="GET">
                                    @if(request('search'))
                                        <input type="hidden" name="search" value="{{ request('search') }}">
                                    @endif
                                    
                                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                        <select name="algorithm" 
                                                id="filterAlgorithm"
                                                class="px-4 py-3 rounded-xl border-2 border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-all duration-300">
                                            <option value="">Tous les algorithmes</option>
                                            <option value="cesar" {{ request('algorithm') == 'cesar' ? 'selected' : '' }}>César</option>
                                            <option value="vigenere" {{ request('algorithm') == 'vigenere' ? 'selected' : '' }}>Vigenère</option>
                                            <option value="xor-text" {{ request('algorithm') == 'xor-text' ? 'selected' : '' }}>XOR</option>
                                            <option value="substitution" {{ request('algorithm') == 'substitution' ? 'selected' : '' }}>Substitution</option>
                                            <option value="reverse" {{ request('algorithm') == 'reverse' ? 'selected' : '' }}>Reverse</option>
                                        </select>
                                        
                                        <select name="date_filter" 
                                                id="filterDate"
                                                class="px-4 py-3 rounded-xl border-2 border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-all duration-300">
                                            <option value="">Toutes les dates</option>
                                            <option value="today" {{ request('date_filter') == 'today' ? 'selected' : '' }}>Aujourd'hui</option>
                                            <option value="week" {{ request('date_filter') == 'week' ? 'selected' : '' }}>Cette semaine</option>
                                            <option value="month" {{ request('date_filter') == 'month' ? 'selected' : '' }}>Ce mois</option>
                                        </select>
                                        
                                        <button type="submit"
                                                class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-3 rounded-xl flex items-center justify-center space-x-2 transition-all duration-300 font-semibold hover:scale-105">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/>
                                            </svg>
                                            <span>Appliquer</span>
                                        </button>
                                    </div>
                                    
                                    @if(request('algorithm') || request('date_filter'))
                                    <div class="mt-3 flex justify-end">
                                        <a href="{{ route('dashboard') }}{{ request('search') ? '?search=' . request('search') : '' }}"
                                           class="bg-red-100 dark:bg-red-900 hover:bg-red-200 dark:hover:bg-red-800 text-red-700 dark:text-red-300 px-4 py-2 rounded-xl flex items-center space-x-2 transition-all duration-300 font-semibold">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                                            </svg>
                                            <span>Réinitialiser les filtres</span>
                                        </a>
                                    </div>
                                    @endif
                                </form>
                            </div>
                        </div>

                        <!-- Contenu unifié -->
                        <div class="p-8">
                            @if($files->count() > 0)
                                @php
                                $algorithmStyles = [
                                    'cesar' => 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200 border border-blue-200',
                                    'substitution' => 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200 border border-green-200',
                                    'vigenere' => 'bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-200 border border-purple-200',
                                    'reverse' => 'bg-orange-100 text-orange-800 dark:bg-orange-900 dark:text-orange-200 border border-orange-200',
                                    'xor-text' => 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200 border border-red-200',
                                ];
                                @endphp

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
                                    @foreach($files as $file)
                                    <div class="file-row group flex flex-col lg:flex-row items-center bg-white/90 dark:bg-gray-800/80 backdrop-blur-xl rounded-2xl border border-gray-200 dark:border-gray-700 p-5 hover:border-blue-400 hover:shadow-xl transition-all duration-300">
                                        
                                        <div class="flex-1 min-w-0 w-full lg:w-auto mb-4 lg:mb-0 flex items-center gap-3">
                                            <span class="text-3xl flex-shrink-0">{{ $file->file_icon }}</span>
                                            <div class="min-w-0 flex-1">
                                                <h4 class="font-bold text-gray-900 dark:text-white truncate text-lg">
                                                    {{ $file->original_name }}
                                                </h4>
                                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                                    {{ $file->algorithm_name }}
                                                </p>
                                            </div>
                                        </div>

                                        <div class="flex items-center justify-end w-full lg:w-[580px] flex-shrink-0 space-x-6">
                                            <div class="w-32 text-center">
                                                <span class="inline-flex items-center px-3 py-1.5 rounded-full text-sm font-medium {{ $algorithmStyles[$file->encryption_method] ?? 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300 border border-gray-200' }}">
                                                    {{ $file->algorithm_name }}
                                                </span>
                                            </div>

                                            <div class="w-24 text-center text-gray-600 dark:text-gray-300 text-sm font-medium">
                                                {{ $file->formatted_size }}
                                            </div>

                                            <div class="w-32 text-center text-gray-600 dark:text-gray-300 text-sm font-medium">
                                                {{ $file->created_at->format('d/m/Y') }}
                                            </div>

                                            <div class="w-48 flex justify-center items-center gap-2">
                                                <a href="{{ route('files.download', $file) }}" 
                                                   class="bg-blue-600 hover:bg-blue-700 text-white py-2 px-3 rounded-xl text-xs font-semibold transition-all duration-300 hover:scale-105 flex items-center gap-1 whitespace-nowrap">
                                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                                    </svg>
                                                    Télécharger
                                                </a>

                                                <form action="{{ route('files.destroy', $file) }}" method="POST" class="inline-block">
                                                    @csrf 
                                                    @method('DELETE')
                                                    <button type="submit" 
                                                            class="bg-red-600 hover:bg-red-700 text-white py-2 px-3 rounded-xl text-xs font-semibold transition-all duration-300 hover:scale-105 flex items-center gap-1 whitespace-nowrap"
                                                            onclick="return confirm('Supprimer définitivement ce fichier ?')">
                                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                        </svg>
                                                        Supprimer
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                    @endforeach
                                </div>

                                @if($files->hasPages())
                                <div class="mt-8 px-4">
                                    {{ $files->links() }}
                                </div>
                                @endif
                                
                            @else
                                <div class="text-center py-12">
                                    <div class="text-7xl mb-6">🔭</div>
                                    <h3 class="text-2xl font-bold text-gray-900 dark:text-gray-100 mb-4">
                                        Aucun fichier trouvé
                                    </h3>
                                    <p class="text-gray-500 dark:text-gray-400 mb-8 max-w-md mx-auto text-lg">
                                        @if(request('search') || request('algorithm') || request('date_filter'))
                                            Aucun fichier ne correspond à vos critères de recherche.
                                        @else
                                            Commencez par uploader votre premier fichier pour le protéger avec nos algorithmes de chiffrement avancés.
                                        @endif
                                    </p>
                                    @if(request('search') || request('algorithm') || request('date_filter'))
                                        <a href="{{ route('dashboard') }}" 
                                           class="bg-blue-600 hover:bg-blue-700 text-white px-8 py-4 rounded-xl transition-all duration-300 font-semibold text-lg hover:scale-105 hover:shadow-xl inline-block">
                                            📋 Voir tous les fichiers
                                        </a>
                                    @else
                                        <button onclick="openUploadModal()" 
                                                class="bg-blue-600 hover:bg-blue-700 text-white px-8 py-4 rounded-xl transition-all duration-300 font-semibold text-lg hover:scale-105 hover:shadow-xl">
                                            📤 Uploader mon premier fichier
                                        </button>
                                    @endif
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <!-- Modal d'upload -->
    <div id="uploadModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden transition-opacity duration-300">
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-2xl w-full max-w-md mx-4 transform transition-transform duration-300 scale-95" id="modalContent">
            <div class="p-8">
                <div class="flex justify-between items-center mb-8">
                    <h3 class="text-2xl font-bold text-gray-900 dark:text-gray-100">
                        📤 Uploader un fichier
                    </h3>
                    <button onclick="closeUploadModal()" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition duration-300 p-2 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-xl">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
                
                <form action="{{ route('files.store') }}" method="POST" enctype="multipart/form-data" class="space-y-8">
                    @csrf
                    
                    <div>
                        <label class="block text-base font-semibold mb-4 text-gray-700 dark:text-gray-300">
                            🔐 Méthode de chiffrement :
                        </label>
                        <select name="encryption_method" required 
                                class="block w-full rounded-2xl border-2 border-gray-300 shadow-lg focus:border-blue-500 focus:ring-4 focus:ring-blue-200 dark:bg-gray-700 dark:border-gray-600 dark:text-gray-300 py-4 px-6 transition-all duration-300 text-lg">
                            @php
                            $encryptionService = new App\Services\EncryptionService();
                            $algorithms = $encryptionService->getAvailableAlgorithms();
                            @endphp
                            @foreach($algorithms as $value => $name)
                                <option value="{{ $value }}">{{ $name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-base font-semibold mb-4 text-gray-700 dark:text-gray-300">
                            📄 Fichier à sécuriser :
                        </label>
                        <input type="file" name="file" id="fileInput" required 
                               accept=".txt,.doc,.docx,.rtf,.md,.pdf"
                               class="block w-full text-lg text-gray-500 file:mr-6 file:py-4 file:px-6 file:rounded-2xl file:border-0 file:text-lg file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 dark:file:bg-gray-700 dark:file:text-gray-300 transition-all duration-300">
                    </div>

                    <div class="flex justify-end space-x-4 pt-6 border-t border-gray-200 dark:border-gray-700">
                        <button type="button" onclick="closeUploadModal()" 
                                class="px-8 py-4 text-lg font-semibold text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-2xl transition-all duration-300 hover:scale-105">
                            Annuler
                        </button>
                        <button type="submit" 
                                class="bg-blue-600 hover:bg-blue-700 text-white px-8 py-4 rounded-2xl text-lg font-semibold transition-all duration-300 flex items-center space-x-3 hover:scale-105 hover:shadow-xl">
                            <span class="text-xl">🔒</span>
                            <span>Chiffrer et Sauvegarder</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        // ========== SYSTÈME DE NOTIFICATIONS TOAST ==========

        function showToast(type, message, duration = 4000) {
            const toastId = 'toast-' + Date.now();
            
            let bgColor, icon;
            
            switch(type) {
                case 'success':
                    bgColor = 'bg-blue-500';
                    icon = `<svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                             <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>`;
                    break;
                case 'delete':
                    bgColor = 'bg-red-500';
                    icon = `<svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                             <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd"/>
                            </svg>`;
                    break;
                case 'error':
                    bgColor = 'bg-red-500';
                    icon = `<svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                             <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                            </svg>`;
                    break;
                default:
                    bgColor = 'bg-gray-500';
                    icon = `<svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                             <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                            </svg>`;
            }
            
            const toastHTML = `
                <div id="${toastId}" class="toast-enter">
                    <div class="${bgColor} text-white px-5 py-3.5 rounded-xl shadow-2xl flex items-center gap-3 min-w-[320px] max-w-md border-2 border-white/20">
                        <div class="flex-shrink-0">
                            ${icon}
                        </div>
                        <span class="flex-1 font-medium text-sm leading-tight">${message}</span>
                        <button onclick="closeToast('${toastId}')" class="flex-shrink-0 hover:bg-white/20 rounded-lg p-1.5 transition-colors">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                            </svg>
                        </button>
                    </div>
                </div>
            `;
            
            const container = document.getElementById('toastContainer');
            container.insertAdjacentHTML('beforeend', toastHTML);
            
            setTimeout(() => closeToast(toastId), duration);
        }

        function closeToast(toastId) {
            const toast = document.getElementById(toastId);
            if (toast) {
                toast.classList.add('toast-exit');
                setTimeout(() => toast.remove(), 300);
            }
        }

        // ========== MODAL UPLOAD ==========

        function openUploadModal() {
            const modal = document.getElementById('uploadModal');
            const modalContent = document.getElementById('modalContent');
            modal.classList.remove('hidden');
            setTimeout(() => {
                modalContent.classList.remove('scale-95');
                modalContent.classList.add('scale-100');
            }, 10);
        }

        function closeUploadModal() {
            const modal = document.getElementById('uploadModal');
            const modalContent = document.getElementById('modalContent');
            modalContent.classList.remove('scale-100');
            modalContent.classList.add('scale-95');
            setTimeout(() => {
                modal.classList.add('hidden');
            }, 300);
        }

        // ========== MODAL INFO & SUPPORT ==========

        function openInfoModal() {
            const modal = document.getElementById('infoModal');
            const modalContent = document.getElementById('infoModalContent');
            modal.classList.remove('hidden');
            setTimeout(() => {
                modalContent.classList.remove('scale-95');
                modalContent.classList.add('scale-100');
            }, 10);
        }

        function closeInfoModal() {
            const modal = document.getElementById('infoModal');
            const modalContent = document.getElementById('infoModalContent');
            modalContent.classList.remove('scale-100');
            modalContent.classList.add('scale-95');
            setTimeout(() => {
                modal.classList.add('hidden');
            }, 300);
        }

        function contactSupport() {
            showToast('info', '📧 Redirection vers le support en cours...');
            setTimeout(() => {
                window.location.href = 'mailto:support@smartdatavault.com?subject=Support%20SmartDataVault&body=Bonjour,%0D%0A%0D%0AJ\'ai besoin d\'aide concernant...';
            }, 1000);
        }

        // ========== RECHERCHE ET FILTRES ==========

        function toggleSearch() {
            const searchBox = document.getElementById('searchBox');
            const filterBox = document.getElementById('filterBox');
            
            searchBox.classList.toggle('hidden');
            
            if (!searchBox.classList.contains('hidden')) {
                filterBox.classList.add('hidden');
                document.getElementById('searchInput').focus();
            }
        }

        function toggleFilter() {
            const filterBox = document.getElementById('filterBox');
            const searchBox = document.getElementById('searchBox');
            
            filterBox.classList.toggle('hidden');
            
            if (!filterBox.classList.contains('hidden')) {
                searchBox.classList.add('hidden');
            }
        }

        // ========== ÉVÉNEMENTS ==========

        document.getElementById('uploadModal')?.addEventListener('click', function(e) {
            if (e.target === this) {
                closeUploadModal();
            }
        });

        document.getElementById('infoModal')?.addEventListener('click', function(e) {
            if (e.target === this) {
                closeInfoModal();
            }
        });

        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                const uploadModal = document.getElementById('uploadModal');
                const infoModal = document.getElementById('infoModal');
                
                if (uploadModal && !uploadModal.classList.contains('hidden')) {
                    closeUploadModal();
                }
                if (infoModal && !infoModal.classList.contains('hidden')) {
                    closeInfoModal();
                }
            }
        });
    </script>

    {{-- Modal Info & Support --}}
    @include('components.info-support-modal')
</body>
</html>