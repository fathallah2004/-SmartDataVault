<div class="sidebar bg-white dark:bg-gray-800 border-r border-gray-200 dark:border-gray-700 
            w-88 fixed left-0 top-0 h-screen min-h-screen flex flex-col z-40">
    
    @php
    use App\Services\EncryptionService;
    
    $user = Auth::user();
    if ($user) {
        // 🔥 CORRECTION : Limite à 100 Mo (100 * 1024 * 1024 bytes)
        $storageLimit = 100 * 1024 * 1024; // 100 MB en bytes
        $storagePercentage = $user->total_storage_used > 0 ? 
            min(($user->total_storage_used / $storageLimit) * 100, 100) : 0;
        
        $stats = [
            'total_files' => $user->total_files_encrypted,
            'total_storage' => $user->formatted_storage,
            'last_upload' => $user->last_upload_at ? $user->last_upload_at->diffForHumans() : 'Jamais',
            'storage_percentage' => $storagePercentage // 🔥 Utilisation du bon calcul
        ];
        
        $encryptionService = new EncryptionService();
        $algorithms = $encryptionService->getAvailableAlgorithms();

        // 🔥 CORRECTION : Nettoyage des données utilisateur
        $cleanName = trim($user->name ?? '');
        $cleanName = preg_replace('/[^\p{L}\p{N}\s]/u', '', $cleanName);
        $cleanName = trim($cleanName);

        $cleanEmail = trim($user->email ?? '');
        $cleanEmail = preg_replace('/[^\w@\.\-]/', '', $cleanEmail);
    } else {
        $stats = [
            'total_files' => 0,
            'total_storage' => '0 bytes',
            'last_upload' => 'Jamais',
            'storage_percentage' => 0
        ];
        $algorithms = [];
        $cleanName = 'Utilisateur';
        $cleanEmail = '';
    }
    @endphp

    <!-- Contenu scrollable -->
    <div class="flex-1 overflow-y-auto">
        <!-- 1. Profile Section CORRIGÉE - SANS AVATAR -->
        <div class="sidebar-profile px-5 border-b border-gray-200 dark:border-gray-700 bg-gradient-to-r from-blue-50 to-indigo-50 dark:from-gray-800 dark:to-gray-900 rounded-tr-2xl shadow-md">
            <div class="flex items-center space-x-4 pl-4">
                <!-- 🔥 SUPPRESSION COMPLÈTE DE L'AVATAR -->
                <div class="flex-1 min-w-0 py-4">
                    <h3 class="text-xl font-bold text-gray-900 dark:text-gray-100 truncate">{{ $cleanName }}</h3>
                    <p class="text-base text-gray-600 dark:text-gray-400 truncate mt-1">{{ $cleanEmail }}</p>
                </div>
            </div>
        </div>

        <!-- 2. Navigation Section - HAUTEUR AUTOMATIQUE -->
        <div class="sidebar-nav px-5 border-b mt-1 border-gray-200 dark:border-gray-700">
            <h4 class="text-base font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-2">Navigation</h4>
            <ul class="space-y-1">
                <li>
                    <a href="{{ route('dashboard') }}" class="flex items-center py-1 text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-xl transition-all duration-200 px-2">
                        <i class="fas fa-home w-6 mr-3 text-lg"></i>
                        <span class="text-base font-medium">Vault</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('encryption.test') }}" class="flex items-center py-1 text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-xl transition-all duration-200 px-2">
                        <i class="fas fa-flask w-6 mr-3 text-lg"></i>
                        <span class="text-base font-medium">Test</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('profile.edit') }}" class="flex items-center py-1 text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-xl transition-all mb-4 duration-200 px-2">
                        <i class="fas fa-user w-6 mr-3 text-lg"></i>
                        <span class="text-base font-medium">Profile</span>
                    </a>
                </li>
            </ul>
        </div>

        <!-- 3. Statistiques Section DYNAMIQUE - SANS BORDURE INFÉRIEURE -->
