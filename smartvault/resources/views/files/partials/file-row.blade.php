{{-- resources/views/files/partials/file-row.blade.php --}}
@props(['file'])

<div class="grid grid-cols-12 gap-4 px-4 py-3 items-center bg-gray-50 dark:bg-gray-700/50 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition duration-150">
    <!-- Nom et icône -->
    <div class="col-span-5 flex items-center space-x-3">
        <span class="text-2xl">{{ $file->file_icon ?? '📄' }}</span>
        <div class="flex-1 min-w-0">
            <div class="font-medium text-gray-900 dark:text-gray-100 truncate">
                {{ $file->original_name }}
            </div>
            <div class="text-xs text-gray-500 dark:text-gray-400 truncate">
                Chiffré avec {{ $file->algorithm_name }}
            </div>
        </div>
    </div>

    <!-- Algorithme -->
    <div class="col-span-2">
        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-200">
            {{ $file->algorithm_name }}
        </span>
    </div>

    <!-- Taille -->
    <div class="col-span-2 text-sm text-gray-600 dark:text-gray-300">
        {{ $file->formatted_size }}
    </div>

    <!-- Date -->
    <div class="col-span-2 text-sm text-gray-600 dark:text-gray-300">
        {{ $file->created_at->format('d/m/Y H:i') }}
    </div>

    <!-- Actions -->
    <div class="col-span-1 flex justify-end space-x-2">
        <a href="{{ route('files.download', $file) }}" 
           class="text-green-600 hover:text-green-800 dark:text-green-400 dark:hover:text-green-300 transition duration-150"
           title="Télécharger">
            📥
        </a>
        <form action="{{ route('files.destroy', $file) }}" method="POST" class="inline">
            @csrf 
            @method('DELETE')
            <button type="submit" 
                    class="text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-300 transition duration-150"
                    title="Supprimer"
                    onclick="return confirm('Êtes-vous sûr de vouloir supprimer définitivement ce fichier ?')">
                🗑️
            </button>
        </form>
    </div>
</div>