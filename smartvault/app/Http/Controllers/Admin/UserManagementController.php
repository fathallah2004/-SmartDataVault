<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\EncryptedFile;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class UserManagementController extends Controller
{
    public function index(Request $request): View
    {
        $query = User::query()
            ->withCount('encryptedFiles')
            ->orderBy('created_at', 'desc');

        if ($search = $request->string('search')->trim()) {
            $query->where(function ($builder) use ($search) {
                $builder
                    ->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($role = $request->string('role')->trim()) {
            $query->where('role', $role);
        }

        $users = $query->paginate(10)->withQueryString();

        $roles = User::query()->select('role')->distinct()->pluck('role');

        $stats = [
            'total_users' => User::count(),
            'total_admins' => User::where('role', 'admin')->count(),
            'total_members' => User::where('role', '!=', 'admin')->count(),
            'total_files' => EncryptedFile::count(),
        ];

        return view('admin.users.index', compact('users', 'roles', 'stats'));
    }

    public function show(User $user, Request $request): View
    {
        $user->loadCount('encryptedFiles');

        $lastFile = EncryptedFile::where('user_id', $user->id)
            ->whereNull('deleted_at')
            ->latest('updated_at')
            ->first();

        $lastSession = DB::table('sessions')
            ->where('user_id', $user->id)
            ->orderByDesc('last_activity')
            ->first();

        $lastAccessAt = $lastSession
            ? Carbon::createFromTimestamp($lastSession->last_activity)
            : null;

        $totalStorage = $user->formatted_storage;

        // Pagination des fichiers
        $files = EncryptedFile::where('user_id', $user->id)
            ->whereNull('deleted_at')
            ->orderByDesc('created_at')
            ->paginate(10)
            ->withQueryString();

        return view('admin.users.show', [
            'managedUser' => $user,
            'lastFile' => $lastFile,
            'lastAccessAt' => $lastAccessAt,
            'totalStorage' => $totalStorage,
            'files' => $files,
        ]);
    }

    public function destroy(User $user): RedirectResponse
    {
        if ($user->isAdmin()) {
            return back()->with('error', 'Impossible de supprimer un autre administrateur.');
        }

        if ($user->is(auth()->user())) {
            return back()->with('error', 'Vous ne pouvez pas supprimer votre propre compte.');
        }

        DB::transaction(function () use ($user): void {
            EncryptedFile::where('user_id', $user->id)->delete();
            $user->delete();
        });

        return redirect()
            ->route('admin.users.index')
            ->with('status', 'Compte utilisateur supprimé avec succès.');
    }
}