<div class="sidebar-stats px-5 mt-1 border-gray-200 dark:border-gray-700">
    <h4 class="text-base font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-4">Statistiques</h4>
    
    <!-- Ajout de space-y-[2px] -->
    <div class="space-y-[2px]">
        
        <!-- Stockage SANS progress bar -->
        <div class="flex items-center justify-between p-2 rounded-xl hover:scale-[1.02] transition-all duration-300 shadow-sm">
            <div class="flex items-center space-x-4">
                <div class="w-10 h-10 rounded-xl flex items-center justify-center">
                    <i class="fas fa-database text-blue-600 dark:text-blue-400 text-base"></i>
                </div>
                <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Stockage</span>
            </div>
            <span class="text-sm font-bold text-gray-900 dark:text-gray-100">{{ $stats['total_storage'] }} / 100 MB</span>
        </div>

        <!-- Fichiers DYNAMIQUE -->
        <div class="flex items-center justify-between p-2 rounded-xl hover:scale-[1.02] transition-all duration-300 shadow-sm">
            <div class="flex items-center space-x-4">
                <div class="w-10 h-10 rounded-xl flex items-center justify-center">
                    <i class="fas fa-file text-green-600 dark:text-green-400 text-base"></i>
                </div>
                <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Fichiers</span>
            </div>
            <span class="text-sm font-bold text-gray-900 dark:text-gray-100">{{ $stats['total_files'] }}</span>
        </div>

        <!-- Dernier upload DYNAMIQUE -->
        <div class="flex items-center justify-between p-2 rounded-xl hover:scale-[1.02] transition-all duration-300 shadow-sm">
            <div class="flex items-center space-x-4">
                <div class="w-10 h-10 rounded-xl flex items-center justify-center">
                    <i class="fas fa-clock text-purple-600 dark:text-purple-400 text-base"></i>
                </div>
                <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Dernier upload</span>
            </div>
            <span class="text-sm font-bold text-gray-900 dark:text-gray-100">{{ $stats['last_upload'] }}</span>
        </div>

        <!-- Algorithmes DYNAMIQUE -->
        <div class="mb-4 flex items-center justify-between p-2 rounded-xl hover:scale-[1.02] transition-all duration-300 shadow-sm">
            <div class="flex items-center space-x-4">
                <div class="w-10 h-10 rounded-xl flex items-center justify-center">
                    <i class="fas fa-lock text-yellow-600 dark:text-yellow-400 text-base"></i>
                </div>
                <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Algorithmes</span>
            </div>
            <span class="text-sm font-bold text-gray-900 dark:text-gray-100">{{ count($algorithms) }}</span>
        </div>
    </div>
</div>

    </div>

<!-- 4. Info/Support + Déconnexion (FIXÉ EN BAS) -->
<div class="sidebar-bottom p-6 space-y-2 bg-white dark:bg-gray-800">
    <!-- Info/Support -->
    <button
        class="w-full flex items-center justify-center py-2 
               text-gray-700 dark:text-gray-300 
               hover:bg-gray-100 dark:hover:bg-gray-700 
               rounded-xl transition-all duration-200 px-2 pl-3">
        <i class="fas fa-info-circle w-6 mr-2 text-lg"></i>
        <span class="text-base font-medium">Info / Support</span>
    </button>

    <!-- Déconnexion -->
    <form method="POST" action="{{ route('logout') }}" class="w-full">
        @csrf
        <button type="submit"
            class="w-full flex items-center justify-center py-2 
                   text-red-600 dark:text-red-400 
                   hover:bg-gray-100 dark:hover:bg-gray-700 
                   rounded-xl transition-all duration-200 px-2 pl-3">
            <i class="fas fa-sign-out-alt w-6 mr-2 text-lg"></i>
            <span class="text-base font-medium">Déconnexion</span>
        </button>
    </form>
</div>





</div>