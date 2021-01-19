<?php

namespace Filament\Tests\Feature\Auth;

use Filament\Http\Livewire\Auth\RequestPassword;
use Filament\Models\FilamentUser;
use Filament\Tests\TestCase;
use Illuminate\Auth\Notifications\ResetPassword as ResetPasswordNotification;
use Illuminate\Support\Facades\Notification;
use Livewire\Livewire;

class RequestPasswordTest extends TestCase
{
    /** @test */
    public function canViewPasswordResetRequestPage()
    {
        $this->get(route('filament.auth.password.request'))
            ->assertSuccessful()
            ->assertSeeLivewire('filament.auth.request-password');
    }

    /** @test */
    public function canRequestPasswordReset()
    {
        Notification::fake();

        $user = FilamentUser::factory()->create();

        Livewire::test(RequestPassword::class)
            ->set('email', $user->email)
            ->call('submit')
            ->assertHasNoErrors()
            ->assertDispatchedBrowserEvent('notify');

        Notification::assertSentTo($user, ResetPasswordNotification::class);
    }

    /** @test */
    public function isRedirectedIfAlreadyLoggedIn()
    {
        $user = FilamentUser::factory()->create();

        $this->be($user, 'filament');

        $this->get(route('filament.auth.password.request'))
            ->assertRedirect(route('filament.dashboard'));
    }

    /** @test */
    public function showsAnErrorWhenBadRequestAttempt()
    {
        Notification::fake();

        $email = $this->faker->safeEmail;

        Livewire::test(RequestPassword::class)
            ->set('email', $email)
            ->call('submit')
            ->assertHasErrors('email');

        Notification::assertNothingSent();
    }

    /** @test */
    public function emailIsRequired()
    {
        Livewire::test(RequestPassword::class)
            ->set('email', null)
            ->call('submit')
            ->assertHasErrors(['email' => 'required']);
    }

    /** @test */
    public function emailIsValidEmail()
    {
        Livewire::test(RequestPassword::class)
            ->set('email', 'invalid-email')
            ->call('submit')
            ->assertHasErrors(['email' => 'email']);
    }
}
