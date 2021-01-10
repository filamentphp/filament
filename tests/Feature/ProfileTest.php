<?php

namespace Filament\Tests\Feature;

use Livewire\Livewire;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\UploadedFile;
use Filament\Tests\TestCase;
use Filament\Tests\Database\Models\User;
use Filament\Http\Livewire\Profile;

class ProfileTest extends TestCase
{
    public $user;

    public function setUp(): void
    {
        $this->user = User::factory()->create();
    }

    public function test_name_is_required()
    {
        $this->getComponent()
            ->set('user.name', '')
            ->call('submit')
            ->assertHasErrors(['user.name' => 'required']);
    }

    public function test_name_is_minimum_two_characters()
    {
        $this->getComponent()
            ->set('user.name', 'a')
            ->call('submit')
            ->assertHasErrors(['user.name' => 'min']);
    }

    public function test_email_is_required()
    {
        $this->getComponent()
            ->set('user.email', '')
            ->call('submit')
            ->assertHasErrors(['user.email' => 'required']);
    }

    public function test_email_is_valid_email()
    {
        $this->getComponent()
            ->set('user.email', 'Something')
            ->call('submit')
            ->assertHasErrors(['user.email' => 'email']);
    }

    public function test_email_is_unique()
    {
        User::factory()->create(['email' => 'test@example.com']);

        $this->getComponent()
            ->set('user.email', 'test@example.com')
            ->call('submit')
            ->assertHasErrors(['user.email' => 'unique']);
    }

    public function test_password_may_be_left_blank()
    {
        $this->getComponent()
            ->set('password', '')
            ->call('submit')
            ->assertHasNoErrors(['password' => 'nullable']);
    }

    public function test_password_is_minimum_eight_characters()
    {
        $this->getComponent()
            ->set('password', 'test')
            ->call('submit')
            ->assertHasErrors(['password' => 'min']);
    }

    public function test_password_must_be_confirmed()
    {
        $this->getComponent()
            ->set('password', 'test')
            ->call('submit')
            ->assertHasErrors(['password' => 'confirmed']);
    }

    public function test_password_confirmation_may_be_left_blank()
    {
        $this->getComponent()
            ->set('password_confirmation', '')
            ->call('submit')
            ->assertHasNoErrors(['password_confirmation' => 'nullable']);
    }

    public function test_password_confirmation_matches_password()
    {
        $this->getComponent()
            ->set('password', 'test')
            ->set('password_confirmation', 'test2')
            ->call('submit')
            ->assertHasErrors(['password_confirmation' => 'same']);
    }

    public function test_profile_saved()
    {
        $this->getComponent()
            ->call('submit')
            ->assertDispatchedBrowserEvent('notify', __('Profile saved!'));
    }

    public function test_can_upload_avatar()
    {
        $file = UploadedFile::fake()->image('avatar.jpg');

        Storage::fake(config('filament.storage_disk'));

        $this->getComponent()
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

        $this->getComponent()
            ->set('avatar', $file)
            ->call('submit');

        $this->user->refresh();

        $this->assertNotNull($this->user->avatar);

        Storage::disk(config('filament.storage_disk'))->assertExists($this->user->avatar);

        $this->getComponent()
            ->call('deleteAvatar')
            ->assertDispatchedBrowserEvent('notify', __('Avatar removed for :name', ['name' => $this->user->name]));

        Storage::disk(config('filament.storage_disk'))->assertMissing($this->user->avatar);
    }

    protected function getComponent()
    {
        return Livewire::actingAs($this->user)
            ->test(Profile::class);
    }
}
