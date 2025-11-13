<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\EncryptedFile;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminDashboardController extends Controller
{
    public function index(Request $request)
    {
        $search = (string) $request->query('search', '');
        $role = (string) $request->query('role', '');
        $sort = (string) $request->query('sort', 'created_at');
        $direction = strtolower((string) $request->query('direction', 'desc')) === 'asc' ? 'asc' : 'desc';

        $usersQuery = User::query()
            ->withCount([
                'encryptedFiles' => function ($query) {
                    $query->whereNull('encrypted_files.deleted_at');
                },
            ])
            ->withSum([
                'encryptedFiles as encrypted_files_size_sum' => function ($query) {
                    $query->whereNull('encrypted_files.deleted_at');
                },
            ], 'file_size');

        if (!empty($search)) {
            $usersQuery->where(function ($query) use ($search): void {
                $query->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if (!empty($role) && in_array($role, ['admin', 'user'], true)) {
            $usersQuery->where('role', $role);
        }

        $allowedSorts = ['name', 'email', 'created_at', 'last_login_at', 'last_upload_at', 'encrypted_files_count'];
        if (!in_array($sort, $allowedSorts, true)) {
            $sort = 'created_at';
        }

        if ($sort === 'encrypted_files_count') {
            $usersQuery->orderBy('encrypted_files_count', $direction);
        } else {
            $usersQuery->orderBy($sort, $direction);
        }

        $users = $usersQuery
            ->paginate(10)
            ->withQueryString();

        $stats = $this->buildStats();

        if ($request->ajax()) {
            return view('admin.users.partials.table', compact('users'))->render();
        }

        return view('admin.users.index', [
            'users' => $users,
            'stats' => $stats,
            'filters' => [
                'search' => $search,
                'role' => $role,
                'sort' => $sort,
                'direction' => $direction,
            ],
        ]);
    }

    public function show(User $user, Request $request): JsonResponse
    {
        $user->loadCount([
            'encryptedFiles' => function ($query) {
                $query->whereNull('encrypted_files.deleted_at');
            },
        ]);

        $filesQuery = EncryptedFile::where('user_id', $user->id)
            ->whereNull('deleted_at');

        // Recherche par nom de fichier
        if ($request->filled('files_search')) {
            $search = $request->query('files_search');
            $filesQuery->where('original_name', 'like', "%{$search}%");
        }

        // Filtre par algorithme
        if ($request->filled('files_algorithm')) {
            $filesQuery->where('encryption_method', $request->query('files_algorithm'));
        }

        // Filtre par date
        if ($request->filled('files_date_filter')) {
            match($request->query('files_date_filter')) {
                'today' => $filesQuery->whereDate('created_at', today()),
                'week' => $filesQuery->where('created_at', '>=', now()->subWeek()),
                'month' => $filesQuery->where('created_at', '>=', now()->subMonth()),
                default => null
            };
        }

        $filesQuery->orderByDesc('created_at');

        // Pagination pour les fichiers
        $page = (int) $request->query('files_page', 1);
        $filesPaginated = $filesQuery->paginate(10, ['*'], 'files_page', $page)->withQueryString();

        $storageSum = EncryptedFile::where('user_id', $user->id)
            ->whereNull('deleted_at')
            ->sum('file_size');

        $files = $filesPaginated->map(function (EncryptedFile $file) {
            return [
                'id' => $file->id,
                'name' => $file->original_name,
                'size' => $this->formatBytes($file->file_size),
                'algorithm' => $file->getAlgorithmNameAttribute(),
                'method' => $file->encryption_method,
                'created_at' => optional($file->created_at)->format('d/m/Y H:i'),
                'created_at_iso' => optional($file->created_at)->toIso8601String(),
            ];
        });

        return response()->json([
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role,
                'formatted_role' => ucfirst($user->role),
                'created_at' => optional($user->created_at)->format('d/m/Y H:i'),
                'last_login_at' => optional($user->last_login_at)->format('d/m/Y H:i'),
                'last_upload_at' => optional($user->last_upload_at)->format('d/m/Y H:i'),
                'files_count' => $user->encrypted_files_count,
                'total_storage' => $this->formatBytes($storageSum),
                'status' => $user->trashed() ? 'inactive' : 'active',
            ],
            'files' => $files,
            'files_pagination' => [
                'current_page' => $filesPaginated->currentPage(),
                'last_page' => $filesPaginated->lastPage(),
                'per_page' => $filesPaginated->perPage(),
                'total' => $filesPaginated->total(),
                'from' => $filesPaginated->firstItem(),
                'to' => $filesPaginated->lastItem(),
            ],
        ]);
    }

    public function destroy(User $user): JsonResponse
    {
        DB::transaction(function () use ($user): void {
            $user->encryptedFiles()->each(function (EncryptedFile $file): void {
                $file->delete();
            });

            $user->delete();
        });

        return response()->json([
            'message' => 'Utilisateur supprimé (soft delete) avec succès.',
        ]);
    }

    private function buildStats(): array
    {
        $totalFiles = EncryptedFile::query()->whereNull('deleted_at')->count();
        $totalAdmins = User::query()->where('role', 'admin')->count();
        $totalUsers = User::query()->where('role', 'user')->count();
        $totalStorageBytes = EncryptedFile::query()->whereNull('deleted_at')->sum('file_size');

        return [
            'total_files' => $totalFiles,
            'total_admins' => $totalAdmins,
            'total_users' => $totalUsers,
            'total_storage' => $this->formatBytes($totalStorageBytes),
        ];
    }

    private function formatBytes(int $bytes): string
    {
        if ($bytes === 0) {
            return '0 B';
        }

        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $pow = min((int) floor(log($bytes, 1024)), count($units) - 1);
        $value = $bytes / (1024 ** $pow);

        return sprintf('%.2f %s', $value, $units[$pow]);
    }
}

