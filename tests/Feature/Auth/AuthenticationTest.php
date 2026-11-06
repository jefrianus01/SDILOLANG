<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use App\Support\Roles;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RolePermissionSeeder::class);
    }

    public function test_login_screen_can_be_rendered(): void
    {
        $this->get('/login')->assertOk();
    }

    public function test_users_can_authenticate_using_username(): void
    {
        $user = User::factory()->create(['username' => '198502032010011002']);
        $user->assignRole(Roles::GURU);

        $response = $this->post('/login', [
            'username' => '198502032010011002',
            'password' => 'password',
        ]);

        $this->assertAuthenticatedAs($user);
        $response->assertRedirect(route('dashboard', absolute: false));
        $this->assertNotNull($user->refresh()->last_login_at);
    }

    public function test_users_can_not_authenticate_with_invalid_password(): void
    {
        User::factory()->create(['username' => '198502032010011002']);

        $this->post('/login', [
            'username' => '198502032010011002',
            'password' => 'wrong-password',
        ]);

        $this->assertGuest();
    }

    public function test_inactive_users_can_not_authenticate(): void
    {
        User::factory()->create([
            'username' => '198502032010011002',
            'is_active' => false,
        ]);

        $this->post('/login', [
            'username' => '198502032010011002',
            'password' => 'password',
        ]);

        $this->assertGuest();
    }

    public function test_user_must_change_password_before_using_the_app(): void
    {
        $user = User::factory()->mustChangePassword()->create();
        $user->assignRole(Roles::GURU);

        $response = $this->actingAs($user)->get('/dashboard');

        $response->assertRedirect(route('profile.edit'));
        $response->assertSessionHas('warning');
    }

    public function test_users_can_logout(): void
    {
        $user = User::factory()->create();
        $user->assignRole(Roles::GURU);

        $response = $this->actingAs($user)->post('/logout');

        $this->assertGuest();
        $response->assertRedirect('/');
    }
}
