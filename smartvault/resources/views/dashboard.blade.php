<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            🔒 {{ __('SmartDataVault - Mon Espace Sécurisé') }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Messages de statut -->
            @if(session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
                    ✅ {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
                    ❌ {{ session('error') }}
                </div>
            @endif

            <!-- KPI Cards - Correction pour alignement horizontal -->
            <div class="flex flex-row flex-wrap gap-4 mb-8">
                <!-- KPI 1: Espace utilisé -->
                <div class="flex-1 min-w-[280px] bg-gradient-to-br from-white to-gray-50 dark:from-gray-800 dark:to-gray-900 rounded-2xl shadow-lg hover:shadow-2xl border-l-4 border-blue-500 p-6 transform hover:-translate-y-1 transition-all duration-300 group">
                    <div class="flex items-center justify-between mb-4">
                        <div class="flex-1">
                            <p class="text-sm font-semibold text-gray-600 dark:text-gray-300 mb-1">Espace utilisé</p>
                            <p class="text-2xl font-bold text-gray-900 dark:text-white mb-2">0 bytes</p>
                            <div class="flex justify-between items-center text-xs text-gray-500 dark:text-gray-400 mb-1">
                                <span>Utilisation</span>
                                <span class="font-semibold">0%</span>
                            </div>
                            <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                                <div class="bg-gradient-to-r from-blue-400 to-blue-600 h-2 rounded-full transition-all duration-1000 ease-out group-hover:from-blue-500 group-hover:to-blue-700" style="width: 0%"></div>
                            </div>
                        </div>
                        <div class="ml-4 text-3xl text-blue-500 bg-blue-100 dark:bg-blue-900/30 rounded-2xl p-3 group-hover:scale-110 group-hover:rotate-12 transition-transform duration-300 shadow-inner">
                            💾
                        </div>
                    </div>
                    <p class="text-xs text-gray-500 dark:text-gray-400 text-center mt-3 bg-gray-100 dark:bg-gray-700/50 rounded-lg py-1 px-2">
                        0.00 MB / 100 MB
                    </p>
                </div>

                <!-- KPI 2: Fichiers -->
                <div class="flex-1 min-w-[280px] bg-gradient-to-br from-white to-gray-50 dark:from-gray-800 dark:to-gray-900 rounded-2xl shadow-lg hover:shadow-2xl border-l-4 border-green-500 p-6 transform hover:-translate-y-1 transition-all duration-300 group">
                    <div class="flex items-center justify-between mb-4">
                        <div class="flex-1">
                            <p class="text-sm font-semibold text-gray-600 dark:text-gray-300 mb-1">Fichiers</p>
                            <p class="text-2xl font-bold text-gray-900 dark:text-white mb-2">0</p>
                            <div class="text-center">
                                <span class="inline-block text-xs font-medium text-green-600 dark:text-green-400 bg-green-100 dark:bg-green-900/30 rounded-full px-3 py-1">
                                    🔒 Sécurisés
                                </span>
                            </div>
                        </div>
                        <div class="ml-4 text-3xl text-green-500 bg-green-100 dark:bg-green-900/30 rounded-2xl p-3 group-hover:scale-110 group-hover:rotate-12 transition-transform duration-300 shadow-inner">
                            📄
                        </div>
                    </div>
                    <p class="text-xs text-gray-500 dark:text-gray-400 text-center mt-3 bg-gray-100 dark:bg-gray-700/50 rounded-lg py-1 px-2">
                        Fichiers chiffrés
                    </p>
                </div>

                <!-- KPI 3: Dernier upload -->
                <div class="flex-1 min-w-[280px] bg-gradient-to-br from-white to-gray-50 dark:from-gray-800 dark:to-gray-900 rounded-2xl shadow-lg hover:shadow-2xl border-l-4 border-purple-500 p-6 transform hover:-translate-y-1 transition-all duration-300 group">
                    <div class="flex items-center justify-between mb-4">
                        <div class="flex-1">
                            <p class="text-sm font-semibold text-gray-600 dark:text-gray-300 mb-1">Dernier upload</p>
                            <p class="text-xl font-bold text-gray-900 dark:text-white mb-2">Jamais</p>
                            <div class="text-center">
                                <span class="inline-block text-xs font-medium text-purple-600 dark:text-purple-400 bg-purple-100 dark:bg-purple-900/30 rounded-full px-3 py-1">
                                    ⏳ En attente
                                </span>
                            </div>
                        </div>
                        <div class="ml-4 text-3xl text-purple-500 bg-purple-100 dark:bg-purple-900/30 rounded-2xl p-3 group-hover:scale-110 group-hover:rotate-12 transition-transform duration-300 shadow-inner">
                            🕒
                        </div>
                    </div>
                    <p class="text-xs text-gray-500 dark:text-gray-400 text-center mt-3 bg-gray-100 dark:bg-gray-700/50 rounded-lg py-1 px-2">
                        Premier upload à venir
                    </p>
                </div>

                <!-- KPI 4: Algorithmes -->
                <div class="flex-1 min-w-[280px] bg-gradient-to-br from-white to-gray-50 dark:from-gray-800 dark:to-gray-900 rounded-2xl shadow-lg hover:shadow-2xl border-l-4 border-yellow-500 p-6 transform hover:-translate-y-1 transition-all duration-300 group">
                    <div class="flex items-center justify-between mb-4">
                        <div class="flex-1">
                            <p class="text-sm font-semibold text-gray-600 dark:text-gray-300 mb-1">Algorithmes</p>
                            <p class="text-2xl font-bold text-gray-900 dark:text-white mb-2">5</p>
                            <div class="text-center">
                                <span class="inline-block text-xs font-medium text-yellow-600 dark:text-yellow-400 bg-yellow-100 dark:bg-yellow-900/30 rounded-full px-3 py-1">
                                    🛡️ Actifs
                                </span>
                            </div>
                        </div>
                        <div class="ml-4 text-3xl text-yellow-500 bg-yellow-100 dark:bg-yellow-900/30 rounded-2xl p-3 group-hover:scale-110 group-hover:rotate-12 transition-transform duration-300 shadow-inner">
                            🔐
                        </div>
                    </div>
                    <p class="text-xs text-gray-500 dark:text-gray-400 text-center mt-3 bg-gray-100 dark:bg-gray-700/50 rounded-lg py-1 px-2">
                        Méthodes disponibles
                    </p>
                </div>
            </div>

            <!-- Section unifiée Mes fichiers AMÉLIORÉE -->
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg border border-gray-200 dark:border-gray-700">
                <!-- Header avec bouton + simple -->
                <div class="px-8 py-6 border-b border-gray-200 dark:border-gray-700">
                    <div class="flex justify-between items-center">
                        <div>
                            <h3 class="text-xl font-bold text-gray-900 dark:text-gray-100">
                                📁 Mes fichiers
                            </h3>
                            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                                {{ $files->count() }} fichier(s) sécurisé(s)
                            </p>
                        </div>
                        <button onclick="openUploadModal()" 
                                class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-xl flex items-center space-x-3 transition-all duration-300 font-semibold hover:scale-105 hover:shadow-lg">
                            <span class="text-xl">+</span>
                            <span>Ajouter un fichier</span>
                        </button>
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
                            'xor' => 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200 border border-red-200',
                        ];
                        @endphp

                        <!-- En-têtes de tableau -->
                        <div class="hidden lg:flex items-center px-6 py-4 text-sm font-semibold text-gray-600 dark:text-gray-400 border-b border-gray-200 dark:border-gray-700 mb-6">
                            <div class="flex-1">Nom du fichier</div>
                            <div class="flex items-center justify-end space-x-16 w-[520px]">
                                <div class="w-32 text-center">Algorithme</div>
                                <div class="w-24 text-center">Taille</div>
                                <div class="w-32 text-center">Date</div>
                                <div class="w-40 text-center">Actions</div>
                            </div>
                        </div>

                        <!-- LISTE AVEC ESPACEMENT AMÉLIORÉ -->
                        <div class="space-y-4">
                            @foreach($files as $file)
                            <div class="group flex flex-col lg:flex-row items-center bg-white/90 dark:bg-gray-800/80 backdrop-blur-xl rounded-2xl border border-gray-200 dark:border-gray-700 p-5 hover:border-blue-400 hover:shadow-xl transition-all duration-300">
                                
                                <!-- Partie gauche : Nom du fichier (flexible) -->
                                <div class="flex-1 min-w-0 w-full lg:w-auto mb-4 lg:mb-0">
                                    <h4 class="font-bold text-gray-900 dark:text-white truncate text-lg">
                                        {{ $file->original_name }}
                                    </h4>
                                </div>

                                <!-- Partie droite : Métadonnées avec ESPACEMENT HORIZONTAL AMÉLIORÉ -->
                                <div class="flex items-center justify-end w-full lg:w-[520px] flex-shrink-0 space-x-8">
                                    <!-- Algorithme -->
                                    <div class="w-32 text-center">
                                        <span class="inline-flex items-center px-3 py-1.5 rounded-full text-sm font-medium {{ $algorithmStyles[$file->algorithm_name] ?? 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300 border border-gray-200' }}">
                                            ⚙️ {{ $file->algorithm_name }}
                                        </span>
                                    </div>

                                    <!-- Taille -->
                                    <div class="w-24 text-center text-gray-600 dark:text-gray-300 text-sm font-medium">
                                        {{ $file->formatted_size }}
                                    </div>

                                    <!-- Date -->
                                    <div class="w-32 text-center text-gray-600 dark:text-gray-300 text-sm font-medium">
                                        {{ $file->created_at->format('d/m/Y') }}
                                    </div>

                                    <!-- Actions -->
                                    <div class="w-40 flex justify-center items-center gap-3">
                                        <!-- Télécharger -->
                                        <a href="{{ route('files.download', $file) }}" 
                                           class="bg-blue-600 hover:bg-blue-700 text-white py-2 px-3 rounded-xl text-xs font-semibold transition-all duration-300 hover:scale-105 flex items-center gap-1">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                            </svg>
                                            Télécharger
                                        </a>

                                        <!-- Supprimer -->
                                        <form action="{{ route('files.destroy', $file) }}" method="POST">
                                            @csrf 
                                            @method('DELETE')
                                            <button type="submit" 
                                                    class="bg-red-600 hover:bg-red-700 text-white py-2 px-3 rounded-xl text-xs font-semibold transition-all duration-300 hover:scale-105 flex items-center gap-1"
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

                        <!-- Pagination -->
                        @if($files->hasPages())
                        <div class="mt-8 px-4">
                            {{ $files->links() }}
                        </div>
                        @endif
                        
                    @else
                        <!-- État vide avec zone de drop intégrée -->
                        <div class="text-center border-3 border-dashed border-gray-300 dark:border-gray-600 rounded-2xl p-12 hover:border-blue-400 dark:hover:border-blue-500 transition-all duration-300 cursor-pointer bg-gradient-to-br from-gray-50 to-white dark:from-gray-800 dark:to-gray-900" 
                             onclick="openUploadModal()"
                             id="dropZone">
                            <div class="max-w-md mx-auto">
                                <div class="text-7xl mb-6">📁</div>
                                <h4 class="text-xl font-bold text-gray-900 dark:text-gray-100 mb-3">
                                    Glissez-déposez vos fichiers ici
                                </h4>
                                <p class="text-base text-gray-500 dark:text-gray-400 mb-6">
                                    ou cliquez pour parcourir vos fichiers
                                </p>
                                <button class="bg-blue-600 hover:bg-blue-700 text-white px-8 py-4 rounded-xl transition-all duration-300 font-semibold text-lg hover:scale-105 hover:shadow-xl">
                                    Parcourir les fichiers
                                </button>
                                <p class="text-sm text-gray-400 dark:text-gray-500 mt-4">
                                    Fichiers texte uniquement (.txt, .doc, .pdf, etc.) - Max 5MB
                                </p>
                            </div>
                        </div>

                        <!-- Message d'état vide -->
                        <div class="text-center py-12">
                            <div class="text-7xl mb-6">📭</div>
                            <h3 class="text-2xl font-bold text-gray-900 dark:text-gray-100 mb-4">
                                Aucun fichier sécurisé
                            </h3>
                            <p class="text-gray-500 dark:text-gray-400 mb-8 max-w-md mx-auto text-lg">
                                Commencez par uploader votre premier fichier pour le protéger avec nos algorithmes de chiffrement avancés.
                            </p>
                            <button onclick="openUploadModal()" class="bg-blue-600 hover:bg-blue-700 text-white px-8 py-4 rounded-xl transition-all duration-300 font-semibold text-lg hover:scale-105 hover:shadow-xl">
                                📤 Uploader mon premier fichier
                            </button>
                        </div>
                    @endif
                </div>
            </div>
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
                    
                    <!-- Sélection de l'algorithme -->
                    <div>
                        <label class="block text-base font-semibold mb-4 text-gray-700 dark:text-gray-300">
                            🔐 Méthode de chiffrement :
                        </label>
                        <select name="encryption_method" required 
                                class="block w-full rounded-2xl border-2 border-gray-300 shadow-lg focus:border-blue-500 focus:ring-4 focus:ring-blue-200 dark:bg-gray-700 dark:border-gray-600 dark:text-gray-300 py-4 px-6 transition-all duration-300 text-lg">
                            @foreach($algorithms as $value => $name)
                                <option value="{{ $value }}">{{ $name }}</option>
                            @endforeach
                        </select>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mt-3">
                            Choisissez l'algorithme pour sécuriser votre fichier
                        </p>
                    </div>

                    <!-- Upload de fichier -->
                    <div>
                        <label class="block text-base font-semibold mb-4 text-gray-700 dark:text-gray-300">
                            📄 Fichier à sécuriser :
                        </label>
                        <input type="file" name="file" id="fileInput" required 
                               accept=".txt,.doc,.docx,.rtf,.md,.pdf"
                               class="block w-full text-lg text-gray-500 file:mr-6 file:py-4 file:px-6 file:rounded-2xl file:border-0 file:text-lg file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 dark:file:bg-gray-700 dark:file:text-gray-300 transition-all duration-300">
                        <p class="text-sm text-gray-500 dark:text-gray-400 mt-3">
                            Formats acceptés : .txt, .doc, .docx, .rtf, .md, .pdf (max 5MB)
                        </p>
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
    // Gestion du modal d'upload
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

    // Drag & Drop pour la zone de drop intégrée
    const dropZone = document.getElementById('dropZone');
    const fileInput = document.getElementById('fileInput');

    if (dropZone) {
        // Empêcher le comportement par défaut
        ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
            dropZone.addEventListener(eventName, preventDefaults, false);
        });

        function preventDefaults(e) {
            e.preventDefault();
            e.stopPropagation();
        }

        // Highlight la zone de drop
        ['dragenter', 'dragover'].forEach(eventName => {
            dropZone.addEventListener(eventName, highlight, false);
        });

        ['dragleave', 'drop'].forEach(eventName => {
            dropZone.addEventListener(eventName, unhighlight, false);
        });

        function highlight() {
            dropZone.classList.add('border-blue-400', 'bg-blue-50', 'dark:bg-blue-900/20', 'scale-105');
        }

        function unhighlight() {
            dropZone.classList.remove('border-blue-400', 'bg-blue-50', 'dark:bg-blue-900/20', 'scale-105');
        }

        // Gérer le drop
        dropZone.addEventListener('drop', handleDrop, false);

        function handleDrop(e) {
            const dt = e.dataTransfer;
            const files = dt.files;
            
            if (files.length > 0) {
                fileInput.files = files;
                openUploadModal();
            }
        }
    }

    // Fermer le modal en cliquant à l'extérieur
    document.getElementById('uploadModal').addEventListener('click', function(e) {
        if (e.target === this) {
            closeUploadModal();
        }
    });

    // Fermer le modal avec ESC
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeUploadModal();
        }
    });
    </script>

    <style>
    .file-item {
        animation: slideIn 0.6s ease-out;
    }

    @keyframes slideIn {
        from {
            opacity: 0;
            transform: translateX(-20px);
        }
        to {
            opacity: 1;
            transform: translateX(0);
        }
    }

    /* Effet de brillance au survol */
    .group:hover {
        background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%);
    }

    .dark .group:hover {
        background: linear-gradient(135deg, #1f2937 0%, #374151 100%);
    }

    /* Style personnalisé pour l'espacement */
    @media (min-width: 1024px) {
        .space-x-8 > * + * {
            margin-left: 2rem; /* 32px d'espace entre chaque élément */
        }
    }
    </style>
</x-app-layout>