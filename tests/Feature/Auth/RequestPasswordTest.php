<?php

namespace Filament\Tests\Feature\Auth;

use Filament\Http\Livewire\Auth\RequestPassword;
use Filament\Models\User;
use Filament\Tests\TestCase;
use Illuminate\Auth\Notifications\ResetPassword as ResetPasswordNotification;
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
