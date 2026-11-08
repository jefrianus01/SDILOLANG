<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ProfileTest extends TestCase
{
    use RefreshDatabase;

    public function test_profile_page_is_displayed(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->get('/profile')->assertOk();
    }

    public function test_user_can_upload_profile_photo(): void
    {
        Storage::fake('public');

        $user = User::factory()->create();

        $png = base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mNk+A8AAQUBAScY42YAAAAASUVORK5CYII=');

        $response = $this->actingAs($user)->patch('/profile', [
            'photo' => UploadedFile::fake()->createWithContent('foto.png', $png),
        ]);

        $response->assertSessionHasNoErrors()->assertRedirect('/profile');

        $user->refresh();

        $this->assertNotNull($user->photo_path);
        Storage::disk('public')->assertExists($user->photo_path);
        $this->assertSame($png, Storage::disk('public')->get($user->photo_path));
    }

    public function test_user_can_not_change_name_or_username_from_profile(): void
    {
        $user = User::factory()->create(['name' => 'Nama Asli', 'username' => '198502032010011002']);

        $this->actingAs($user)->patch('/profile', [
            'name' => 'Nama Palsu',
            'username' => '000000000000000000',
        ])->assertSessionHasNoErrors();

        $user->refresh();

        $this->assertSame('Nama Asli', $user->name);
        $this->assertSame('198502032010011002', $user->username);
    }

    public function test_user_can_delete_profile_photo(): void
    {
        Storage::fake('public');

        $user = User::factory()->create(['photo_path' => 'photos/foto.jpg']);
        Storage::disk('public')->put('photos/foto.jpg', 'dummy');

        $this->actingAs($user)->delete('/profile/photo')->assertRedirect('/profile');

        $this->assertNull($user->refresh()->photo_path);
        Storage::disk('public')->assertMissing('photos/foto.jpg');
    }

    public function test_invalid_photo_is_rejected(): void
    {
        Storage::fake('public');

        $user = User::factory()->create();

        $this->actingAs($user)->patch('/profile', [
            'photo' => UploadedFile::fake()->create('dokumen.pdf', 100, 'application/pdf'),
        ])->assertSessionHasErrors('photo');
    }
}
