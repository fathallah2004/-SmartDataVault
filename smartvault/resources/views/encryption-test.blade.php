<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>SmartDataVault - Test Cryptage</title>
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

        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.5; }
        }

        .loading {
            animation: pulse 1.5s ease-in-out infinite;
        }
    </style>
</head>
<body class="bg-gray-100 dark:bg-gray-900">
    
    <!-- Container pour les notifications toast -->
    <div id="toastContainer" class="fixed top-4 right-4 z-50 space-y-3 max-w-md">
        <!-- Les notifications seront ajoutées ici -->
    </div>

    <!-- Notifications Laravel converties en toast -->
    @if(session('success'))
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                showToast('success', {!! json_encode(session('success')) !!});
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
                    <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight flex items-center space-x-3">
                        <div class="w-10 h-10 bg-gradient-to-br from-purple-500 to-pink-500 rounded-xl flex items-center justify-center shadow-lg">
                            <i class="fas fa-flask text-white"></i>
                        </div>
                        <span>Laboratoire de Cryptage</span>
                    </h2>
                </div>
            </header>

            <!-- Main Content -->
            <main class="py-6">
                <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                    
                    <!-- Carte principale du laboratoire -->
                    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg border border-gray-200 dark:border-gray-700 mb-6">
                        <div class="p-8">
                            <!-- En-tête -->
                            <div class="mb-8">
                                <div class="flex items-start space-x-4 mb-4">
                                    <div class="w-14 h-14 bg-gradient-to-br from-purple-500 to-pink-500 rounded-2xl flex items-center justify-center shadow-lg flex-shrink-0">
                                        <i class="fas fa-flask text-white text-2xl"></i>
                                    </div>
                                    <div>
                                        <h3 class="text-2xl font-bold text-gray-900 dark:text-gray-100">
                                            Laboratoire de Test
                                        </h3>
                                        <p class="text-gray-600 dark:text-gray-400 mt-1">
                                            Testez les différents algorithmes de chiffrement sans sauvegarder de fichiers. 
                                            Parfait pour expérimenter et comprendre le fonctionnement de chaque méthode.
                                        </p>
                                    </div>
                                </div>
                            </div>
                            
                            <form id="encryptionTestForm" class="space-y-6">
                                @csrf
                                
                                <!-- Options de configuration -->
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <!-- Algorithme -->
                                    <div>
                                        <label class="block text-sm font-semibold mb-3 text-gray-700 dark:text-gray-300 flex items-center space-x-2">
                                            <i class="fas fa-cog text-blue-500"></i>
                                            <span>Algorithme de chiffrement</span>
                                        </label>
                                        <select name="test_algorithm" id="testAlgorithm" required 
                                                class="block w-full rounded-xl border-2 border-gray-300 dark:border-gray-600 shadow-sm focus:border-blue-500 focus:ring-4 focus:ring-blue-200 dark:bg-gray-700 dark:text-gray-300 py-3 px-4 transition-all duration-300">
                                            @foreach($algorithms as $value => $name)
                                                <option value="{{ $value }}">{{ $name }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <!-- Clé -->
                                    <div>
                                        <label class="block text-sm font-semibold mb-3 text-gray-700 dark:text-gray-300 flex items-center space-x-2">
                                            <i class="fas fa-key text-yellow-500"></i>
                                            <span>Clé de chiffrement (optionnelle)</span>
                                        </label>
                                        <input type="text" id="testKey" 
                                               placeholder="Laissé vide pour génération auto"
                                               class="block w-full rounded-xl border-2 border-gray-300 dark:border-gray-600 shadow-sm focus:border-blue-500 focus:ring-4 focus:ring-blue-200 dark:bg-gray-700 dark:text-gray-300 py-3 px-4 transition-all duration-300">
                                    </div>
                                </div>

                                <!-- Texte à chiffrer -->
                                <div>
                                    <label class="block text-sm font-semibold mb-3 text-gray-700 dark:text-gray-300 flex items-center space-x-2">
                                        <i class="fas fa-file-alt text-green-500"></i>
                                        <span>Texte à chiffrer</span>
                                    </label>
                                    <textarea id="testText" rows="6" required
                                              placeholder="Entrez le texte que vous souhaitez chiffrer..."
                                              class="block w-full rounded-xl border-2 border-gray-300 dark:border-gray-600 shadow-sm focus:border-blue-500 focus:ring-4 focus:ring-blue-200 dark:bg-gray-700 dark:text-gray-300 py-3 px-4 transition-all duration-300 font-mono"></textarea>
                                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-2">
                                        💡 Astuce : Utilisez Ctrl + Entrée pour chiffrer rapidement
                                    </p>
                                </div>

                                <!-- Boutons d'action -->
                                <div class="flex flex-wrap gap-3">
                                    <button type="button" onclick="testEncryption()" 
                                            class="bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700 text-white px-8 py-3 rounded-xl font-semibold transition-all duration-300 flex items-center space-x-2 shadow-lg hover:shadow-xl hover:scale-105">
                                        <i class="fas fa-lock"></i>
                                        <span>Tester le Chiffrement</span>
                                    </button>
                                    
                                    <button type="button" onclick="testDecryption()" id="decryptBtn"
                                            class="bg-gradient-to-r from-green-600 to-emerald-600 hover:from-green-700 hover:to-emerald-700 text-white px-8 py-3 rounded-xl font-semibold transition-all duration-300 flex items-center space-x-2 shadow-lg hover:shadow-xl hover:scale-105 opacity-50 cursor-not-allowed" disabled>
                                        <i class="fas fa-unlock"></i>
                                        <span>Tester le Déchiffrement</span>
                                    </button>

                                    <button type="button" onclick="clearTest()" 
                                            class="bg-gray-600 hover:bg-gray-700 text-white px-8 py-3 rounded-xl font-semibold transition-all duration-300 flex items-center space-x-2 shadow-lg hover:shadow-xl hover:scale-105">
                                        <i class="fas fa-trash-alt"></i>
                                        <span>Effacer</span>
                                    </button>
                                </div>

                                <!-- Résultats -->
                                <div id="testResults" class="hidden mt-8 p-6 bg-gradient-to-br from-gray-50 to-gray-100 dark:from-gray-700 dark:to-gray-800 rounded-2xl border-2 border-gray-200 dark:border-gray-600">
                                    <h4 class="text-xl font-bold mb-6 text-gray-900 dark:text-gray-100 flex items-center space-x-2">
                                        <div class="w-8 h-8 bg-gradient-to-br from-blue-500 to-purple-500 rounded-lg flex items-center justify-center">
                                            <i class="fas fa-chart-bar text-white text-sm"></i>
                                        </div>
                                        <span>Résultats du Test</span>
                                    </h4>
                                    
                                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                                        <!-- Résultat chiffré -->
                                        <div class="bg-white dark:bg-gray-800 rounded-xl p-5 shadow-md border border-gray-200 dark:border-gray-700">
                                            <label class="block text-sm font-semibold mb-3 text-gray-700 dark:text-gray-300 flex items-center justify-between">
                                                <span class="flex items-center space-x-2">
                                                    <i class="fas fa-lock text-blue-500"></i>
                                                    <span>Texte chiffré (texte brut)</span>
                                                </span>
                                                <button type="button" onclick="copyToClipboard('encryptedResult')"
                                                        class="text-xs bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300 px-3 py-1 rounded-lg hover:bg-blue-200 dark:hover:bg-blue-900/50 transition-all duration-300 flex items-center space-x-1">
                                                    <i class="fas fa-copy"></i>
                                                    <span>Copier</span>
                                                </button>
                                            </label>
                                            <textarea id="encryptedResult" rows="6" readonly
                                                      class="w-full rounded-xl border-2 border-gray-300 dark:border-gray-600 shadow-sm dark:bg-gray-700 dark:text-gray-300 font-mono text-sm p-3 resize-none"></textarea>
                                            <p id="encryptedKey" class="text-xs text-gray-500 dark:text-gray-400 mt-3 font-mono bg-gray-100 dark:bg-gray-700 p-2 rounded-lg"></p>
                                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-2">
                                                💡 Le texte affiché est le résultat brut du chiffrement (non encodé en base64)
                                            </p>
                                        </div>

                                        <!-- Résultat déchiffré -->
                                        <div class="bg-white dark:bg-gray-800 rounded-xl p-5 shadow-md border border-gray-200 dark:border-gray-700">
                                            <label class="block text-sm font-semibold mb-3 text-gray-700 dark:text-gray-300 flex items-center justify-between">
                                                <span class="flex items-center space-x-2">
                                                    <i class="fas fa-unlock text-green-500"></i>
                                                    <span>Texte déchiffré</span>
                                                </span>
                                                <button type="button" onclick="copyToClipboard('decryptedResult')"
                                                        class="text-xs bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-300 px-3 py-1 rounded-lg hover:bg-green-200 dark:hover:bg-green-900/50 transition-all duration-300 flex items-center space-x-1">
                                                    <i class="fas fa-copy"></i>
                                                    <span>Copier</span>
                                                </button>
                                            </label>
                                            <textarea id="decryptedResult" rows="6" readonly
                                                      class="w-full rounded-xl border-2 border-gray-300 dark:border-gray-600 shadow-sm dark:bg-gray-700 dark:text-gray-300 font-mono text-sm p-3 resize-none"></textarea>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Section d'information sur les algorithmes -->
                    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg border border-gray-200 dark:border-gray-700">
                        <div class="p-8">
                            <h3 class="text-xl font-bold mb-6 text-gray-900 dark:text-gray-100 flex items-center space-x-2">
                                <div class="w-8 h-8 bg-gradient-to-br from-indigo-500 to-purple-500 rounded-lg flex items-center justify-center">
                                    <i class="fas fa-info-circle text-white text-sm"></i>
                                </div>
                                <span>À propos des Algorithmes</span>
                            </h3>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                                <!-- César -->
                                <div class="group bg-gradient-to-br from-blue-50 to-blue-100 dark:from-blue-900/20 dark:to-blue-800/20 rounded-xl p-5 border-2 border-blue-200 dark:border-blue-700 hover:shadow-lg transition-all duration-300 hover:scale-105">
                                    <div class="flex items-center space-x-3 mb-3">
                                        <div class="w-10 h-10 bg-blue-500 rounded-lg flex items-center justify-center">
                                            <i class="fas fa-arrow-right text-white"></i>
                                        </div>
                                        <h4 class="font-bold text-blue-800 dark:text-blue-300 text-lg">César</h4>
                                    </div>
                                    <p class="text-sm text-blue-700 dark:text-blue-400 leading-relaxed">
                                        Algorithme de substitution simple basé sur le décalage de lettres. Parfait pour l'apprentissage des bases du chiffrement.
                                    </p>
                                </div>

                                <!-- Vigenère -->
                                <div class="group bg-gradient-to-br from-purple-50 to-purple-100 dark:from-purple-900/20 dark:to-purple-800/20 rounded-xl p-5 border-2 border-purple-200 dark:border-purple-700 hover:shadow-lg transition-all duration-300 hover:scale-105">
                                    <div class="flex items-center space-x-3 mb-3">
                                        <div class="w-10 h-10 bg-purple-500 rounded-lg flex items-center justify-center">
                                            <i class="fas fa-key text-white"></i>
                                        </div>
                                        <h4 class="font-bold text-purple-800 dark:text-purple-300 text-lg">Vigenère</h4>
                                    </div>
                                    <p class="text-sm text-purple-700 dark:text-purple-400 leading-relaxed">
                                        Chiffrement polyalphabétique utilisant une clé. Sécurité intermédiaire, idéal pour protéger des données textuelles.
                                    </p>
                                </div>

                                <!-- XOR -->
                                <div class="group bg-gradient-to-br from-red-50 to-red-100 dark:from-red-900/20 dark:to-red-800/20 rounded-xl p-5 border-2 border-red-200 dark:border-red-700 hover:shadow-lg transition-all duration-300 hover:scale-105">
                                    <div class="flex items-center space-x-3 mb-3">
                                        <div class="w-10 h-10 bg-red-500 rounded-lg flex items-center justify-center">
                                            <i class="fas fa-exchange-alt text-white"></i>
                                        </div>
                                        <h4 class="font-bold text-red-800 dark:text-red-300 text-lg">XOR</h4>
                                    </div>
                                    <p class="text-sm text-red-700 dark:text-red-400 leading-relaxed">
                                        Opération bit à bit rapide et efficace. Utilisé dans de nombreux systèmes de chiffrement modernes.
                                    </p>
                                </div>

                                <!-- Substitution -->
                                <div class="group bg-gradient-to-br from-green-50 to-green-100 dark:from-green-900/20 dark:to-green-800/20 rounded-xl p-5 border-2 border-green-200 dark:border-green-700 hover:shadow-lg transition-all duration-300 hover:scale-105">
                                    <div class="flex items-center space-x-3 mb-3">
                                        <div class="w-10 h-10 bg-green-500 rounded-lg flex items-center justify-center">
                                            <i class="fas fa-random text-white"></i>
                                        </div>
                                        <h4 class="font-bold text-green-800 dark:text-green-300 text-lg">Substitution</h4>
                                    </div>
                                    <p class="text-sm text-green-700 dark:text-green-400 leading-relaxed">
                                        Remplace chaque caractère par un autre selon une table de correspondance. Bon niveau de sécurité basique.
                                    </p>
                                </div>

                                <!-- Reverse -->
                                <div class="group bg-gradient-to-br from-orange-50 to-orange-100 dark:from-orange-900/20 dark:to-orange-800/20 rounded-xl p-5 border-2 border-orange-200 dark:border-orange-700 hover:shadow-lg transition-all duration-300 hover:scale-105">
                                    <div class="flex items-center space-x-3 mb-3">
                                        <div class="w-10 h-10 bg-orange-500 rounded-lg flex items-center justify-center">
                                            <i class="fas fa-undo text-white"></i>
                                        </div>
                                        <h4 class="font-bold text-orange-800 dark:text-orange-300 text-lg">Reverse</h4>
                                    </div>
                                    <p class="text-sm text-orange-700 dark:text-orange-400 leading-relaxed">
                                        Inverse simplement l'ordre des caractères. Très basique, parfait pour comprendre les concepts fondamentaux.
                                    </p>
                                </div>

                                <!-- Info générale -->
                                <div class="bg-gradient-to-br from-gray-50 to-gray-100 dark:from-gray-700 dark:to-gray-800 rounded-xl p-5 border-2 border-gray-200 dark:border-gray-600 flex items-center justify-center">
                                    <div class="text-center">
                                        <i class="fas fa-lightbulb text-4xl text-yellow-500 mb-3"></i>
                                        <p class="text-sm text-gray-700 dark:text-gray-300 font-semibold">
                                            Testez chaque algorithme pour mieux comprendre leurs différences!
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </main>
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

        // ========== FONCTIONS DE TEST ==========

        // Tester le chiffrement
        async function testEncryption() {
            const algorithm = document.getElementById('testAlgorithm').value;
            const text = document.getElementById('testText').value;
            const key = document.getElementById('testKey').value;

            if (!text) {
                showToast('error', 'Veuillez entrer un texte à chiffrer');
                return;
            }

            const encryptBtn = document.querySelector('button[onclick="testEncryption()"]');
            const originalHTML = encryptBtn.innerHTML;
            encryptBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i><span>Chiffrement en cours...</span>';
            encryptBtn.disabled = true;

            try {
                const response = await fetch('/api/test-encryption', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({
                        text: text,
                        algorithm: algorithm,
                        key: key || null
                    })
                });

                const result = await response.json();

                if (response.ok) {
                    // Toujours afficher le texte brut chiffré (non base64)
                    const encryptedDisplay = result.encrypted_text_raw || result.encrypted_content;
                    document.getElementById('encryptedResult').value = encryptedDisplay;
                    document.getElementById('decryptedResult').value = result.decrypted_text;
                    document.getElementById('encryptedKey').textContent = '🔑 Clé utilisée: ' + result.used_key;
                    
                    // Stocker le base64 pour le déchiffrement (nécessaire pour l'API)
                    const decryptBtn = document.getElementById('decryptBtn');
                    decryptBtn.dataset.encrypted = result.encrypted_content; // Base64 pour l'API
                    decryptBtn.dataset.key = result.used_key;
                    decryptBtn.dataset.algorithm = algorithm;
                    decryptBtn.disabled = false;
                    decryptBtn.classList.remove('opacity-50', 'cursor-not-allowed');
                    
                    document.getElementById('testResults').classList.remove('hidden');
                    showToast('success', '✅ Chiffrement effectué avec succès!');
                } else {
                    showToast('error', 'Erreur: ' + result.error);
                }
            } catch (error) {
                showToast('error', 'Erreur lors du test: ' + error.message);
            } finally {
                encryptBtn.innerHTML = originalHTML;
                encryptBtn.disabled = false;
            }
        }

        // Tester le déchiffrement
        async function testDecryption() {
            const btn = document.getElementById('decryptBtn');
            const encryptedContent = btn.dataset.encrypted;
            const key = btn.dataset.key;
            const algorithm = btn.dataset.algorithm;

            const originalHTML = btn.innerHTML;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i><span>Déchiffrement en cours...</span>';
            btn.disabled = true;

            try {
                const response = await fetch('/api/test-decryption', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({
                        encrypted_content: encryptedContent,
                        algorithm: algorithm,
                        key: key
                    })
                });

                const result = await response.json();

                if (response.ok) {
                    document.getElementById('decryptedResult').value = result.decrypted_text;
                    showToast('success', '✅ Déchiffrement effectué avec succès!');
                } else {
                    showToast('error', 'Erreur: ' + result.error);
                }
            } catch (error) {
                showToast('error', 'Erreur lors du déchiffrement: ' + error.message);
            } finally {
                btn.innerHTML = originalHTML;
                btn.disabled = false;
            }
        }

        // Effacer le test
        function clearTest() {
            document.getElementById('testText').value = '';
            document.getElementById('testKey').value = '';
            document.getElementById('encryptedResult').value = '';
            document.getElementById('decryptedResult').value = '';
            document.getElementById('encryptedKey').textContent = '';
            document.getElementById('testResults').classList.add('hidden');
            
            const decryptBtn = document.getElementById('decryptBtn');
            decryptBtn.disabled = true;
            decryptBtn.classList.add('opacity-50', 'cursor-not-allowed');
            
            showToast('success', '🗑️ Formulaire effacé');
        }

        // Copier dans le presse-papier
        function copyToClipboard(elementId) {
            const element = document.getElementById(elementId);
            const text = element.value;
            
            if (!text) {
                showToast('error', 'Rien à copier');
                return;
            }
            
            navigator.clipboard.writeText(text).then(() => {
                showToast('success', '📋 Texte copié dans le presse-papier!');
            }).catch(() => {
                // Fallback pour les navigateurs plus anciens
                element.select();
                element.setSelectionRange(0, 99999);
                
                try {
                    document.execCommand('copy');
                    showToast('success', '📋 Texte copié dans le presse-papier!');
                } catch (err) {
                    showToast('error', 'Erreur lors de la copie');
                }
            });
        }

        // Gérer le changement d'algorithme
        document.getElementById('testAlgorithm').addEventListener('change', function() {
            const algorithm = this.value;
            const keyInput = document.getElementById('testKey');
            
            if (algorithm === 'vigenere') {
                keyInput.placeholder = 'Ex: SECRET (min 3 caractères)';
            } else if (algorithm === 'cesar') {
                keyInput.placeholder = 'Ex: 3 (décalage de 1 à 25)';
            } else if (algorithm === 'xor-text') {
                keyInput.placeholder = 'Ex: MYSECRETKEY';
            } else if (algorithm === 'substitution') {
                keyInput.placeholder = 'Génération automatique recommandée';
            } else {
                keyInput.placeholder = 'Non requis pour cet algorithme';
            }
        });

        // Raccourci clavier : Ctrl + Enter pour chiffrer
        document.getElementById('testText').addEventListener('keydown', function(e) {
            if (e.key === 'Enter' && e.ctrlKey) {
                e.preventDefault();
                testEncryption();
            }
        });

        // Modal Info & Support
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
                window.location.href = 'mailto:fathallahamine2004@gmail.com?subject=Support%20SmartDataVault&body=Bonjour,%0D%0A%0D%0AJ\'ai besoin d\'aide concernant...';
            }, 1000);
        }

        // Fermer le modal avec Escape
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                const infoModal = document.getElementById('infoModal');
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