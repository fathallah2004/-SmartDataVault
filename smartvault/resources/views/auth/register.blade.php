<x-guest-layout>
    <!-- Header -->
    <div class="text-center mb-8">
        <h2 class="text-3xl font-bold text-gray-900 dark:text-gray-100 mb-2">
            <i class="fas fa-user-plus mr-2 text-indigo-600"></i>
            {{ __('Create Account') }}
        </h2>
        <p class="text-gray-600 dark:text-gray-400">
            {{ __('Join SmartDataVault and secure your data') }}
        </p>
    </div>

    <form method="POST" action="{{ route('register') }}" class="space-y-6">
        @csrf

        <!-- Name -->
        <div>
            <label for="name" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                <i class="fas fa-user mr-2 text-indigo-600"></i>
                {{ __('Full Name') }}
            </label>
            <div class="relative">
                <input 
                    id="name" 
                    name="name" 
                    type="text" 
                    value="{{ old('name') }}" 
                    required 
                    autofocus 
                    autocomplete="name"
                    class="w-full px-4 py-3 pl-12 border-2 border-gray-300 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:text-gray-100 transition-all duration-300"
                    placeholder="John Doe"
                />
                <i class="fas fa-user absolute left-4 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
            </div>
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>

        <!-- Email Address -->
        <div>
            <label for="email" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                <i class="fas fa-envelope mr-2 text-indigo-600"></i>
                {{ __('Email Address') }}
            </label>
            <div class="relative">
                <input 
                    id="email" 
                    name="email" 
                    type="email" 
                    value="{{ old('email') }}" 
                    required 
                    autocomplete="username"
                    class="w-full px-4 py-3 pl-12 border-2 border-gray-300 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:text-gray-100 transition-all duration-300"
                    placeholder="your@email.com"
                />
                <i class="fas fa-envelope absolute left-4 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
            </div>
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div>
            <label for="password" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                <i class="fas fa-lock mr-2 text-indigo-600"></i>
                {{ __('Password') }}
            </label>
            <div class="relative">
                <input 
                    id="password" 
                    name="password" 
                    type="password" 
                    required 
                    autocomplete="new-password"
                    class="w-full px-4 py-3 pl-12 border-2 border-gray-300 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:text-gray-100 transition-all duration-300"
                    placeholder="••••••••"
                />
                <i class="fas fa-lock absolute left-4 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
            </div>
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Confirm Password -->
        <div>
            <label for="password_confirmation" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                <i class="fas fa-lock mr-2 text-indigo-600"></i>
                {{ __('Confirm Password') }}
            </label>
            <div class="relative">
                <input 
                    id="password_confirmation" 
                    name="password_confirmation" 
                    type="password" 
                    required 
                    autocomplete="new-password"
                    class="w-full px-4 py-3 pl-12 border-2 border-gray-300 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:text-gray-100 transition-all duration-300"
                    placeholder="••••••••"
                />
                <i class="fas fa-lock absolute left-4 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
            </div>
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <!-- Submit Button -->
        <button 
            type="submit"
            class="w-full bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 text-white font-bold py-4 px-6 rounded-xl shadow-lg hover:shadow-xl transition-all duration-300 transform hover:scale-105 flex items-center justify-center gap-2"
        >
            <i class="fas fa-user-plus"></i>
            {{ __('Create Account') }}
        </button>

        <!-- Login Link -->
        <div class="text-center pt-4 border-t border-gray-200 dark:border-gray-700">
            <p class="text-sm text-gray-600 dark:text-gray-400">
                {{ __('Already have an account?') }}
                <a href="{{ route('login') }}" class="text-indigo-600 dark:text-indigo-400 hover:text-indigo-800 dark:hover:text-indigo-300 font-semibold ml-1 transition-colors duration-300">
                    {{ __('Sign in') }}
                </a>
            </p>
        </div>
    </form>
</x-guest-layout>
