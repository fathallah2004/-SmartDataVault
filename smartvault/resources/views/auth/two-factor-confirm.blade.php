<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('Confirm Two Factor Authentication') }} - SmartDataVault</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gradient-to-br from-blue-50 via-indigo-50 to-purple-50 dark:from-gray-900 dark:via-gray-800 dark:to-gray-900 min-h-screen flex items-center justify-center px-4">
    
    <div class="max-w-md w-full">
        <!-- Logo/Header -->
        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center w-20 h-20 bg-gradient-to-br from-green-600 to-emerald-600 rounded-2xl shadow-lg mb-4">
                <i class="fas fa-shield-alt text-white text-3xl"></i>
            </div>
            <h1 class="text-3xl font-bold text-gray-900 dark:text-gray-100 mb-2">
                {{ __('Confirm Two Factor Authentication') }}
            </h1>
            <p class="text-gray-600 dark:text-gray-400">
                {{ __('Please confirm access to your account by entering the authentication code provided by your authenticator application.') }}
            </p>
        </div>

        <!-- Card -->
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-2xl p-8 border border-gray-100 dark:border-gray-700">
            
            @if (session('status'))
                <div class="mb-6 bg-green-50 dark:bg-green-900/20 border-l-4 border-green-500 p-4 rounded-lg">
                    <div class="flex items-center">
                        <i class="fas fa-check-circle text-green-500 text-xl mr-3"></i>
                        <p class="text-green-800 dark:text-green-300 font-medium">{{ session('status') }}</p>
                    </div>
                </div>
            @endif

            @if ($errors->any())
                <div class="mb-6 bg-red-50 dark:bg-red-900/20 border-l-4 border-red-500 p-4 rounded-lg">
                    <div class="flex items-center">
                        <i class="fas fa-exclamation-circle text-red-500 text-xl mr-3"></i>
                        <div>
                            @foreach ($errors->all() as $error)
                                <p class="text-red-800 dark:text-red-300">{{ $error }}</p>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif

            <form method="POST" action="{{ route('two-factor.confirm') }}" class="space-y-6">
                @csrf

                <!-- Code Input -->
                <div>
                    <label for="code" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                        <i class="fas fa-key mr-2"></i>
                        {{ __('Authentication Code') }}
                    </label>
                    <input 
                        id="code" 
                        name="code" 
                        type="text" 
                        inputmode="numeric" 
                        pattern="[0-9]*"
                        autofocus
                        autocomplete="one-time-code"
                        class="w-full px-4 py-3 border-2 border-gray-300 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-green-500 dark:bg-gray-700 dark:text-gray-100 text-center text-2xl tracking-widest font-mono"
                        placeholder="000000"
                        maxlength="6"
                    />
                    <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">
                        {{ __('Enter the six-digit code from your authenticator application to confirm two factor authentication.') }}
                    </p>
                </div>

                <!-- Submit Button -->
                <button 
                    type="submit"
                    class="w-full bg-gradient-to-r from-green-600 to-emerald-600 hover:from-green-700 hover:to-emerald-700 text-white font-bold py-4 px-6 rounded-xl transition-all duration-300 hover:scale-105 hover:shadow-lg flex items-center justify-center gap-2"
                >
                    <i class="fas fa-check-circle"></i>
                    {{ __('Confirm') }}
                </button>
            </form>

            <!-- Back to Profile -->
            <div class="mt-6 text-center">
                <a href="{{ route('profile.edit') }}" class="text-sm text-green-600 dark:text-green-400 hover:text-green-800 dark:hover:text-green-300">
                    <i class="fas fa-arrow-left mr-2"></i>
                    {{ __('Back to Profile') }}
                </a>
            </div>
        </div>

        <!-- Footer -->
        <div class="mt-8 text-center text-sm text-gray-600 dark:text-gray-400">
            <p>
                <i class="fas fa-shield-alt mr-2"></i>
                {{ __('SmartDataVault - Secure Authentication') }}
            </p>
        </div>
    </div>

    <script>
        // Auto-focus and format code input
        const codeInput = document.getElementById('code');
        if (codeInput) {
            codeInput.addEventListener('input', function(e) {
                // Only allow numbers
                this.value = this.value.replace(/[^0-9]/g, '');
            });
        }
    </script>

</body>
</html>

