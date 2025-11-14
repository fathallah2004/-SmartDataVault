<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }} - SmartDataVault</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        <script src="https://cdn.tailwindcss.com"></script>
    </head>
    <body class="font-sans text-gray-900 antialiased">
        <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 relative overflow-hidden" 
             style="background: linear-gradient(135deg, #667eea 0%, #764ba2 25%, #f093fb 50%, #4facfe 75%, #00f2fe 100%); background-size: 400% 400%; animation: gradient 15s ease infinite;">
            
            <!-- Animated Background Overlay -->
            <div class="absolute inset-0 bg-black/20 dark:bg-black/40"></div>
            
            <!-- Decorative Elements -->
            <div class="absolute top-0 left-0 w-72 h-72 bg-blue-400/30 rounded-full mix-blend-multiply filter blur-xl opacity-70 animate-blob"></div>
            <div class="absolute top-0 right-0 w-72 h-72 bg-purple-400/30 rounded-full mix-blend-multiply filter blur-xl opacity-70 animate-blob animation-delay-2000"></div>
            <div class="absolute -bottom-8 left-20 w-72 h-72 bg-pink-400/30 rounded-full mix-blend-multiply filter blur-xl opacity-70 animate-blob animation-delay-4000"></div>

            <!-- Content -->
            <div class="relative z-10 w-full">
                <!-- Logo Section -->
                <div class="text-center mb-6">
                    <a href="/" class="inline-block">
                        <div class="inline-flex items-center justify-center w-20 h-20 bg-white/20 backdrop-blur-lg rounded-2xl shadow-2xl mb-4 border border-white/30 hover:scale-110 transition-transform duration-300">
                            <i class="fas fa-shield-alt text-white text-3xl"></i>
                        </div>
                    </a>
                    <h1 class="text-3xl font-bold text-white mb-2 drop-shadow-lg">
                        SmartDataVault
                    </h1>
                    <p class="text-white/90 text-sm drop-shadow">
                        {{ __('Secure Your Data with Military-Grade Encryption') }}
                    </p>
                </div>

                <!-- Form Card -->
                <div class="w-full sm:max-w-md mx-auto px-6">
                    <div class="bg-white/95 dark:bg-gray-800/95 backdrop-blur-xl shadow-2xl rounded-2xl border border-white/20 dark:border-gray-700/50 overflow-hidden">
                        <div class="px-8 py-8">
                            {{ $slot }}
                        </div>
                    </div>
                </div>

                <!-- Footer -->
                <div class="text-center mt-8">
                    <p class="text-white/80 text-sm drop-shadow">
                        <i class="fas fa-lock mr-2"></i>
                        {{ __('Protected by Advanced Security') }}
                    </p>
                </div>
            </div>
        </div>

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
        </style>
    </body>
</html>
