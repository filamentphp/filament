<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use App\Models\User;
use App\Providers\RouteServiceProvider;
use Filament\Http\Livewire\Auth\Login;
use Filament\Tests\TestCase;

class LoginTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_see_login_form()
    {
        $this->get('/filament/login')
            ->assertSuccessful()
            ->assertSeeLivewire('login');
    }

    /*
    public function test_existing_user_can_log_in()
    {
        $user = User::factory()->create();

        Livewire::test(Login::class)
            ->set('email', $user->email)
            ->set('password', 'password')
            ->call('login')
            ->assertRedirect(RouteServiceProvider::HOME);

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

    public function test_bad_credentials_show_error_on_login()
    {
        $component = Livewire::test(Login::class)
            ->set('email', 'example@example.com')
            ->set('password', 'wrongpassword')
            ->call('login')
            ->assertHasErrors('password');
    }
    */
}