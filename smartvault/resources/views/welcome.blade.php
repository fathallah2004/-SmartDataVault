<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>SmartDataVault - Sécurisez vos données avec un chiffrement de niveau militaire</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

        <!-- Styles / Scripts -->
            @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://cdn.tailwindcss.com"></script>
    </head>
<body class="min-h-screen relative overflow-hidden" 
      style="background: linear-gradient(135deg, #667eea 0%, #764ba2 25%, #f093fb 50%, #4facfe 75%, #00f2fe 100%); background-size: 400% 400%; animation: gradient 15s ease infinite;">
    
    <!-- Animated Background Overlay -->
    <div class="absolute inset-0 bg-black/10 dark:bg-black/30"></div>
    
    <!-- Decorative Elements -->
    <div class="absolute top-0 left-0 w-96 h-96 bg-blue-400/20 rounded-full mix-blend-multiply filter blur-3xl opacity-70 animate-blob"></div>
    <div class="absolute top-0 right-0 w-96 h-96 bg-purple-400/20 rounded-full mix-blend-multiply filter blur-3xl opacity-70 animate-blob animation-delay-2000"></div>
    <div class="absolute -bottom-20 left-20 w-96 h-96 bg-pink-400/20 rounded-full mix-blend-multiply filter blur-3xl opacity-70 animate-blob animation-delay-4000"></div>
    <div class="absolute bottom-0 right-20 w-96 h-96 bg-cyan-400/20 rounded-full mix-blend-multiply filter blur-3xl opacity-70 animate-blob animation-delay-6000"></div>

    <!-- Hero Section -->
    <div class="relative z-10 max-w-7xl mx-auto px-6 py-20">
        <div class="text-center mb-16">
            <!-- Main Heading -->
            <h1 class="text-6xl md:text-7xl lg:text-8xl font-bold text-white mb-6 drop-shadow-2xl leading-tight">
                <span class="block">Sécurisez vos</span>
                <span class="block bg-gradient-to-r from-yellow-200 via-pink-200 to-cyan-200 bg-clip-text text-transparent">
                    Données
                                </span>
            </h1>
            
            <p class="text-xl md:text-2xl text-white/90 mb-8 max-w-3xl mx-auto drop-shadow-lg leading-relaxed">
                Chiffrement de niveau militaire • Stockage sécurisé • Protection maximale
            </p>
            
            <!-- CTA Buttons -->
            <div class="flex flex-col sm:flex-row items-center justify-center gap-4 mb-12">
                @auth
                    <a href="{{ route('dashboard') }}" class="group px-8 py-4 bg-white text-indigo-600 font-bold rounded-xl shadow-2xl hover:shadow-3xl transition-all duration-300 transform hover:scale-105 flex items-center gap-2">
                        <i class="fas fa-rocket"></i>
                        <span>Accéder au Dashboard</span>
                        <i class="fas fa-arrow-right group-hover:translate-x-1 transition-transform"></i>
                    </a>
                @else
                    <a href="{{ route('register') }}" class="group px-8 py-4 bg-white text-indigo-600 font-bold rounded-xl shadow-2xl hover:shadow-3xl transition-all duration-300 transform hover:scale-105 flex items-center gap-2">
                        <i class="fas fa-rocket"></i>
                        <span>Commencer gratuitement</span>
                        <i class="fas fa-arrow-right group-hover:translate-x-1 transition-transform"></i>
                    </a>
                    <a href="{{ route('login') }}" class="px-8 py-4 bg-white/20 backdrop-blur-lg text-white font-bold rounded-xl border-2 border-white/30 hover:bg-white/30 transition-all duration-300 shadow-lg">
                        <i class="fas fa-sign-in-alt mr-2"></i>Se connecter
                    </a>
                @endauth
            </div>
        </div>

        <!-- Features Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mt-20">
            <!-- Feature 1 -->
            <div class="bg-white/10 backdrop-blur-xl rounded-2xl p-8 border border-white/20 shadow-2xl hover:bg-white/15 transition-all duration-300 transform hover:scale-105">
                <div class="w-16 h-16 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-xl flex items-center justify-center mb-6 shadow-lg">
                    <i class="fas fa-lock text-white text-2xl"></i>
                </div>
                <h3 class="text-2xl font-bold text-white mb-4">Chiffrement Militaire</h3>
                <p class="text-white/80 leading-relaxed">
                    Vos fichiers sont protégés avec un chiffrement AES-256, le même standard utilisé par les militaires et les banques.
                </p>
            </div>

            <!-- Feature 2 -->
            <div class="bg-white/10 backdrop-blur-xl rounded-2xl p-8 border border-white/20 shadow-2xl hover:bg-white/15 transition-all duration-300 transform hover:scale-105">
                <div class="w-16 h-16 bg-gradient-to-br from-purple-500 to-pink-600 rounded-xl flex items-center justify-center mb-6 shadow-lg">
                    <i class="fas fa-cloud-upload-alt text-white text-2xl"></i>
                </div>
                <h3 class="text-2xl font-bold text-white mb-4">Stockage Sécurisé</h3>
                <p class="text-white/80 leading-relaxed">
                    Tous vos fichiers sont stockés de manière sécurisée avec un accès uniquement par vous. Vos données restent privées.
                </p>
            </div>

            <!-- Feature 3 -->
            <div class="bg-white/10 backdrop-blur-xl rounded-2xl p-8 border border-white/20 shadow-2xl hover:bg-white/15 transition-all duration-300 transform hover:scale-105">
                <div class="w-16 h-16 bg-gradient-to-br from-green-500 to-emerald-600 rounded-xl flex items-center justify-center mb-6 shadow-lg">
                    <i class="fas fa-shield-alt text-white text-2xl"></i>
                </div>
                <h3 class="text-2xl font-bold text-white mb-4">2FA Intégré</h3>
                <p class="text-white/80 leading-relaxed">
                    Authentification à deux facteurs pour une sécurité maximale. Protégez votre compte avec un code unique.
                </p>
            </div>

            <!-- Feature 4 -->
            <div class="bg-white/10 backdrop-blur-xl rounded-2xl p-8 border border-white/20 shadow-2xl hover:bg-white/15 transition-all duration-300 transform hover:scale-105">
                <div class="w-16 h-16 bg-gradient-to-br from-cyan-500 to-blue-600 rounded-xl flex items-center justify-center mb-6 shadow-lg">
                    <i class="fas fa-key text-white text-2xl"></i>
                </div>
                <h3 class="text-2xl font-bold text-white mb-4">Clés Privées</h3>
                <p class="text-white/80 leading-relaxed">
                    Chaque fichier est chiffré avec une clé unique. Seul vous avez accès à vos données.
                </p>
            </div>

            <!-- Feature 5 -->
            <div class="bg-white/10 backdrop-blur-xl rounded-2xl p-8 border border-white/20 shadow-2xl hover:bg-white/15 transition-all duration-300 transform hover:scale-105">
                <div class="w-16 h-16 bg-gradient-to-br from-orange-500 to-red-600 rounded-xl flex items-center justify-center mb-6 shadow-lg">
                    <i class="fas fa-bolt text-white text-2xl"></i>
                </div>
                <h3 class="text-2xl font-bold text-white mb-4">Performance</h3>
                <p class="text-white/80 leading-relaxed">
                    Chiffrement et déchiffrement rapides. Accédez à vos fichiers en quelques secondes.
                </p>
            </div>

            <!-- Feature 6 -->
            <div class="bg-white/10 backdrop-blur-xl rounded-2xl p-8 border border-white/20 shadow-2xl hover:bg-white/15 transition-all duration-300 transform hover:scale-105">
                <div class="w-16 h-16 bg-gradient-to-br from-pink-500 to-rose-600 rounded-xl flex items-center justify-center mb-6 shadow-lg">
                    <i class="fas fa-user-shield text-white text-2xl"></i>
                </div>
                <h3 class="text-2xl font-bold text-white mb-4">Confidentialité</h3>
                <p class="text-white/80 leading-relaxed">
                    Vos données ne sont jamais partagées. Nous ne pouvons pas accéder à vos fichiers chiffrés.
                </p>
            </div>
        </div>

        <!-- Stats Section -->
        <div class="mt-20 grid grid-cols-2 md:grid-cols-4 gap-6">
            <div class="bg-white/10 backdrop-blur-xl rounded-xl p-6 border border-white/20 text-center">
                <div class="text-4xl font-bold text-white mb-2">256-bit</div>
                <div class="text-white/80 text-sm">Chiffrement AES</div>
            </div>
            <div class="bg-white/10 backdrop-blur-xl rounded-xl p-6 border border-white/20 text-center">
                <div class="text-4xl font-bold text-white mb-2">100%</div>
                <div class="text-white/80 text-sm">Sécurisé</div>
            </div>
            <div class="bg-white/10 backdrop-blur-xl rounded-xl p-6 border border-white/20 text-center">
                <div class="text-4xl font-bold text-white mb-2">2FA</div>
                <div class="text-white/80 text-sm">Authentification</div>
            </div>
            <div class="bg-white/10 backdrop-blur-xl rounded-xl p-6 border border-white/20 text-center">
                <div class="text-4xl font-bold text-white mb-2">24/7</div>
                <div class="text-white/80 text-sm">Disponible</div>
            </div>
        </div>

        <!-- Final CTA -->
        <div class="mt-20 text-center">
            <div class="bg-white/10 backdrop-blur-xl rounded-2xl p-12 border border-white/20 shadow-2xl max-w-4xl mx-auto">
                <h2 class="text-4xl font-bold text-white mb-4">
                    Prêt à sécuriser vos données ?
                </h2>
                <p class="text-xl text-white/80 mb-8">
                    Rejoignez SmartDataVault aujourd'hui et protégez vos fichiers avec un chiffrement de niveau militaire.
                </p>
                @guest
                    <a href="{{ route('register') }}" class="inline-flex items-center px-8 py-4 bg-white text-indigo-600 font-bold rounded-xl shadow-2xl hover:shadow-3xl transition-all duration-300 transform hover:scale-105">
                        <i class="fas fa-user-plus mr-2"></i>
                        Créer un compte gratuit
                        <i class="fas fa-arrow-right ml-2"></i>
                    </a>
                @endguest
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="relative z-10 mt-20 py-8 border-t border-white/20">
        <div class="max-w-7xl mx-auto px-6 text-center">
            <p class="text-white/80 text-sm">
                <i class="fas fa-shield-alt mr-2"></i>
                SmartDataVault - Vos données sont sécurisées avec un chiffrement de niveau militaire
            </p>
            <p class="text-white/60 text-xs mt-2">
                © {{ date('Y') }} SmartDataVault. Tous droits réservés.
            </p>
        </div>
    </footer>

    <style>
        @keyframes gradient {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }

        @keyframes blob {
            0% { transform: translate(0px, 0px) scale(1); }
            33% { transform: translate(30px, -50px) scale(1.1); }
            66% { transform: translate(-20px, 20px) scale(0.9); }
            100% { transform: translate(0px, 0px) scale(1); }
        }

        .animate-blob {
            animation: blob 7s infinite;
        }

        .animation-delay-2000 {
            animation-delay: 2s;
        }

        .animation-delay-4000 {
            animation-delay: 4s;
        }

        .animation-delay-6000 {
            animation-delay: 6s;
        }
    </style>
    </body>
</html>
