<?php

namespace Tests\Feature\Admin;

use App\Models\EncryptedFile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_access_user_listing(): void
    {
        $admin = User::factory()->admin()->create();

        $response = $this->actingAs($admin)->get(route('admin.users.index'));

        $response->assertOk();
        $response->assertSee('Tableau de bord des utilisateurs');
    }

    public function test_non_admin_cannot_access_admin_routes(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('admin.users.index'));

        $response->assertStatus(403);
    }

    public function test_admin_redirected_after_login(): void
    {
        $admin = User::factory()->admin()->create([
            'password' => bcrypt('password'),
        ]);

        $response = $this->post(route('login'), [
            'email' => $admin->email,
            'password' => 'password',
        ]);

        $response->assertRedirect(route('admin.dashboard', absolute: false));
    }

    public function test_admin_can_logout(): void
    {
        $admin = User::factory()->admin()->create();

        $response = $this->actingAs($admin)->post(route('logout'));

        $response->assertRedirect('/');
        $this->assertGuest();
    }

    public function test_admin_can_fetch_user_details_via_ajax(): void
    {
        $admin = User::factory()->admin()->create();
        $user = User::factory()->create([
            'last_upload_at' => now()->subDay(),
            'last_login_at' => now()->subHours(5),
        ]);

        EncryptedFile::create([
            'original_name' => 'document.txt',
            'file_size' => 2048,
            'file_type' => 'txt',
            'encrypted_content' => base64_encode('foo'),
            'encryption_method' => 'cesar',
            'encryption_key' => '3',
            'iv' => null,
            'file_hash' => 'hash',
            'user_id' => $user->id,
        ]);

        $response = $this->actingAs($admin)->getJson(route('admin.users.show', $user));

        $response->assertOk()
            ->assertJsonStructure([
                'user' => [
                    'id',
                    'name',
                    'email',
                    'role',
                    'created_at',
                    'last_login_at',
                    'last_upload_at',
                    'files_count',
                    'total_storage',
                    'status',
                ],
                'files' => [
                    [
                        'id',
                        'name',
                        'size',
                        'algorithm',
                        'method',
                        'created_at',
                    ],
                ],
            ])
            ->assertJsonFragment(['name' => $user->name]);
    }

    public function test_admin_can_soft_delete_user(): void
    {
        $admin = User::factory()->admin()->create();
        $user = User::factory()->create();

        $file = EncryptedFile::create([
            'original_name' => 'secret.pdf',
            'file_size' => 1024,
            'file_type' => 'pdf',
            'encrypted_content' => base64_encode('data'),
            'encryption_method' => 'reverse',
            'encryption_key' => '5',
            'iv' => null,
            'file_hash' => 'hash',
            'user_id' => $user->id,
        ]);

        $response = $this->actingAs($admin)->deleteJson(route('admin.users.destroy', $user));

        $response->assertOk()->assertJsonFragment([
            'message' => 'Utilisateur supprimé (soft delete) avec succès.',
        ]);

        $this->assertSoftDeleted($user);
        $this->assertSoftDeleted($file);
    }
}

