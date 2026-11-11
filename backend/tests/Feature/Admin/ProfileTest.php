<?php

namespace Tests\Feature\Admin;

use App\Filament\Pages\Auth\EditProfile;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Livewire\Livewire;
use Tests\TestCase;

class ProfileTest extends TestCase
{
    use RefreshDatabase;

    public function test_every_role_can_open_the_profile_page(): void
    {
        foreach (['super_admin', 'admin', 'editor'] as $role) {
            $this->actingAs($this->userWithRole($role))->get('/admin/profile')->assertOk();
        }
    }

    public function test_user_updates_name_email_and_password(): void
    {
        $user = $this->userWithRole('editor');
        $user->forceFill(['password' => 'old-Passw0rd'])->save();
        $this->actingAs($user);

        Livewire::test(EditProfile::class)
            ->fillForm([
                'name' => 'New Name', 'email' => 'new@example.com',
                'password' => 'New-Passw0rd!', 'passwordConfirmation' => 'New-Passw0rd!', 'currentPassword' => 'old-Passw0rd',
            ])
            ->call('save')
            ->assertHasNoFormErrors();

        $user->refresh();
        $this->assertSame(['New Name', 'new@example.com'], [$user->name, $user->email]);
        $this->assertTrue(Hash::check('New-Passw0rd!', $user->password));
    }

    public function test_password_change_needs_the_current_password(): void
    {
        $user = $this->userWithRole('editor');
        $user->forceFill(['password' => 'old-Passw0rd'])->save();
        $this->actingAs($user);

        Livewire::test(EditProfile::class)
            ->fillForm(['password' => 'New-Passw0rd!', 'passwordConfirmation' => 'New-Passw0rd!', 'currentPassword' => 'wrong'])
            ->call('save')
            ->assertHasFormErrors(['currentPassword']);

        $this->assertTrue(Hash::check('old-Passw0rd', $user->refresh()->password));
    }
}
