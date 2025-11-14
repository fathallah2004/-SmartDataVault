<x-guest-layout>
    <!-- Header -->
    <div class="text-center mb-8">
        <h2 class="text-3xl font-bold text-gray-900 dark:text-gray-100 mb-2">
            <i class="fas fa-key mr-2 text-indigo-600"></i>
            {{ __('Reset Password') }}
        </h2>
        <p class="text-gray-600 dark:text-gray-400">
            {{ __('Click the button below to receive a new password via email.') }}
        </p>
        @if(session('login_email'))
            <div class="mt-4 p-3 bg-blue-50 dark:bg-blue-900/20 rounded-lg border border-blue-200 dark:border-blue-800">
                <p class="text-sm text-blue-800 dark:text-blue-300">
                    <i class="fas fa-envelope mr-2"></i>
                    {{ __('Password will be sent to:') }} <strong>{{ session('login_email') }}</strong>
                </p>
            </div>
        @else
            <div class="mt-4 p-3 bg-yellow-50 dark:bg-yellow-900/20 rounded-lg border border-yellow-200 dark:border-yellow-800">
                <p class="text-sm text-yellow-800 dark:text-yellow-300">
                    <i class="fas fa-info-circle mr-2"></i>
                    {{ __('Please try logging in first, then click on "Forgot Password" if you need a new password.') }}
                </p>
            </div>
        @endif
    </div>

    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <!-- Email Configuration Notice -->
    @if(config('mail.default') === 'log')
        <div class="mb-6 bg-yellow-50 dark:bg-yellow-900/20 border-l-4 border-yellow-500 p-4 rounded-lg">
            <div class="flex items-start space-x-3">
                <i class="fas fa-info-circle text-yellow-600 dark:text-yellow-400 text-lg mt-0.5"></i>
                <div>
                    <p class="text-sm font-semibold text-yellow-900 dark:text-yellow-100 mb-1">
                        {{ __('Email Configuration Notice') }}
                    </p>
                    <p class="text-sm text-yellow-800 dark:text-yellow-200">
                        {{ __('Emails are currently being logged instead of sent. Check storage/logs/laravel.log for the email content. To send real emails, configure MAIL_MAILER in your .env file.') }}
                    </p>
                </div>
            </div>
        </div>
    @endif

    @if($errors->any())
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

    <form method="POST" action="{{ route('password.email') }}" class="space-y-6">
        @csrf

        <!-- Submit Button -->
        <button 
            type="submit"
            class="w-full bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 text-white font-bold py-4 px-6 rounded-xl shadow-lg hover:shadow-xl transition-all duration-300 transform hover:scale-105 flex items-center justify-center gap-2"
        >
            <i class="fas fa-paper-plane"></i>
            {{ __('Send New Password') }}
        </button>

        <!-- Back to Login -->
        <div class="text-center pt-4 border-t border-gray-200 dark:border-gray-700">
            <a href="{{ route('login') }}" class="text-sm text-indigo-600 dark:text-indigo-400 hover:text-indigo-800 dark:hover:text-indigo-300 font-medium transition-colors duration-300">
                <i class="fas fa-arrow-left mr-1"></i>
                {{ __('Back to Login') }}
            </a>
        </div>
    </form>
</x-guest-layout>
