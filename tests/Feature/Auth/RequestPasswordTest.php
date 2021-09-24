<?php

namespace Filament\Tests\Feature\Auth;

use Filament\Http\Livewire\Auth\RequestPassword;
use Filament\Models\User;
use Filament\Tests\TestCase;
use Illuminate\Auth\Notifications\ResetPassword as ResetPasswordNotification;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Notification;
use Livewire\Livewire;

class RequestPasswordTest extends TestCase
{
    /** @test */
    public function can_request_password_reset()
    {
        Notification::fake();

        $user = User::factory()->create();

        Livewire::test(RequestPassword::class)
            ->set('email', $user->email)
            ->call('submit')
            ->assertHasNoErrors()
            ->assertDispatchedBrowserEvent('notify');

        Notification::assertSentTo($user, ResetPasswordNotification::class);
    }

    /** @test */
    public function can_request_password_reset_with_web_guard()
    {
        Notification::fake();

        // Configure filament to use default Laravel's auth guard
        Config::set('filament.auth.guard', 'web');

        // Set Laravel's default user model to filament's one, so it has filament's attributes
        // and uses 'IsFilamentUser' trait (needed to send the notification)
        Config::set('auth.providers.users.model', User::class);

        $user = User::factory()->create();

        Livewire::test(RequestPassword::class)
            ->set('email', $user->email)
            ->call('submit')
            ->assertHasNoErrors()
            ->assertDispatchedBrowserEvent('notify');

        Notification::assertSentTo($user, ResetPasswordNotification::class);
    }

    /** @test */
    public function can_view_password_reset_request_page()
    {
        $this->get(route('filament.auth.password.request'))
            ->assertSuccessful()
            ->assertSeeLivewire('filament.core.auth.request-password');
    }

    /** @test */
    public function email_is_required()
    {
        Livewire::test(RequestPassword::class)
            ->set('email', null)
            ->call('submit')
            ->assertHasErrors(['email' => 'required']);
    }

    /** @test */
    public function email_is_valid_email()
    {
        Livewire::test(RequestPassword::class)
            ->set('email', 'invalid-email')
            ->call('submit')
            ->assertHasErrors(['email' => 'email']);
    }

    /** @test */
    public function shows_an_error_when_bad_request_attempt()
    {
        Notification::fake();

        $email = $this->faker->safeEmail;

        Livewire::test(RequestPassword::class)
            ->set('email', $email)
            ->call('submit')
            ->assertHasErrors('email');

        Notification::assertNothingSent();
    }
}
