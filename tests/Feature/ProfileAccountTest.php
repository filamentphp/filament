<?php

namespace Filament\Tests\Feature;

use Livewire\Livewire;
use Filament\Tests\TestCase;
use Filament\Tests\Database\Models\User;
use Filament\Http\Livewire\Account;

class ProfileAccountTest extends TestCase
{
    public $user;

    public function setUp(): void
    {
        $this->user = User::factory()->create();
    }

    public function test_name_is_required()
    {
        $this->accountComponent()
            ->set('user.name', '')
            ->call('submit')
            ->assertHasErrors(['user.name' => 'required']);
    }

    public function test_name_is_minimum_two_characters()
    {
        $this->accountComponent()
            ->set('user.name', 'a')
            ->call('submit')
            ->assertHasErrors(['user.name' => 'min']);
    }

    public function test_email_is_required()
    {
        $this->accountComponent()
            ->set('user.email', '')
            ->call('submit')
            ->assertHasErrors(['user.email' => 'required']);
    }

    public function test_email_is_valid_email()
    {
        $this->accountComponent()
            ->set('user.email', 'Something')
            ->call('submit')
            ->assertHasErrors(['user.email' => 'email']);
    }

    public function test_email_is_unique()
    {
        User::factory()->create(['email' => 'test@example.com']);

        $this->accountComponent()
            ->set('user.email', 'test@example.com')
            ->call('submit')
            ->assertHasErrors(['user.email' => 'unique']);
    }

    public function test_password_may_be_left_blank()
    {
        $this->accountComponent()
            ->set('password', '')
            ->call('submit')
            ->assertHasNoErrors(['password' => 'nullable']);
    }

    public function test_password_is_minimum_eight_characters()
    {
        $this->accountComponent()
            ->set('password', 'test')
            ->call('submit')
            ->assertHasErrors(['password' => 'min']);
    }

    public function test_password_must_be_confirmed()
    {
        $this->accountComponent()
            ->set('password', 'test')
            ->call('submit')
            ->assertHasErrors(['password' => 'confirmed']);
    }

    public function test_password_confirmation_may_be_left_blank()
    {
        $this->accountComponent()
            ->set('password_confirmation', '')
            ->call('submit')
            ->assertHasNoErrors(['password_confirmation' => 'nullable']);
    }

    public function test_password_confirmation_matches_password()
    {
        $this->accountComponent()
            ->set('password', 'test')
            ->set('password_confirmation', 'test2')
            ->call('submit')
            ->assertHasErrors(['password_confirmation' => 'same']);
    }

    public function test_account_saved()
    {
        $this->accountComponent()
            ->call('submit')
            ->assertDispatchedBrowserEvent('notify', __('Account saved!'));
    }

    public function accountComponent()
    {
        return Livewire::test(Account::class, ['user' => $this->user]);
    }
}