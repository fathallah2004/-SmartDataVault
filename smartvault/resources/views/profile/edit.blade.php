<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('Profile') }} - SmartDataVault</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="bg-gray-50 dark:bg-gray-900">
    
    <!-- Include Sidebar Component -->
    <x-sidebar />

    <!-- Main Content Area with Sidebar Offset -->
    <div class="ml-80 min-h-screen">
        
        <!-- Header -->
        <header class="bg-white dark:bg-gray-800 shadow-sm sticky top-0 z-30">
            <div class="max-w-7xl mx-auto px-6 py-6">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-3xl font-bold text-gray-900 dark:text-gray-100">
                            <i class="fas fa-user-circle mr-3 text-blue-600"></i>
                            {{ __('Profile Settings') }}
                        </h1>
                        <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                            Gérez vos informations personnelles et la sécurité de votre compte
                        </p>
                    </div>
                </div>
            </div>
        </header>

        <!-- Main Content -->
        <main class="max-w-7xl mx-auto px-6 py-8">
            
            <!-- Success Message -->
            @if (session('status'))
                <div x-data="{ show: true }" 
                     x-show="show" 
                     x-transition
                     x-init="setTimeout(() => show = false, 5000)"
                     class="mb-6 bg-green-50 dark:bg-green-900/20 border-l-4 border-green-500 p-4 rounded-lg shadow-md">
                    <div class="flex items-center">
                        <i class="fas fa-check-circle text-green-500 text-xl mr-3"></i>
                        <p class="text-green-800 dark:text-green-300 font-medium">
                            @if (session('status') === 'two-factor-authentication-enabled')
                                {{ __('Two factor authentication has been enabled. Please confirm it by entering a code from your authenticator application.') }}
                            @elseif (session('status') === 'two-factor-authentication-disabled')
                                {{ __('Two factor authentication has been disabled.') }}
                            @elseif (session('status') === 'two-factor-authentication-confirmed')
                                {{ __('Two factor authentication has been confirmed and is now active.') }}
                            @else
                                {{ session('status') }}
                            @endif
                        </p>
                    </div>
                </div>
            @endif

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                
                <!-- Left Column - Main Forms (2/3 width) -->
                <div class="lg:col-span-2 space-y-6">
                    
                    <!-- Profile Information Card -->
                    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg overflow-hidden border border-gray-100 dark:border-gray-700 hover:shadow-xl transition-shadow duration-300">
                        <div class="bg-gradient-to-r from-blue-500 to-indigo-600 p-6">
                            <div class="flex items-center">
                                <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center mr-4">
                                    <i class="fas fa-user text-white text-xl"></i>
                                </div>
                                <div>
                                    <h2 class="text-2xl font-bold text-white">
                                        {{ __('Profile Information') }}
                                    </h2>
                                    <p class="text-blue-100 text-sm mt-1">
                                        Mettez à jour vos informations personnelles
                                    </p>
                                </div>
                            </div>
                        </div>
                        <div class="p-8">
                            @include('profile.partials.update-profile-information-form')
                        </div>
                    </div>

                    <!-- Update Password Card -->
                    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg overflow-hidden border border-gray-100 dark:border-gray-700 hover:shadow-xl transition-shadow duration-300">
                        <div class="bg-gradient-to-r from-purple-500 to-pink-600 p-6">
                            <div class="flex items-center">
                                <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center mr-4">
                                    <i class="fas fa-lock text-white text-xl"></i>
                                </div>
                                <div>
                                    <h2 class="text-2xl font-bold text-white">
                                        {{ __('Update Password') }}
                                    </h2>
                                    <p class="text-purple-100 text-sm mt-1">
                                        Assurez-vous que votre compte reste sécurisé
                                    </p>
                                </div>
                            </div>
                        </div>
                        <div class="p-8">
                            @include('profile.partials.update-password-form')
                        </div>
                    </div>

                    <!-- Two Factor Authentication Card -->
                    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg overflow-hidden border border-gray-100 dark:border-gray-700 hover:shadow-xl transition-shadow duration-300">
                        <div class="bg-gradient-to-r from-green-500 to-emerald-600 p-6">
                            <div class="flex items-center">
                                <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center mr-4">
                                    <i class="fas fa-shield-alt text-white text-xl"></i>
                                </div>
                                <div>
                                    <h2 class="text-2xl font-bold text-white">
                                        {{ __('Two Factor Authentication') }}
                                    </h2>
                                    <p class="text-green-100 text-sm mt-1">
                                        Ajoutez une couche de sécurité supplémentaire à votre compte
                                    </p>
                                </div>
                            </div>
                        </div>
                        <div class="p-8">
                            @include('profile.partials.two-factor-authentication-form')
                        </div>
                    </div>

                </div>

                <!-- Right Column - Danger Zone (1/3 width) -->
                <div class="lg:col-span-1">
                    
                    <!-- Account Security Tips -->
                    <div class="bg-blue-50 dark:bg-blue-900/20 rounded-2xl p-6 mb-6 border border-blue-200 dark:border-blue-800">
                        <div class="flex items-center mb-4">
                            <i class="fas fa-shield-alt text-blue-600 dark:text-blue-400 text-2xl mr-3"></i>
                            <h3 class="text-lg font-bold text-blue-900 dark:text-blue-100">
                                Conseils de Sécurité
                            </h3>
                        </div>
                        <ul class="space-y-3 text-sm text-blue-800 dark:text-blue-200">
                            <li class="flex items-start">
                                <i class="fas fa-check-circle text-blue-600 mr-2 mt-1"></i>
                                <span>Utilisez un mot de passe fort (12+ caractères)</span>
                            </li>
                            <li class="flex items-start">
                                <i class="fas fa-check-circle text-blue-600 mr-2 mt-1"></i>
                                <span>Activez la vérification en deux étapes</span>
                            </li>
                            <li class="flex items-start">
                                <i class="fas fa-check-circle text-blue-600 mr-2 mt-1"></i>
                                <span>Changez votre mot de passe régulièrement</span>
                            </li>
                            <li class="flex items-start">
                                <i class="fas fa-check-circle text-blue-600 mr-2 mt-1"></i>
                                <span>Ne partagez jamais vos identifiants</span>
                            </li>
                        </ul>
                    </div>

                    <!-- Delete Account Card -->
                    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg overflow-hidden border border-red-200 dark:border-red-900 hover:shadow-xl transition-shadow duration-300">
                        <div class="bg-gradient-to-r from-red-500 to-red-700 p-6">
                            <div class="flex items-center">
                                <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center mr-4">
                                    <i class="fas fa-exclamation-triangle text-white text-xl"></i>
                                </div>
                                <div>
                                    <h2 class="text-2xl font-bold text-white">
                                        {{ __('Danger Zone') }}
                                    </h2>
                                    <p class="text-red-100 text-sm mt-1">
                                        Action irréversible
                                    </p>
                                </div>
                            </div>
                        </div>
                        <div class="p-8">
                            @include('profile.partials.delete-user-form')
                        </div>
                    </div>

                </div>

            </div>
        </main>

        <!-- Footer -->
        <footer class="bg-white dark:bg-gray-800 border-t border-gray-200 dark:border-gray-700 mt-12">
            <div class="max-w-7xl mx-auto px-6 py-6">
                <p class="text-center text-sm text-gray-600 dark:text-gray-400">
                    <i class="fas fa-shield-alt mr-2"></i>
                    SmartDataVault - Vos données sont sécurisées avec un chiffrement de niveau militaire
                </p>
            </div>
        </footer>

    </div>

    <!-- Include Info/Support Modal Component -->
    <x-info-support-modal />

    <script>
        // Dark mode toggle (optional)
        function toggleDarkMode() {
            document.documentElement.classList.toggle('dark');
            localStorage.setItem('darkMode', document.documentElement.classList.contains('dark'));
        }

        // Load dark mode preference
        if (localStorage.getItem('darkMode') === 'true') {
            document.documentElement.classList.add('dark');
        }

        // Info Modal Functions
        function openInfoModal() {
            const modal = document.getElementById('infoModal');
            const modalContent = document.getElementById('infoModalContent');
            
            if (modal) {
                modal.classList.remove('hidden');
                // Petit délai pour l'animation
                setTimeout(() => {
                    modal.classList.add('opacity-100');
                    modalContent.classList.remove('scale-95');
                    modalContent.classList.add('scale-100');
                }, 10);
            }
        }

        function closeInfoModal() {
            const modal = document.getElementById('infoModal');
            const modalContent = document.getElementById('infoModalContent');
            
            if (modal) {
                modal.classList.remove('opacity-100');
                modalContent.classList.remove('scale-100');
                modalContent.classList.add('scale-95');
                
                // Attendre la fin de l'animation avant de cacher
                setTimeout(() => {
                    modal.classList.add('hidden');
                }, 300);
            }
        }

        // Fermer le modal en cliquant en dehors
        document.addEventListener('click', function(event) {
            const modal = document.getElementById('infoModal');
            const modalContent = document.getElementById('infoModalContent');
            
            if (modal && event.target === modal) {
                closeInfoModal();
            }
        });

        // Fermer le modal avec la touche Échap
        document.addEventListener('keydown', function(event) {
            if (event.key === 'Escape') {
                const modal = document.getElementById('infoModal');
                if (modal && !modal.classList.contains('hidden')) {
                    closeInfoModal();
                }
            }
        });
    </script>

</body>
</html>