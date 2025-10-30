<section class="space-y-6">
    <div class="bg-red-50 dark:bg-red-900/10 border-l-4 border-red-500 p-4 rounded-lg">
        <div class="flex items-start">
            <i class="fas fa-exclamation-triangle text-red-600 dark:text-red-400 text-xl mr-3 mt-1"></i>
            <div>
                <h3 class="text-base font-bold text-red-800 dark:text-red-200 mb-2">
                    Supprimer le compte
                </h3>
                <p class="text-sm text-red-700 dark:text-red-300 leading-relaxed">
                    Une fois votre compte supprimé, toutes ses ressources et données seront définitivement effacées. Avant de supprimer votre compte, veuillez télécharger toutes les données ou informations que vous souhaitez conserver.
                </p>
            </div>
        </div>
    </div>

    <button
        x-data=""
        x-on:click.prevent="$dispatch('open-modal', 'confirm-user-deletion')"
        class="w-full px-6 py-4 bg-red-600 hover:bg-red-700 text-white font-bold rounded-xl
               focus:ring-4 focus:ring-red-500/50 transform hover:scale-[1.02] transition-all duration-200 
               shadow-lg hover:shadow-xl flex items-center justify-center group">
        <i class="fas fa-trash-alt mr-3 text-lg group-hover:shake"></i>
        <span>Supprimer le compte définitivement</span>
    </button>

    <x-modal name="confirm-user-deletion" :show="$errors->userDeletion->isNotEmpty()" focusable>
        <div class="p-8">
            <form method="post" action="{{ route('profile.destroy') }}">
                @csrf
                @method('delete')

                <!-- Modal Header -->
                <div class="text-center mb-6">
                    <div class="w-20 h-20 bg-red-100 dark:bg-red-900/30 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-exclamation-triangle text-red-600 dark:text-red-400 text-3xl"></i>
                    </div>
                    <h2 class="text-2xl font-bold text-gray-900 dark:text-gray-100 mb-2">
                        {{ __('Are you sure you want to delete your account?') }}
                    </h2>
                    <p class="text-sm text-gray-600 dark:text-gray-400">
                        Cette action est irréversible et toutes vos données seront perdues définitivement.
                    </p>
                </div>

                <!-- Warning Box -->
                <div class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-xl p-4 mb-6">
                    <div class="flex items-start">
                        <i class="fas fa-info-circle text-red-600 dark:text-red-400 mr-3 mt-1"></i>
                        <div class="text-sm text-red-800 dark:text-red-200">
                            <p class="font-semibold mb-2">Les éléments suivants seront supprimés :</p>
                            <ul class="space-y-1 ml-4">
                                <li>• Tous vos fichiers chiffrés</li>
                                <li>• Votre historique d'activité</li>
                                <li>• Vos paramètres et préférences</li>
                                <li>• Vos informations personnelles</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Password Confirmation -->
                <div class="mb-6">
                    <label for="password" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                        <i class="fas fa-lock mr-2 text-red-500"></i>
                        {{ __('Password') }}
                    </label>
                    <div class="relative">
                        <input
                            id="password"
                            name="password"
                            type="password"
                            class="w-full px-4 py-3 border-2 border-gray-200 dark:border-gray-600 rounded-xl 
                                   bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100
                                   focus:border-red-500 dark:focus:border-red-400 focus:ring-4 focus:ring-red-500/20
                                   transition-all duration-200 placeholder-gray-400 dark:placeholder-gray-500"
                            placeholder="{{ __('Entrez votre mot de passe pour confirmer') }}"
                            required />
                        <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                            <i class="fas fa-key text-gray-400"></i>
                        </div>
                    </div>
                    @if($errors->userDeletion->get('password'))
                        <p class="mt-2 text-sm text-red-600 dark:text-red-400 flex items-center">
                            <i class="fas fa-exclamation-circle mr-2"></i>
                            {{ $errors->userDeletion->get('password')[0] }}
                        </p>
                    @endif
                </div>

                <!-- Confirmation Checkbox -->
                <div class="mb-6">
                    <label class="flex items-start cursor-pointer group">
                        <input 
                            type="checkbox" 
                            required
                            class="w-5 h-5 mt-0.5 text-red-600 border-gray-300 rounded focus:ring-red-500 focus:ring-2" />
                        <span class="ml-3 text-sm text-gray-700 dark:text-gray-300 group-hover:text-gray-900 dark:group-hover:text-gray-100">
                            Je comprends que cette action est <strong class="text-red-600 dark:text-red-400">irréversible</strong> et que toutes mes données seront <strong class="text-red-600 dark:text-red-400">définitivement supprimées</strong>.
                        </span>
                    </label>
                </div>

                <!-- Action Buttons -->
                <div class="flex items-center justify-end space-x-3">
                    <button
                        type="button"
                        x-on:click="$dispatch('close')"
                        class="px-6 py-3 bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 font-semibold rounded-xl
                               hover:bg-gray-300 dark:hover:bg-gray-600 focus:ring-4 focus:ring-gray-500/50
                               transition-all duration-200 flex items-center">
                        <i class="fas fa-times mr-2"></i>
                        {{ __('Cancel') }}
                    </button>

                    <button
                        type="submit"
                        class="px-6 py-3 bg-red-600 hover:bg-red-700 text-white font-bold rounded-xl
                               focus:ring-4 focus:ring-red-500/50 transition-all duration-200 
                               shadow-lg hover:shadow-xl flex items-center group">
                        <i class="fas fa-trash-alt mr-2 group-hover:rotate-12 transition-transform"></i>
                        {{ __('Delete Account') }}
                    </button>
                </div>
            </form>
        </div>
    </x-modal>
</section>

<style>
    @keyframes shake {
        0%, 100% { transform: translateX(0); }
        25% { transform: translateX(-5px); }
        75% { transform: translateX(5px); }
    }
    
    .group:hover .group-hover\:shake {
        animation: shake 0.5s ease-in-out;
    }
</style>