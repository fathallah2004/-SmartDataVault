<section>
    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="post" action="{{ route('profile.update') }}" class="space-y-6">
        @csrf
        @method('patch')

        <!-- Name Input -->
        <div class="group">
            <label for="name" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                <i class="fas fa-user mr-2 text-blue-500"></i>{{ __('Name') }}
            </label>
            <div class="relative">
                <input 
                    id="name" 
                    name="name" 
                    type="text" 
                    class="w-full px-4 py-3 border-2 border-gray-200 dark:border-gray-600 rounded-xl 
                           bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100
                           focus:border-blue-500 dark:focus:border-blue-400 focus:ring-4 focus:ring-blue-500/20
                           transition-all duration-200 placeholder-gray-400 dark:placeholder-gray-500"
                    value="{{ old('name', $user->name) }}" 
                    required 
                    autofocus 
                    autocomplete="name"
                    placeholder="Votre nom complet" />
                <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                    <i class="fas fa-id-card text-gray-400"></i>
                </div>
            </div>
            @if($errors->get('name'))
                <p class="mt-2 text-sm text-red-600 dark:text-red-400 flex items-center">
                    <i class="fas fa-exclamation-circle mr-2"></i>
                    {{ $errors->get('name')[0] }}
                </p>
            @endif
        </div>

        <!-- Email Input -->
        <div class="group">
            <label for="email" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                <i class="fas fa-envelope mr-2 text-purple-500"></i>{{ __('Email') }}
            </label>
            <div class="relative">
                <input 
                    id="email" 
                    name="email" 
                    type="email" 
                    class="w-full px-4 py-3 border-2 border-gray-200 dark:border-gray-600 rounded-xl 
                           bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100
                           focus:border-purple-500 dark:focus:border-purple-400 focus:ring-4 focus:ring-purple-500/20
                           transition-all duration-200 placeholder-gray-400 dark:placeholder-gray-500"
                    value="{{ old('email', $user->email) }}" 
                    required 
                    autocomplete="username"
                    placeholder="votre.email@exemple.com" />
                <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                    <i class="fas fa-at text-gray-400"></i>
                </div>
            </div>
            @if($errors->get('email'))
                <p class="mt-2 text-sm text-red-600 dark:text-red-400 flex items-center">
                    <i class="fas fa-exclamation-circle mr-2"></i>
                    {{ $errors->get('email')[0] }}
                </p>
            @endif

            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                <div class="mt-3 p-4 bg-yellow-50 dark:bg-yellow-900/20 border-l-4 border-yellow-500 rounded-lg">
                    <div class="flex items-start">
                        <i class="fas fa-exclamation-triangle text-yellow-600 dark:text-yellow-400 mt-1 mr-3"></i>
                        <div class="flex-1">
                            <p class="text-sm text-yellow-800 dark:text-yellow-200 font-medium">
                                {{ __('Your email address is unverified.') }}
                            </p>
                            <button 
                                form="send-verification" 
                                class="mt-2 text-sm text-yellow-700 dark:text-yellow-300 underline hover:text-yellow-900 dark:hover:text-yellow-100 font-semibold transition-colors">
                                {{ __('Click here to re-send the verification email.') }}
                            </button>
                        </div>
                    </div>

                    @if (session('status') === 'verification-link-sent')
                        <div class="mt-3 flex items-center text-sm text-green-700 dark:text-green-300 font-medium">
                            <i class="fas fa-check-circle mr-2"></i>
                            {{ __('A new verification link has been sent to your email address.') }}
                        </div>
                    @endif
                </div>
            @endif
        </div>

        <!-- Submit Button -->
        <div class="flex items-center justify-between pt-4 border-t border-gray-200 dark:border-gray-700">
            <button 
                type="submit"
                class="px-8 py-3 bg-gradient-to-r from-blue-500 to-indigo-600 text-white font-semibold rounded-xl
                       hover:from-blue-600 hover:to-indigo-700 focus:ring-4 focus:ring-blue-500/50
                       transform hover:scale-105 transition-all duration-200 shadow-lg hover:shadow-xl
                       flex items-center group">
                <i class="fas fa-save mr-2 group-hover:rotate-12 transition-transform"></i>
                {{ __('Save Changes') }}
            </button>

            @if (session('status') === 'profile-updated')
                <div
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 3000)"
                    class="flex items-center px-4 py-2 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg">
                    <i class="fas fa-check-circle text-green-600 dark:text-green-400 mr-2"></i>
                    <span class="text-sm font-medium text-green-700 dark:text-green-300">{{ __('Saved.') }}</span>
                </div>
            @endif
        </div>
    </form>
</section>