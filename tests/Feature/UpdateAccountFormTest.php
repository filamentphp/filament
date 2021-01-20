<?php

namespace Filament\Tests\Feature;

use Filament\Http\Livewire\UpdateAccountForm;
use Filament\Models\FilamentUser;
use Filament\Tests\TestCase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Livewire\Livewire;

class UpdateAccountFormTest extends TestCase
{
    /** @test */
    public function can_view_account_page()
    {
        $user = FilamentUser::factory()->create();

        $this->be($user);

        $this->get(route('filament.account'))
            ->assertSuccessful()
            ->assertSeeLivewire('filament.update-account-form');
    }

    /** @test */
    public function can_update_account_information()
    {
        Storage::fake(config('filament.storage_disk'));

        $user = FilamentUser::factory()->create();
        $newAvatar = UploadedFile::fake()->image('avatar.jpg');
        $newUserDetails = FilamentUser::factory()->make();
        $newPassword = Str::random();

        $this->be($user);

        Livewire::test(UpdateAccountForm::class)
            ->assertSet('user.email', $user->email)
            ->assertSet('user.name', $user->name)
            ->set('newAvatar', $newAvatar)
            ->set('newPassword', $newPassword)
            ->set('newPasswordConfirmation', $newPassword)
            ->set('user.email', $newUserDetails->email)
            ->set('user.name', $newUserDetails->name)
            ->call('submit')
            ->assertSet('newAvatar', null)
            ->assertSet('newPassword', null)
            ->assertSet('newPasswordConfirmation', null)
            ->assertDispatchedBrowserEvent('notify');

        $user->refresh();

        Storage::disk(config('filament.storage_disk'))->assertExists($user->avatar);
        $this->assertEquals($newUserDetails->email, $user->email);
        $this->assertEquals($newUserDetails->name, $user->name);
        $this->assertTrue(Auth::attempt([
            'email' => $newUserDetails->email,
            'password' => $newPassword,
        ]));
    }

    /** @test */
    public function can_delete_avatar()
    {
        $user = FilamentUser::factory()->create();
        $newAvatar = UploadedFile::fake()->image('avatar.jpg');

        $this->be($user);

        $component = Livewire::test(UpdateAccountForm::class)
            ->set('newAvatar', $newAvatar)
            ->call('submit');

        $user->refresh();

        Storage::disk(config('filament.storage_disk'))->assertExists($user->avatar);

        $component
            ->call('deleteAvatar')
            ->assertSet('newAvatar', null);

        Storage::disk(config('filament.storage_disk'))->assertMissing($user->avatar);

        $user->refresh();

        $this->assertNull($user->avatar);
    }

    /** @test */
    public function new_avatar_is_image()
    {
        $user = FilamentUser::factory()->create();
        $newAvatar = UploadedFile::fake()->create('document.txt');

        $this->be($user);

        Livewire::test(UpdateAccountForm::class)
            ->set('newAvatar', $newAvatar)
            ->assertHasErrors(['newAvatar' => 'image']);
    }

    /** @test */
    public function new_password_contains_minimum_8_characters()
    {
        $user = FilamentUser::factory()->create();

        $this->be($user);

        Livewire::test(UpdateAccountForm::class)
            ->set('newPassword', 'pass')
            ->call('submit')
            ->assertHasErrors(['newPassword' => 'min']);
    }

    /** @test */
    public function new_password_is_confirmed()
    {
        $user = FilamentUser::factory()->create();

        $this->be($user);

        Livewire::test(UpdateAccountForm::class)
            ->set('newPassword', 'password')
            ->set('newPasswordConfirmation', 'different-password')
            ->call('submit')
            ->assertHasErrors(['newPasswordConfirmation' => 'same']);
    }

    /** @test */
    public function user_email_is_required()
    {
        $user = FilamentUser::factory()->create();

        $this->be($user);

        Livewire::test(UpdateAccountForm::class)
            ->set('user.email', null)
            ->call('submit')
            ->assertHasErrors(['user.email' => 'required']);
    }

    /** @test */
    public function user_email_is_valid_email()
    {
        $user = FilamentUser::factory()->create();

        $this->be($user);

        Livewire::test(UpdateAccountForm::class)
            ->set('user.email', 'invalid-email')
            ->call('submit')
            ->assertHasErrors(['user.email' => 'email']);
    }

    /** @test */
    public function user_name_is_required()
    {
        $user = FilamentUser::factory()->create();

        $this->be($user);

        Livewire::test(UpdateAccountForm::class)
            ->set('user.name', null)
            ->call('submit')
            ->assertHasErrors(['user.name' => 'required']);
    }
}
