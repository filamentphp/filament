<?php

namespace Tests\Feature;

use Filament\Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Filament\Tests\Database\Models\User;
use Filament\Http\Livewire\Auth\Login;

class LoginTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_see_login_form()
    {
        // $this->withoutExceptionHandling();
        $this->get(route('filament.login'))
            ->assertSuccessful()
            ->assertSee(trans('filament::auth.signin'));
    }

    public function test_existing_user_can_log_in()
    {
        $user = User::factory()->create();

        Livewire::test(Login::class)
            ->set('email', $user->email)
            ->set('password', 'password')
            ->call('login')
            ->assertRedirect(route('filament.dashboard'));

        $this->assertAuthenticated();
    }

    public function test_email_is_required()
    {
        Livewire::test(Login::class)
            ->call('login')
            ->assertHasErrors(['email' => 'required']);
    }

    public function test_email_is_valid_email()
    {
        Livewire::test(Login::class)
            ->set('email', 'Something')
            ->call('login')
            ->assertHasErrors(['email' => 'email']);
    }

    public function test_password_is_required()
    {
        Livewire::test(Login::class)
            ->call('login')
            ->assertHasErrors(['password' => 'required']);
    }

    public function test_password_is_minimum_eight_characters()
    {
        Livewire::test(Login::class)
            ->set('password', 'test')
            ->call('login')
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
                        ->call('login');

        return $component;
    }
}