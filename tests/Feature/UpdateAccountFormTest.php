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

    public function testNameIsRequired()
    {
        Livewire::test(UpdateAccountForm::class)
            ->set('user.name', '')
            ->call('submit')
            ->assertHasErrors(['user.name' => 'required']);
    }

    public function testNameIsMinimumTwoCharacters()
    {
        Livewire::test(UpdateAccountForm::class)
            ->set('user.name', 'a')
            ->call('submit')
            ->assertHasErrors(['user.name' => 'min']);
    }

    public function testEmailIsRequired()
    {
        Livewire::test(UpdateAccountForm::class)
            ->set('user.email', '')
            ->call('submit')
            ->assertHasErrors(['user.email' => 'required']);
    }

    public function testEmailIsValidEmail()
    {
        Livewire::test(UpdateAccountForm::class)
            ->set('user.email', 'Something')
            ->call('submit')
            ->assertHasErrors(['user.email' => 'email']);
    }

    public function testEmailIsUnique()
    {
        User::factory()->create(['email' => 'test@example.com']);

        Livewire::test(UpdateAccountForm::class)
            ->set('user.email', 'test@example.com')
            ->call('submit')
            ->assertHasErrors(['user.email' => 'unique']);
    }

    public function testPasswordMayBeLeftBlank()
    {
        Livewire::test(UpdateAccountForm::class)
            ->set('password', '')
            ->call('submit')
            ->assertHasNoErrors(['password' => 'nullable']);
    }

    public function testPasswordIsMinimumEightCharacters()
    {
        Livewire::test(UpdateAccountForm::class)
            ->set('password', 'test')
            ->call('submit')
            ->assertHasErrors(['password' => 'min']);
    }

    public function testPasswordMustBeConfirmed()
    {
        Livewire::test(UpdateAccountForm::class)
            ->set('password', 'test')
            ->call('submit')
            ->assertHasErrors(['password' => 'confirmed']);
    }

    public function testPasswordConfirmationMayBeLeftBlank()
    {
        Livewire::test(UpdateAccountForm::class)
            ->set('password_confirmation', '')
            ->call('submit')
            ->assertHasNoErrors(['password_confirmation' => 'nullable']);
    }

    public function testPasswordConfirmationMatchesPassword()
    {
        Livewire::test(UpdateAccountForm::class)
            ->set('password', 'test')
            ->set('password_confirmation', 'test2')
            ->call('submit')
            ->assertHasErrors(['password_confirmation' => 'same']);
    }

    public function testAccountSaved()
    {
        Livewire::test(UpdateAccountForm::class)
            ->call('submit')
            ->assertDispatchedBrowserEvent('notify', __('filament::update-account-form.updated'));
    }

    public function testCanUploadAvatar()
    {
        $file = UploadedFile::fake()->image('avatar.jpg');

        Storage::fake(config('filament.storage_disk'));

        Livewire::test(UpdateAccountForm::class)
            ->set('avatar', $file)
            ->call('submit');

        $this->user->refresh();

        static::assertNotNull($this->user->avatar);

        Storage::disk(config('filament.storage_disk'))->assertExists($this->user->avatar);
    }

    public function testCanDeleteAvatar()
    {
        $file = UploadedFile::fake()->image('avatar.jpg');

        Storage::fake(config('filament.storage_disk'));

        Livewire::test(UpdateAccountForm::class)
            ->set('avatar', $file)
            ->call('submit');

        $this->user->refresh();

        static::assertNotNull($this->user->avatar);

        Storage::disk(config('filament.storage_disk'))->assertExists($this->user->avatar);

        Livewire::test(UpdateAccountForm::class)
            ->call('deleteAvatar')
            ->assertDispatchedBrowserEvent('notify', __('filament::avatar.delete', ['name' => $this->user->name]));

        Storage::disk(config('filament.storage_disk'))->assertMissing($this->user->avatar);
    }

    protected function setUp(): void
    {
        $this->user = FilamentUser::factory()->create();

        $this->be($this->user);
    }
}
