<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            🧪 {{ __('Laboratoire de Cryptage') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
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

            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-8">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <h3 class="text-2xl font-bold mb-2">🧪 Laboratoire de Test de Cryptage</h3>
                    <p class="text-gray-600 dark:text-gray-400 mb-6">
                        Testez les différents algorithmes de chiffrement sans sauvegarder de fichiers. 
                        Parfait pour expérimenter et comprendre le fonctionnement de chaque méthode.
                    </p>
                    
                    <form id="encryptionTestForm" class="space-y-4">
                        @csrf
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <!-- Algorithme -->
                            <div>
                                <label class="block text-sm font-medium mb-2 text-gray-700 dark:text-gray-300">
                                    Algorithme :
                                </label>
                                <select name="test_algorithm" id="testAlgorithm" required 
                                        class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-gray-300">
                                    @foreach($algorithms as $value => $name)
                                        <option value="{{ $value }}">{{ $name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Clé (optionnelle pour certains algorithmes) -->
                            <div>
                                <label class="block text-sm font-medium mb-2 text-gray-700 dark:text-gray-300">
                                    Clé (optionnelle) :
                                </label>
                                <input type="text" id="testKey" 
                                       placeholder="Laissé vide pour génération auto"
                                       class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-gray-300">
                            </div>
                        </div>

                        <!-- Texte à chiffrer -->
                        <div>
                            <label class="block text-sm font-medium mb-2 text-gray-700 dark:text-gray-300">
                                Texte à chiffrer :
                            </label>
                            <textarea id="testText" rows="4" required
                                      placeholder="Entrez le texte à chiffrer..."
                                      class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-gray-300"></textarea>
                        </div>

                        <!-- Boutons d'action -->
                        <div class="flex space-x-2">
                            <button type="button" onclick="testEncryption()" 
                                    class="bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150 flex items-center">
                                🔒 Tester le Chiffrement
                            </button>
                            
                            <button type="button" onclick="testDecryption()" id="decryptBtn"
                                    class="bg-green-600 text-white px-6 py-3 rounded-lg hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition ease-in-out duration-150 flex items-center" disabled>
                                🔓 Tester le Déchiffrement
                            </button>

                            <button type="button" onclick="clearTest()" 
                                    class="bg-gray-600 text-white px-6 py-3 rounded-lg hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150 flex items-center">
                                🗑️ Effacer
                            </button>
                        </div>

                        <!-- Résultats -->
                        <div id="testResults" class="hidden mt-6 p-6 bg-gray-50 dark:bg-gray-700 rounded-lg border border-gray-200 dark:border-gray-600">
                            <h4 class="text-lg font-semibold mb-4 text-gray-900 dark:text-gray-100">📊 Résultats du Test</h4>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <!-- Résultat chiffré -->
                                <div>
                                    <label class="block text-sm font-medium mb-2 text-gray-700 dark:text-gray-300">
                                        Texte chiffré :
                                    </label>
                                    <div class="flex">
                                        <textarea id="encryptedResult" rows="4" readonly
                                                  class="flex-1 rounded-l-md border-gray-300 shadow-sm dark:bg-gray-600 dark:border-gray-500 dark:text-gray-300 font-mono text-sm"></textarea>
                                        <button type="button" onclick="copyToClipboard('encryptedResult')"
                                                class="bg-gray-500 text-white px-4 rounded-r-md hover:bg-gray-600 transition ease-in-out duration-150 flex items-center">
                                            📋
                                        </button>
                                    </div>
                                    <p id="encryptedKey" class="text-xs text-gray-500 dark:text-gray-400 mt-2 font-mono"></p>
                                </div>

                                <!-- Résultat déchiffré -->
                                <div>
                                    <label class="block text-sm font-medium mb-2 text-gray-700 dark:text-gray-300">
                                        Texte déchiffré :
                                    </label>
                                    <div class="flex">
                                        <textarea id="decryptedResult" rows="4" readonly
                                                  class="flex-1 rounded-l-md border-gray-300 shadow-sm dark:bg-gray-600 dark:border-gray-500 dark:text-gray-300 font-mono text-sm"></textarea>
                                        <button type="button" onclick="copyToClipboard('decryptedResult')"
                                                class="bg-gray-500 text-white px-4 rounded-r-md hover:bg-gray-600 transition ease-in-out duration-150 flex items-center">
                                            📋
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Section d'information sur les algorithmes -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <h3 class="text-xl font-semibold mb-4">ℹ️ À propos des Algorithmes</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="p-4 bg-blue-50 dark:bg-blue-900/20 rounded-lg">
                            <h4 class="font-semibold text-blue-800 dark:text-blue-300">César & Reverse</h4>
                            <p class="text-sm text-blue-600 dark:text-blue-400 mt-1">
                                Algorithmes basiques parfaits pour l'apprentissage. Faible sécurité mais excellents pour comprendre les concepts.
                            </p>
                        </div>
                        <div class="p-4 bg-green-50 dark:bg-green-900/20 rounded-lg">
                            <h4 class="font-semibold text-green-800 dark:text-green-300">Vigenère & Substitution</h4>
                            <p class="text-sm text-green-600 dark:text-green-400 mt-1">
                                Niveau sécurité intermédiaire. Idéal pour une protection basique des données textuelles.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Script JavaScript -->
    <script>
    // Tester le chiffrement
    async function testEncryption() {
        const algorithm = document.getElementById('testAlgorithm').value;
        const text = document.getElementById('testText').value;
        const key = document.getElementById('testKey').value;

        if (!text) {
            alert('Veuillez entrer un texte à chiffrer');
            return;
        }

        // Afficher un indicateur de chargement
        const encryptBtn = document.querySelector('button[onclick="testEncryption()"]');
        const originalText = encryptBtn.textContent;
        encryptBtn.textContent = '⏳ Chiffrement...';
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
                // Afficher les résultats
                document.getElementById('encryptedResult').value = result.encrypted_content;
                document.getElementById('decryptedResult').value = result.decrypted_text;
                document.getElementById('encryptedKey').textContent = 'Clé utilisée: ' + result.used_key;
                
                // Stocker les données pour le déchiffrement
                document.getElementById('decryptBtn').dataset.encrypted = result.encrypted_content;
                document.getElementById('decryptBtn').dataset.key = result.used_key;
                document.getElementById('decryptBtn').dataset.algorithm = algorithm;
                document.getElementById('decryptBtn').disabled = false;
                
                // Afficher la section résultats
                document.getElementById('testResults').classList.remove('hidden');
            } else {
                alert('Erreur: ' + result.error);
            }
        } catch (error) {
            alert('Erreur lors du test: ' + error.message);
        } finally {
            // Restaurer le bouton
            encryptBtn.textContent = originalText;
            encryptBtn.disabled = false;
        }
    }

    // Tester le déchiffrement
    async function testDecryption() {
        const btn = document.getElementById('decryptBtn');
        const encryptedContent = btn.dataset.encrypted;
        const key = btn.dataset.key;
        const algorithm = btn.dataset.algorithm;

        // Afficher un indicateur de chargement
        const originalText = btn.textContent;
        btn.textContent = '⏳ Déchiffrement...';
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
            } else {
                alert('Erreur: ' + result.error);
            }
        } catch (error) {
            alert('Erreur lors du déchiffrement: ' + error.message);
        } finally {
            // Restaurer le bouton
            btn.textContent = originalText;
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
        document.getElementById('decryptBtn').disabled = true;
    }

    // Copier dans le presse-papier
    function copyToClipboard(elementId) {
        const element = document.getElementById(elementId);
        element.select();
        element.setSelectionRange(0, 99999); // Pour mobile
        
        try {
            const successful = document.execCommand('copy');
            if (successful) {
                // Feedback visuel temporaire
                const btn = event.target;
                const originalText = btn.textContent;
                btn.textContent = '✅';
                setTimeout(() => {
                    btn.textContent = originalText;
                }, 1000);
            }
        } catch (err) {
            // Fallback pour les navigateurs modernes
            navigator.clipboard.writeText(element.value).then(() => {
                const btn = event.target;
                const originalText = btn.textContent;
                btn.textContent = '✅';
                setTimeout(() => {
                    btn.textContent = originalText;
                }, 1000);
            });
        }
    }

    // Générer une clé aléatoire pour Vigenère
    document.getElementById('testAlgorithm').addEventListener('change', function() {
        const algorithm = this.value;
        const keyInput = document.getElementById('testKey');
        
        if (algorithm === 'vigenere') {
            keyInput.placeholder = 'Ex: SECRET (min 3 caractères)';
        } else if (algorithm === 'cesar') {
            keyInput.placeholder = 'Ex: 3 (décalage)';
        } else {
            keyInput.placeholder = 'Laissé vide pour génération auto';
        }
    });

    // Soumission du formulaire avec Enter
    document.getElementById('testText').addEventListener('keypress', function(e) {
        if (e.key === 'Enter' && e.ctrlKey) {
            e.preventDefault();
            testEncryption();
        }
    });
    </script>
</x-app-layout>