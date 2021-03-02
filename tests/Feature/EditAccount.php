<?php

namespace Filament\Tests\Feature;

use Filament\Http\Livewire\EditAccount;
use Filament\Models\User;
use Filament\Tests\TestCase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Livewire\Livewire;

class EditAccountTest extends TestCase
{
    /** @test */
    public function can_view_account_page()
    {
        $user = User::factory()->create();

        $this->be($user);

        $this->get(route('filament.account'))
            ->assertSuccessful()
            ->assertSeeLivewire('filament.edit-account');
    }

    /** @test */
    public function can_update_account_information()
    {
        Storage::fake(config('filament.storage_disk'));

        $user = User::factory()->create();
//        $newAvatar = UploadedFile::fake()->image('avatar.jpg');
        $newUserDetails = User::factory()->make();
        $newPassword = Str::random();

        $this->be($user);

        Livewire::test(EditAccount::class)
            ->assertSet('record.email', $user->email)
            ->assertSet('record.name', $user->name)
//            ->set('newAvatar', $newAvatar)
            ->set('newPassword', $newPassword)
            ->set('newPasswordConfirmation', $newPassword)
            ->set('record.email', $newUserDetails->email)
            ->set('record.name', $newUserDetails->name)
            ->call('save')
//            ->assertSet('newAvatar', null)
            ->assertSet('newPassword', null)
            ->assertSet('newPasswordConfirmation', null)
            ->assertDispatchedBrowserEvent('notify');

        $user->refresh();

//        Storage::disk(config('filament.storage_disk'))->assertExists($user->avatar);
        $this->assertEquals($newUserDetails->email, $user->email);
        $this->assertEquals($newUserDetails->name, $user->name);
        $this->assertTrue(Auth::attempt([
            'email' => $newUserDetails->email,
            'password' => $newPassword,
        ]));
    }

//    /** @test */
//    public function can_delete_avatar()
//    {
//        $user = User::factory()->create();
//        $newAvatar = UploadedFile::fake()->image('avatar.jpg');
//
//        $this->be($user);
//
//        $component = Livewire::test(EditAccount::class)
//            ->set('newAvatar', $newAvatar)
//            ->call('save');
//
//        $user->refresh();
//
//        Storage::disk(config('filament.storage_disk'))->assertExists($user->avatar);
//
//        $component
//            ->call('deleteAvatar')
//            ->assertSet('newAvatar', null);
//
//        Storage::disk(config('filament.storage_disk'))->assertMissing($user->avatar);
//
//        $user->refresh();
//
//        $this->assertNull($user->avatar);
//    }

//    /** @test */
//    public function new_avatar_is_image()
//    {
//        $user = User::factory()->create();
//        $newAvatar = UploadedFile::fake()->create('document.txt');
//
//        $this->be($user);
//
//        Livewire::test(EditAccount::class)
//            ->set('newAvatar', $newAvatar)
//            ->assertHasErrors(['newAvatar' => 'image']);
//    }

    /** @test */
    public function new_password_contains_minimum_8_characters()
    {
        $user = User::factory()->create();

        $this->be($user);

        Livewire::test(EditAccount::class)
            ->set('newPassword', 'pass')
            ->call('save')
            ->assertHasErrors(['newPassword' => 'min']);
    }

    /** @test */
    public function new_password_is_confirmed()
    {
        $user = User::factory()->create();

        $this->be($user);

        Livewire::test(EditAccount::class)
            ->set('newPassword', 'password')
            ->set('newPasswordConfirmation', 'different-password')
            ->call('save')
            ->assertHasErrors(['newPasswordConfirmation' => 'same']);
    }

    /** @test */
    public function record_email_is_required()
    {
        $user = User::factory()->create();

        $this->be($user);

        Livewire::test(EditAccount::class)
            ->set('record.email', null)
            ->call('save')
            ->assertHasErrors(['record.email' => 'required']);
    }

    /** @test */
    public function record_email_is_valid_email()
    {
        $user = User::factory()->create();

        $this->be($user);

        Livewire::test(EditAccount::class)
            ->set('record.email', 'invalid-email')
            ->call('save')
            ->assertHasErrors(['record.email' => 'email']);
    }

    /** @test */
    public function record_name_is_required()
    {
        $user = User::factory()->create();

        $this->be($user);

        Livewire::test(EditAccount::class)
            ->set('record.name', null)
            ->call('save')
            ->assertHasErrors(['record.name' => 'required']);
    }
}
