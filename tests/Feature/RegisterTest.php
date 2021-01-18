<?php

namespace Filament\Tests\Feature;

use Livewire\Livewire;
use Filament\Tests\TestCase;
use Filament\Facades\Filament;
use Filament\Http\Livewire\Auth\Register;
use Filament\Tests\Database\Models\User;

class RegisterTest extends TestCase
{
    public function test_can_see_register_form()
    {
        $this->get(route('filament.register'))
            ->assertSuccessful()
            ->assertSee(__('filament::auth.createAccount'));
    }

    public function test_can_register()
    {
        Livewire::test(Register::class)
            ->set('name', 'Me')
            ->set('email', 'me@example.com')
            ->set('password', 'password')
            ->set('password_confirmation', 'password')
            ->call('submit')
            ->assertHasNoErrors(['name', 'email', 'password', 'password_confirmation'])
            ->assertRedirect(Filament::home());

        $this->assertDatabaseHas('users', [
            'name' => 'Me',
            'email' => 'me@example.com',
        ]);

        $this->assertAuthenticated();
    }

    public function test_name_is_required()
    {
        Livewire::test(Register::class)
            ->call('submit')
            ->assertHasErrors(['name' => 'required']);
    }

    public function test_name_is_minimum_two_characters()
    {
        Livewire::test(Register::class)
            ->set('name', 'a')
            ->call('submit')
            ->assertHasErrors(['name' => 'min']);
    }

    public function test_email_is_required()
    {
        Livewire::test(Register::class)
            ->call('submit')
            ->assertHasErrors(['email' => 'required']);
    }

    public function test_email_is_valid_email()
    {
        Livewire::test(Register::class)
            ->set('email', 'Something')
            ->call('submit')
            ->assertHasErrors(['email' => 'email']);
    }

    public function test_email_is_unique()
    {
        User::factory()->create(['email' => 'test@example.com']);

        Livewire::test(Register::class)
            ->set('email', 'test@example.com')
            ->assertHasErrors(['email' => 'unique']);
    }

    public function test_password_is_required()
    {
        Livewire::test(Register::class)
            ->call('submit')
            ->assertHasErrors(['password' => 'required']);
    }

    public function test_password_is_minimum_eight_characters()
    {
        Livewire::test(Register::class)
            ->set('password', 'test')
            ->call('submit')
            ->assertHasErrors(['password' => 'min']);
    }

    public function test_password_must_be_confirmed()
    {
        Livewire::test(Register::class)
            ->set('password', 'test')
            ->call('submit')
            ->assertHasErrors(['password' => 'confirmed']);
    }

    public function test_password_confirmation_is_required()
    {
        Livewire::test(Register::class)
            ->call('submit')
            ->assertHasErrors(['password_confirmation' => 'required']);
    }

    public function test_password_confirmation_matches_password()
    {
        Livewire::test(Register::class)
            ->set('password', 'test')
            ->set('password_confirmation', 'test2')
            ->call('submit')
            ->assertHasErrors(['password_confirmation' => 'same']);
    }
}