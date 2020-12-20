<?php

namespace Filament\Tests\Feature;

use Livewire\Livewire;
use Filament\Tests\TestCase;
use Filament\Facades\Filament;
use Filament\Tests\Database\Models\User;
use Filament\Http\Livewire\Auth\Login;

class LoginTest extends TestCase
{
    public function test_can_see_login_form()
    {
        // $this->withoutExceptionHandling();
        $this->get(route('filament.login'))
            ->assertSuccessful()
            ->assertSee(__('filament::auth.signin'));
    }

    public function test_existing_user_can_log_in()
    {
        $user = User::factory()->create();

        Livewire::test(Login::class)
            ->set('email', $user->email)
            ->set('password', 'password')
            ->call('submit')
            ->assertRedirect(Filament::home());

        $this->assertAuthenticated();
    }

    public function test_redirect_if_logged_in()
    {
        $user = User::factory()->create();
        Livewire::actingAs($user)
            ->test(Login::class)
            ->assertRedirect(Filament::home());
    }

    public function test_email_is_required()
    {
        Livewire::test(Login::class)
            ->call('submit')
            ->assertHasErrors(['email' => 'required']);
    }

    public function test_email_is_valid_email()
    {
        Livewire::test(Login::class)
            ->set('email', 'Something')
            ->call('submit')
            ->assertHasErrors(['email' => 'email']);
    }

    public function test_password_is_required()
    {
        Livewire::test(Login::class)
            ->call('submit')
            ->assertHasErrors(['password' => 'required']);
    }

    public function test_password_is_minimum_eight_characters()
    {
        Livewire::test(Login::class)
            ->set('password', 'test')
            ->call('submit')
            ->assertHasErrors(['password' => 'min']);
    }

    public function test_bad_credentials_show_error()
    {
        $this->invalid_login()
            ->assertHasErrors('email');
    }

    public function test_bad_credentials_show_error_due_to_login_throttling()
    {
        for ($i = 0; $i < 4; $i++) { // 4 invalid login attemps
            $this->invalid_login();
        }

        // 5th invalid login attempt should return validation error
        $this->invalid_login()
            ->assertHasErrors('email');
    }

    private function invalid_login()
    {
        $component = Livewire::test(Login::class)
                        ->set('email', 'example@example.com')
                        ->set('password', 'wrongpassword')
                        ->call('submit');

        return $component;
    }
}