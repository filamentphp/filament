<?php

namespace Filament\Tests\Feature\Auth;

use Filament\Filament;
use Filament\Http\Livewire\Auth\Login;
use Filament\Models\FilamentUser;
use Filament\Tests\TestCase;
use Illuminate\Support\Str;
use Livewire\Livewire;

class LoginTest extends TestCase
{
    /** @test */
    public function can_view_login_page()
    {
        $this->get(route('filament.auth.login'))
            ->assertSuccessful()
            ->assertSeeLivewire('filament.auth.login');
    }

    /** @test */
    public function is_redirected_if_already_logged_in()
    {
        $user = FilamentUser::factory()->create();

        $this->be($user, 'filament');

        $this->get(route('filament.auth.login'))
            ->assertRedirect(route('filament.dashboard'));
    }

    /** @test */
    public function a_user_can_login()
    {
        $user = FilamentUser::factory()->create();

        Livewire::test(Login::class)
            ->set('email', $user->email)
            ->set('password', 'password')
            ->call('authenticate')
            ->assertRedirect(route('filament.dashboard'));

        $this->assertAuthenticatedAs($user, 'filament');
    }

    /** @test */
    public function bad_login_attempt_shows_message()
    {
        $user = FilamentUser::factory()->create();

        Livewire::test(Login::class)
            ->set('email', $user->email)
            ->set('password', 'bad-password')
            ->call('authenticate')
            ->assertHasErrors('email');

        $this->assertGuest('filament');
    }

    /** @test */
    public function email_is_required()
    {
        Livewire::test(Login::class)
            ->set('password', 'password')
            ->call('authenticate')
            ->assertHasErrors(['email' => 'required']);
    }

    /** @test */
    public function email_must_be_valid_email()
    {
        Livewire::test(Login::class)
            ->set('email', 'invalid-email')
            ->set('password', 'password')
            ->call('authenticate')
            ->assertHasErrors(['email' => 'email']);
    }

    /** @test */
    public function password_is_required()
    {
        $user = FilamentUser::factory()->create();

        Livewire::test(Login::class)
            ->set('email', $user->email)
            ->call('authenticate')
            ->assertHasErrors(['password' => 'required']);
    }
}
