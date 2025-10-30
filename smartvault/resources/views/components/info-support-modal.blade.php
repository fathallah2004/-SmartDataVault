<!-- Modal Info & Support - Design Amélioré -->
<div id="infoModal" class="fixed inset-0 bg-black/60 backdrop-blur-sm flex items-center justify-center z-50 hidden transition-all duration-300 p-4 overflow-y-auto">
    <div class="bg-gradient-to-br from-white to-gray-50 dark:from-gray-800 dark:to-gray-900 rounded-3xl shadow-2xl w-full max-w-4xl transform transition-all duration-300 scale-95 my-8 border border-gray-200 dark:border-gray-700" id="infoModalContent">
        
        <!-- Header avec gradient -->
        <div class="relative bg-gradient-to-r from-blue-600 via-purple-600 to-indigo-600 p-8 text-white overflow-hidden">
            <div class="absolute top-0 right-0 w-40 h-40 bg-white/10 rounded-full blur-3xl -mr-20 -mt-20"></div>
            <div class="absolute bottom-0 left-0 w-32 h-32 bg-white/10 rounded-full blur-3xl -ml-16 -mb-16"></div>
            <div class="absolute top-1/2 right-1/4 w-24 h-24 bg-white/5 rounded-full blur-2xl"></div>
            
            <div class="relative flex justify-between items-start">
                <div>
                    <div class="flex items-center space-x-3 mb-2">
                        <div class="w-12 h-12 bg-white/20 backdrop-blur-sm rounded-xl flex items-center justify-center">
                            <i class="fas fa-shield-alt text-2xl"></i>
                        </div>
                        <h3 class="text-3xl font-bold">SmartDataVault</h3>
                    </div>
                    <p class="text-blue-100 text-sm mt-2">Votre coffre-fort numérique sécurisé</p>
                </div>
                <button onclick="closeInfoModal()" class="text-white/80 hover:text-white hover:bg-white/20 transition-all duration-300 p-2 rounded-xl backdrop-blur-sm">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
        </div>

        <!-- Contenu scrollable -->
        <div class="overflow-y-auto max-h-[70vh] p-8">
            <div class="space-y-8">
                
                <!-- Section À propos avec carte -->
                <div class="bg-white dark:bg-gray-800 rounded-2xl p-6 shadow-lg border border-gray-100 dark:border-gray-700 hover:shadow-xl transition-shadow duration-300">
                    <div class="flex items-start space-x-4">
                        <div class="flex-shrink-0">
                            <div class="w-14 h-14 bg-gradient-to-br from-blue-500 to-blue-600 rounded-2xl flex items-center justify-center shadow-lg">
                                <i class="fas fa-info-circle text-white text-2xl"></i>
                            </div>
                        </div>
                        <div class="flex-1">
                            <h4 class="text-xl font-bold text-gray-900 dark:text-gray-100 mb-3">
                                À propos de SmartDataVault
                            </h4>
                            <p class="text-gray-600 dark:text-gray-300 leading-relaxed">
                                SmartDataVault est une plateforme innovante de chiffrement de fichiers qui garantit la protection 
                                maximale de vos documents sensibles. Grâce à une combinaison d'algorithmes cryptographiques avancés, 
                                vos données sont chiffrées localement avant leur stockage, assurant une sécurité de bout en bout.
                            </p>
                            <div class="mt-4 flex flex-wrap gap-2">
                                <span class="px-3 py-1 bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300 rounded-full text-xs font-semibold">
                                    🔒 Chiffrement Local
                                </span>
                                <span class="px-3 py-1 bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-300 rounded-full text-xs font-semibold">
                                    ✅ 100% Sécurisé
                                </span>
                                <span class="px-3 py-1 bg-purple-100 dark:bg-purple-900/30 text-purple-700 dark:text-purple-300 rounded-full text-xs font-semibold">
                                    ⚡ Rapide & Efficace
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Grille de fonctionnalités -->
                <div>
                    <h4 class="text-2xl font-bold text-gray-900 dark:text-gray-100 mb-6 flex items-center">
                        <div class="w-10 h-10 bg-gradient-to-br from-yellow-500 to-orange-500 rounded-xl flex items-center justify-center mr-3 shadow-lg">
                            <i class="fas fa-star text-white"></i>
                        </div>
                        Fonctionnalités Principales
                    </h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <!-- Feature Card 1 -->
                        <div class="group bg-white dark:bg-gray-800 rounded-2xl p-5 shadow-md border border-gray-100 dark:border-gray-700 hover:shadow-xl hover:scale-105 transition-all duration-300">
                            <div class="flex items-start space-x-4">
                                <div class="w-12 h-12 bg-gradient-to-br from-green-500 to-emerald-600 rounded-xl flex items-center justify-center flex-shrink-0 shadow-lg group-hover:scale-110 transition-transform">
                                    <i class="fas fa-file-shield text-white text-lg"></i>
                                </div>
                                <div>
                                    <h5 class="font-bold text-gray-900 dark:text-gray-100 mb-1">Formats Multiples</h5>
                                    <p class="text-sm text-gray-600 dark:text-gray-300">Support de TXT, PDF, Word, RTF et Markdown</p>
                                </div>
                            </div>
                        </div>

                        <!-- Feature Card 2 -->
                        <div class="group bg-white dark:bg-gray-800 rounded-2xl p-5 shadow-md border border-gray-100 dark:border-gray-700 hover:shadow-xl hover:scale-105 transition-all duration-300">
                            <div class="flex items-start space-x-4">
                                <div class="w-12 h-12 bg-gradient-to-br from-purple-500 to-pink-600 rounded-xl flex items-center justify-center flex-shrink-0 shadow-lg group-hover:scale-110 transition-transform">
                                    <i class="fas fa-key text-white text-lg"></i>
                                </div>
                                <div>
                                    <h5 class="font-bold text-gray-900 dark:text-gray-100 mb-1">5 Algorithmes</h5>
                                    <p class="text-sm text-gray-600 dark:text-gray-300">César, Vigenère, XOR, Substitution, Reverse</p>
                                </div>
                            </div>
                        </div>

                        <!-- Feature Card 3 -->
                        <div class="group bg-white dark:bg-gray-800 rounded-2xl p-5 shadow-md border border-gray-100 dark:border-gray-700 hover:shadow-xl hover:scale-105 transition-all duration-300">
                            <div class="flex items-start space-x-4">
                                <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-cyan-600 rounded-xl flex items-center justify-center flex-shrink-0 shadow-lg group-hover:scale-110 transition-transform">
                                    <i class="fas fa-database text-white text-lg"></i>
                                </div>
                                <div>
                                    <h5 class="font-bold text-gray-900 dark:text-gray-100 mb-1">100 MB Stockage</h5>
                                    <p class="text-sm text-gray-600 dark:text-gray-300">Espace sécurisé pour vos fichiers chiffrés</p>
                                </div>
                            </div>
                        </div>

                        <!-- Feature Card 4 -->
                        <div class="group bg-white dark:bg-gray-800 rounded-2xl p-5 shadow-md border border-gray-100 dark:border-gray-700 hover:shadow-xl hover:scale-105 transition-all duration-300">
                            <div class="flex items-start space-x-4">
                                <div class="w-12 h-12 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-xl flex items-center justify-center flex-shrink-0 shadow-lg group-hover:scale-110 transition-transform">
                                    <i class="fas fa-moon text-white text-lg"></i>
                                </div>
                                <div>
                                    <h5 class="font-bold text-gray-900 dark:text-gray-100 mb-1">Mode Sombre</h5>
                                    <p class="text-sm text-gray-600 dark:text-gray-300">Interface adaptative pour votre confort</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Section Support Premium -->
                <div class="relative bg-gradient-to-br from-indigo-600 via-purple-600 to-pink-600 rounded-2xl p-8 text-white overflow-hidden shadow-2xl">
                    <div class="absolute top-0 right-0 w-32 h-32 bg-white/10 rounded-full blur-3xl -mr-16 -mt-16"></div>
                    <div class="absolute bottom-0 left-0 w-24 h-24 bg-white/10 rounded-full blur-3xl -ml-12 -mb-12"></div>
                    <div class="absolute top-1/2 left-1/3 w-20 h-20 bg-white/5 rounded-full blur-2xl"></div>
                    
                    <div class="relative">
                        <div class="flex items-center space-x-3 mb-4">
                            <div class="w-14 h-14 bg-white/20 backdrop-blur-sm rounded-2xl flex items-center justify-center">
                                <i class="fas fa-headset text-2xl"></i>
                            </div>
                            <h4 class="text-2xl font-bold">Support Technique 24/7</h4>
                        </div>
                        
                        <p class="text-blue-100 mb-6 leading-relaxed">
                            Notre équipe d'experts est à votre disposition pour vous accompagner dans l'utilisation de SmartDataVault
                        </p>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <a href="mailto:fathallahamine2004@gmail.com" class="bg-white/10 backdrop-blur-sm rounded-xl p-4 hover:bg-white/20 transition-all cursor-pointer group">
                                <div class="flex items-center space-x-3">
                                    <div class="w-10 h-10 bg-white/20 rounded-lg flex items-center justify-center group-hover:scale-110 transition-transform">
                                        <i class="fas fa-envelope text-lg"></i>
                                    </div>
                                    <div>
                                        <p class="text-xs text-blue-100 mb-1">Email</p>
                                        <p class="font-semibold text-sm break-all">fathallahamine2004@gmail.com</p>
                                    </div>
                                </div>
                            </a>

                            <a href="tel:+21625887779" class="bg-white/10 backdrop-blur-sm rounded-xl p-4 hover:bg-white/20 transition-all cursor-pointer group">
                                <div class="flex items-center space-x-3">
                                    <div class="w-10 h-10 bg-white/20 rounded-lg flex items-center justify-center group-hover:scale-110 transition-transform">
                                        <i class="fas fa-phone text-lg"></i>
                                    </div>
                                    <div>
                                        <p class="text-xs text-blue-100 mb-1">Téléphone</p>
                                        <p class="font-semibold text-sm">+216 25 887 779</p>
                                    </div>
                                </div>
                            </a>

                            <div class="bg-white/10 backdrop-blur-sm rounded-xl p-4 hover:bg-white/20 transition-all">
                                <div class="flex items-center space-x-3">
                                    <div class="w-10 h-10 bg-white/20 rounded-lg flex items-center justify-center">
                                        <i class="fas fa-clock text-lg"></i>
                                    </div>
                                    <div>
                                        <p class="text-xs text-blue-100 mb-1">Disponibilité</p>
                                        <p class="font-semibold text-sm">24/7 - 7j/7</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>



                <!-- Stats Section -->
                <div class="bg-gradient-to-r from-gray-50 to-gray-100 dark:from-gray-800 dark:to-gray-900 rounded-2xl p-6 border border-gray-200 dark:border-gray-700">
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
                        <div class="text-center">
                            <div class="text-3xl font-bold text-blue-600 dark:text-blue-400 mb-1">10K+</div>
                            <p class="text-sm text-gray-600 dark:text-gray-400">Utilisateurs actifs</p>
                        </div>
                        <div class="text-center">
                            <div class="text-3xl font-bold text-green-600 dark:text-green-400 mb-1">500K+</div>
                            <p class="text-sm text-gray-600 dark:text-gray-400">Fichiers sécurisés</p>
                        </div>
                        <div class="text-center">
                            <div class="text-3xl font-bold text-purple-600 dark:text-purple-400 mb-1">99.9%</div>
                            <p class="text-sm text-gray-600 dark:text-gray-400">Uptime garanti</p>
                        </div>
                        <div class="text-center">
                            <div class="text-3xl font-bold text-orange-600 dark:text-orange-400 mb-1">5⭐</div>
                            <p class="text-sm text-gray-600 dark:text-gray-400">Note moyenne</p>
                        </div>
                    </div>
                </div>

            </div>
        </div>

        <!-- Footer avec actions -->
        <div class="bg-gradient-to-r from-gray-50 to-gray-100 dark:from-gray-800 dark:to-gray-900 px-8 py-5 border-t border-gray-200 dark:border-gray-700">
            <div class="flex justify-center">
                <button onclick="closeInfoModal()" 
                        class="px-12 py-3 text-sm font-semibold text-white bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700 rounded-xl transition-all duration-300 shadow-lg hover:shadow-xl hover:scale-105">
                    Fermer
                </button>
            </div>
        </div>
    </div>
</div>