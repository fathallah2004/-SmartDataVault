<section>
    @if (! auth()->user()->two_factor_secret)
        <!-- 2FA Not Enabled State -->
        <div class="space-y-6">
            <div class="flex items-start space-x-4 p-6 bg-gradient-to-br from-blue-50 to-indigo-50 dark:from-gray-800 dark:to-gray-700 rounded-xl border border-blue-200 dark:border-blue-800">
                <div class="flex-shrink-0">
                    <div class="w-12 h-12 bg-blue-100 dark:bg-blue-900/30 rounded-xl flex items-center justify-center">
                        <i class="fas fa-shield-alt text-blue-600 dark:text-blue-400 text-xl"></i>
                    </div>
                </div>
                <div class="flex-1">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-2">
                        {{ __('Protect Your Account') }}
                    </h3>
                    <p class="text-sm text-gray-600 dark:text-gray-400 mb-4 leading-relaxed">
                        {{ __('When two factor authentication is enabled, you will be prompted for a secure, random token during authentication. You may retrieve this token from your phone\'s Google Authenticator application.') }}
                    </p>
                    <form method="post" action="{{ route('two-factor.enable') }}">
                        @csrf
                        <button type="submit" class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white font-semibold rounded-xl shadow-lg hover:shadow-xl transition-all duration-300 transform hover:scale-105">
                            <i class="fas fa-lock mr-2"></i>
                            {{ __('Enable Two Factor Authentication') }}
                        </button>
                    </form>
                </div>
            </div>
        </div>

    @elseif (! auth()->user()->two_factor_confirmed_at)
        <!-- 2FA Enabled But Not Confirmed State -->
        <div class="space-y-6">
            <div class="bg-gradient-to-br from-yellow-50 to-amber-50 dark:from-yellow-900/20 dark:to-amber-900/20 border-2 border-yellow-300 dark:border-yellow-700 rounded-xl p-6 shadow-lg">
                <div class="flex items-start space-x-4">
                    <div class="flex-shrink-0">
                        <div class="w-14 h-14 bg-yellow-100 dark:bg-yellow-900/40 rounded-xl flex items-center justify-center animate-pulse">
                            <i class="fas fa-exclamation-triangle text-yellow-600 dark:text-yellow-400 text-2xl"></i>
                        </div>
                    </div>
                    <div class="flex-1">
                        <h3 class="text-lg font-bold text-yellow-900 dark:text-yellow-100 mb-2">
                            {{ __('Confirmation Required') }}
                        </h3>
                        <p class="text-yellow-800 dark:text-yellow-300 font-medium mb-4">
                            {{ __('Two factor authentication has been enabled, but you need to confirm it by entering a code from your authenticator application.') }}
                        </p>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6 border border-gray-200 dark:border-gray-700">
                <div class="text-center mb-6">
                    <h4 class="text-md font-semibold text-gray-900 dark:text-gray-100 mb-2">
                        <i class="fas fa-qrcode mr-2 text-indigo-600"></i>
                        {{ __('Scan QR Code') }}
                    </h4>
                    <p class="text-sm text-gray-600 dark:text-gray-400">
                        {{ __('Scan the following QR code using your phone\'s authenticator application, then confirm by entering a code.') }}
                    </p>
                </div>

                <div class="flex justify-center mb-6">
                    <div class="bg-white p-6 rounded-xl shadow-lg border-2 border-gray-200 dark:border-gray-700 transform hover:scale-105 transition-transform duration-300">
                        {!! auth()->user()->twoFactorQrCodeSvg() !!}
                    </div>
                </div>

                <div class="bg-gray-50 dark:bg-gray-900 rounded-xl p-5 mb-6 border border-gray-200 dark:border-gray-700">
                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-3">
                        <i class="fas fa-key mr-2 text-indigo-600"></i>
                        {{ __('Secret Key') }}
                    </label>
                    <div class="px-4 py-3 bg-white dark:bg-gray-800 rounded-lg font-mono text-sm break-all border-2 border-gray-200 dark:border-gray-700 text-gray-900 dark:text-gray-100 shadow-inner">
                        {{ decrypt(auth()->user()->two_factor_secret) }}
                    </div>
                    <p class="mt-2 text-xs text-gray-500 dark:text-gray-400">
                        <i class="fas fa-info-circle mr-1"></i>
                        {{ __('Enter this key manually if you cannot scan the QR code.') }}
                    </p>
                </div>

                <div class="text-center">
                    <a href="{{ route('two-factor.confirmation.show') }}" class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 text-white font-semibold rounded-xl shadow-lg hover:shadow-xl transition-all duration-300 transform hover:scale-105">
                        <i class="fas fa-check-circle mr-2"></i>
                        {{ __('Confirm Two Factor Authentication') }}
                    </a>
                </div>
            </div>
        </div>

    @else
        <!-- 2FA Enabled and Confirmed State -->
        <div class="space-y-6">
            <!-- Status Card -->
            <div class="bg-gradient-to-br from-green-50 via-emerald-50 to-teal-50 dark:from-green-900/20 dark:via-emerald-900/20 dark:to-teal-900/20 border-2 border-green-300 dark:border-green-700 rounded-xl p-6 shadow-lg">
                <div class="flex items-start space-x-4">
                    <div class="flex-shrink-0">
                        <div class="w-16 h-16 bg-gradient-to-br from-green-500 to-emerald-600 rounded-xl flex items-center justify-center shadow-lg">
                            <i class="fas fa-shield-check text-white text-2xl"></i>
                        </div>
                    </div>
                    <div class="flex-1">
                        <h3 class="text-xl font-bold text-green-900 dark:text-green-100 mb-2 flex items-center">
                            <i class="fas fa-check-circle mr-2"></i>
                            {{ __('Two Factor Authentication Active') }}
                        </h3>
                        <p class="text-green-800 dark:text-green-300 font-medium mb-3">
                            {{ __('Two factor authentication is enabled and confirmed.') }}
                        </p>
                        <div class="flex items-center space-x-2 text-sm text-green-700 dark:text-green-400">
                            <i class="fas fa-lock"></i>
                            <span>{{ __('Your account is now protected with two factor authentication. You will be prompted for a code when logging in.') }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recovery Codes Card -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg border-2 border-gray-200 dark:border-gray-700 overflow-hidden">
                <div class="bg-gradient-to-r from-purple-600 to-indigo-600 p-6">
                    <div class="flex items-center space-x-3">
                        <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center">
                            <i class="fas fa-key text-white text-xl"></i>
                        </div>
                        <div>
                            <h4 class="text-xl font-bold text-white">
                                {{ __('Recovery Codes') }}
                            </h4>
                            <p class="text-purple-100 text-sm mt-1">
                                {{ __('Keep these codes safe') }}
                            </p>
                        </div>
                    </div>
                </div>

                <div class="p-6">
                    <div class="bg-amber-50 dark:bg-amber-900/20 border-l-4 border-amber-500 rounded-lg p-4 mb-6">
                        <div class="flex items-start space-x-3">
                            <i class="fas fa-exclamation-triangle text-amber-600 dark:text-amber-400 text-lg mt-0.5"></i>
                            <div>
                                <p class="text-sm font-semibold text-amber-900 dark:text-amber-100 mb-1">
                                    {{ __('Important: Store These Codes Securely') }}
                                </p>
                                <p class="text-sm text-amber-800 dark:text-amber-200">
                                    {{ __('Store these recovery codes in a secure password manager. They can be used to recover access to your account if your two factor authentication device is lost.') }}
                                </p>
                            </div>
                        </div>
                    </div>

                    @if(auth()->user()->two_factor_recovery_codes)
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3 mb-6">
                            @foreach (json_decode(decrypt(auth()->user()->two_factor_recovery_codes), true) as $index => $code)
                                <div class="group relative">
                                    <div class="flex items-center space-x-3 p-4 bg-gradient-to-r from-gray-50 to-gray-100 dark:from-gray-900 dark:to-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 hover:border-indigo-400 dark:hover:border-indigo-600 transition-all duration-300 hover:shadow-md cursor-pointer" onclick="copyToClipboard('{{ $code }}')">
                                        <div class="flex-shrink-0 w-8 h-8 bg-indigo-100 dark:bg-indigo-900/30 rounded-lg flex items-center justify-center">
                                            <span class="text-xs font-bold text-indigo-600 dark:text-indigo-400">{{ $index + 1 }}</span>
                                        </div>
                                        <code class="flex-1 font-mono text-sm text-gray-900 dark:text-gray-100 select-all">
                                            {{ $code }}
                                        </code>
                                        <button 
                                            type="button" 
                                            onclick="event.stopPropagation(); copyToClipboard('{{ $code }}')" 
                                            class="opacity-0 group-hover:opacity-100 transition-opacity duration-300 p-2 text-indigo-600 dark:text-indigo-400 hover:bg-indigo-100 dark:hover:bg-indigo-900/30 rounded-lg"
                                            title="{{ __('Copy code') }}"
                                        >
                                            <i class="fas fa-copy text-sm"></i>
                                        </button>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <div class="flex items-center justify-between p-4 bg-blue-50 dark:bg-blue-900/20 rounded-lg border border-blue-200 dark:border-blue-800">
                            <div class="flex items-center space-x-2 text-sm text-blue-800 dark:text-blue-200">
                                <i class="fas fa-info-circle"></i>
                                <span>{{ __('Click on any code or use the copy button to copy it to clipboard') }}</span>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Disable 2FA Section -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg border-2 border-red-200 dark:border-red-900/50 p-6">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-4">
                        <div class="w-12 h-12 bg-red-100 dark:bg-red-900/30 rounded-xl flex items-center justify-center text-2xl">
                            <span>⚠️</span>
                        </div>
                        <div>
                            <h4 class="text-lg font-semibold text-gray-900 dark:text-gray-100">
                                <span class="mr-2">🔓</span>{{ __('Disable Two Factor Authentication') }}
                            </h4>
                            <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                                <span class="mr-1">🛡️</span>{{ __('Remove the extra security layer from your account') }}
                            </p>
                        </div>
                    </div>
                    <form method="post" action="{{ route('two-factor.disable') }}" class="inline-block">
                        @csrf
                        @method('delete')
                        <button 
                            type="submit" 
                            onclick="return confirm('{{ __('Are you sure you want to disable two factor authentication? This will make your account less secure.') }}')"
                            class="px-6 py-3 bg-gradient-to-r from-red-600 to-red-700 hover:from-red-700 hover:to-red-800 text-white font-semibold rounded-xl shadow-lg hover:shadow-xl transition-all duration-300 transform hover:scale-105"
                        >
                            <span class="mr-2">❌</span>
                            <i class="fas fa-times-circle mr-2"></i>
                            {{ __('Disable') }}
                        </button>
                    </form>
                </div>
            </div>
        </div>
    @endif
</section>

<script>
function copyToClipboard(text) {
    navigator.clipboard.writeText(text).then(function() {
        // Show a temporary success message
        const notification = document.createElement('div');
        notification.className = 'fixed top-4 right-4 bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg z-50 flex items-center space-x-2 animate-fade-in';
        notification.innerHTML = '<i class="fas fa-check-circle"></i><span>{{ __("Code copied!") }}</span>';
        document.body.appendChild(notification);
        
        setTimeout(() => {
            notification.classList.add('animate-fade-out');
            setTimeout(() => notification.remove(), 300);
        }, 2000);
    }).catch(function(err) {
        console.error('Failed to copy: ', err);
        // Show error message
        const notification = document.createElement('div');
        notification.className = 'fixed top-4 right-4 bg-red-500 text-white px-6 py-3 rounded-lg shadow-lg z-50 flex items-center space-x-2 animate-fade-in';
        notification.innerHTML = '<i class="fas fa-exclamation-circle"></i><span>{{ __("Failed to copy code") }}</span>';
        document.body.appendChild(notification);
        
        setTimeout(() => {
            notification.classList.add('animate-fade-out');
            setTimeout(() => notification.remove(), 300);
        }, 2000);
    });
}

// Add CSS animations
const style = document.createElement('style');
style.textContent = `
    @keyframes fade-in {
        from { opacity: 0; transform: translateY(-10px); }
        to { opacity: 1; transform: translateY(0); }
    }
    @keyframes fade-out {
        from { opacity: 1; transform: translateY(0); }
        to { opacity: 0; transform: translateY(-10px); }
    }
    .animate-fade-in {
        animation: fade-in 0.3s ease-out;
    }
    .animate-fade-out {
        animation: fade-out 0.3s ease-out;
    }
`;
document.head.appendChild(style);
</script>

