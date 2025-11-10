{{-- resources/views/files/partials/upload-modal.blade.php --}}
<div id="uploadModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden transition-opacity duration-300">
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-2xl w-full max-w-md mx-4 transform transition-transform duration-300 scale-95" id="modalContent">
        <div class="p-6">
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-xl font-semibold text-gray-900 dark:text-gray-100">
                    📤 Uploader un fichier
                </h3>
                <button onclick="closeUploadModal()" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition duration-150">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            
            <form action="{{ route('files.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                @csrf
                
                <!-- Upload de fichier -->
                <div>
                    <label class="block text-sm font-medium mb-3 text-gray-700 dark:text-gray-300">
                        📄 Fichier à sécuriser :
                    </label>
                    <input type="file" name="file" id="fileInput" required 
                           accept="image/*,.jpg,.jpeg,.png,.gif,.bmp,.webp,.txt,.doc,.docx,.rtf,.md,.pdf"
                           onchange="updateAlgorithmOptions(this)"
                           class="block w-full text-sm text-gray-500 file:mr-4 file:py-3 file:px-4 file:rounded-xl file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 dark:file:bg-gray-700 dark:file:text-gray-300 transition duration-150">
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-2">
                        Formats acceptés : Texte (.txt, .doc, .docx, .rtf, .md, .pdf) ou Images (.jpg, .jpeg, .png, .gif, .bmp, .webp) (max 10MB)
                    </p>
                </div>

                <!-- Sélection de l'algorithme -->
                <div>
                    <label class="block text-sm font-medium mb-3 text-gray-700 dark:text-gray-300">
                        🔐 Méthode de chiffrement :
                    </label>
                    <select name="encryption_method" id="algorithmSelect" required 
                            class="block w-full rounded-xl border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-gray-300 py-3 px-4 border transition duration-150">
                        <!-- Options texte par défaut -->
                        @foreach($algorithms as $value => $name)
                            <option value="{{ $value }}" class="text-algorithm">{{ $name }}</option>
                        @endforeach
                        <!-- Options images -->
                        @foreach($imageAlgorithms as $value => $name)
                            <option value="{{ $value }}" class="image-algorithm" style="display:none;">{{ $name }}</option>
                        @endforeach
                    </select>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-2">
                        Choisissez l'algorithme pour sécuriser votre fichier
                    </p>
                </div>

                <div class="flex justify-end space-x-3 pt-4 border-t border-gray-200 dark:border-gray-700">
                    <button type="button" onclick="closeUploadModal()" 
                            class="px-6 py-3 text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-xl transition duration-150">
                        Annuler
                    </button>
                    <button type="submit" 
                            class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-xl text-sm font-medium transition duration-150 flex items-center space-x-2">
                        <span>🔒</span>
                        <span>Chiffrer et Sauvegarder</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
