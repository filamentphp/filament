<?php

namespace Filament\Tests\Feature;

use Filament\Http\Livewire\UpdateAccountForm;
use Filament\Models\FilamentUser;
use Filament\Tests\TestCase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;

class UpdateAccountFormTest extends TestCase
{
    public $user;

    public function setUp(): void
    {
        $this->user = FilamentUser::factory()->create();

        $this->be($this->user);
    }

    public function test_name_is_required()
    {
        Livewire::test(UpdateAccountForm::class)
            ->set('user.name', '')
            ->call('submit')
            ->assertHasErrors(['user.name' => 'required']);
    }

    public function test_name_is_minimum_two_characters()
    {
        Livewire::test(UpdateAccountForm::class)
            ->set('user.name', 'a')
            ->call('submit')
            ->assertHasErrors(['user.name' => 'min']);
    }

    public function test_email_is_required()
    {
        Livewire::test(UpdateAccountForm::class)
            ->set('user.email', '')
            ->call('submit')
            ->assertHasErrors(['user.email' => 'required']);
    }

    public function test_email_is_valid_email()
    {
        Livewire::test(UpdateAccountForm::class)
            ->set('user.email', 'Something')
            ->call('submit')
            ->assertHasErrors(['user.email' => 'email']);
    }

    public function test_email_is_unique()
    {
        User::factory()->create(['email' => 'test@example.com']);

        Livewire::test(UpdateAccountForm::class)
            ->set('user.email', 'test@example.com')
            ->call('submit')
            ->assertHasErrors(['user.email' => 'unique']);
    }

    public function test_password_may_be_left_blank()
    {
        Livewire::test(UpdateAccountForm::class)
            ->set('password', '')
            ->call('submit')
            ->assertHasNoErrors(['password' => 'nullable']);
    }

    public function test_password_is_minimum_eight_characters()
    {
        Livewire::test(UpdateAccountForm::class)
            ->set('password', 'test')
            ->call('submit')
            ->assertHasErrors(['password' => 'min']);
    }

    public function test_password_must_be_confirmed()
    {
        Livewire::test(UpdateAccountForm::class)
            ->set('password', 'test')
            ->call('submit')
            ->assertHasErrors(['password' => 'confirmed']);
    }

    public function test_password_confirmation_may_be_left_blank()
    {
        Livewire::test(UpdateAccountForm::class)
            ->set('password_confirmation', '')
            ->call('submit')
            ->assertHasNoErrors(['password_confirmation' => 'nullable']);
    }

    public function test_password_confirmation_matches_password()
    {
        Livewire::test(UpdateAccountForm::class)
            ->set('password', 'test')
            ->set('password_confirmation', 'test2')
            ->call('submit')
            ->assertHasErrors(['password_confirmation' => 'same']);
    }

    public function test_account_saved()
    {
        Livewire::test(UpdateAccountForm::class)
            ->call('submit')
            ->assertDispatchedBrowserEvent('notify', __('filament::update-account-form.updated'));
    }

    public function test_can_upload_avatar()
    {
        $file = UploadedFile::fake()->image('avatar.jpg');

        Storage::fake(config('filament.storage_disk'));

        Livewire::test(UpdateAccountForm::class)
            ->set('avatar', $file)
            ->call('submit');

        $this->user->refresh();

        $this->assertNotNull($this->user->avatar);

        Storage::disk(config('filament.storage_disk'))->assertExists($this->user->avatar);
    }

    public function test_can_delete_avatar()
    {
        $file = UploadedFile::fake()->image('avatar.jpg');

        Storage::fake(config('filament.storage_disk'));

        Livewire::test(UpdateAccountForm::class)
            ->set('avatar', $file)
            ->call('submit');

        $this->user->refresh();

        $this->assertNotNull($this->user->avatar);

        Storage::disk(config('filament.storage_disk'))->assertExists($this->user->avatar);

        Livewire::test(UpdateAccountForm::class)
            ->call('deleteAvatar')
            ->assertDispatchedBrowserEvent('notify', __('filament::avatar.delete', ['name' => $this->user->name]));

        Storage::disk(config('filament.storage_disk'))->assertMissing($this->user->avatar);
    }
}
