<?php

namespace Filament\Tests\Feature\Auth;

use Filament\Http\Livewire\Auth\Login;
use Filament\Models\FilamentUser;
use Filament\Tests\TestCase;
use Livewire\Livewire;

class LoginTest extends TestCase
{
    /** @test */
    public function canViewLoginPage()
    {
        $this->get(route('filament.auth.login'))
            ->assertSuccessful()
            ->assertSeeLivewire('filament.auth.login');
    }

    /** @test */
    public function canLogin()
    {
        $user = FilamentUser::factory()->create();

        Livewire::test(Login::class)
            ->set('email', $user->email)
            ->set('password', 'password')
            ->call('submit')
            ->assertRedirect(route('filament.dashboard'));

        $this->assertAuthenticatedAs($user, 'filament');
    }

    /** @test */
    public function isRedirectedIfAlreadyLoggedIn()
    {
        $user = FilamentUser::factory()->create();

        $this->be($user, 'filament');

        $this->get(route('filament.auth.login'))
            ->assertRedirect(route('filament.dashboard'));
    }

    /** @test */
    public function showsAnErrorWhenBadLoginAttempt()
    {
        $user = FilamentUser::factory()->create();

        Livewire::test(Login::class)
            ->set('email', $user->email)
            ->set('password', 'bad-password')
            ->call('submit')
            ->assertHasErrors('email');

        $this->assertGuest('filament');
    }

    /** @test */
    public function emailIsRequired()
    {
        Livewire::test(Login::class)
            ->set('email', null)
            ->call('submit')
            ->assertHasErrors(['email' => 'required']);
    }

    /** @test */
    public function emailIsValidEmail()
    {
        Livewire::test(Login::class)
            ->set('email', 'invalid-email')
            ->call('submit')
            ->assertHasErrors(['email' => 'email']);
    }

    /** @test */
    public function passwordIsRequired()
    {
        $user = FilamentUser::factory()->create();

        Livewire::test(Login::class)
            ->set('password', null)
            ->call('submit')
            ->assertHasErrors(['password' => 'required']);
    }
}
