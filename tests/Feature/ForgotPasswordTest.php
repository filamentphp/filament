<?php

namespace Tests\Feature;

use Illuminate\Auth\Notifications\ResetPassword as ResetPasswordNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Password;
use Livewire\Livewire;
use Filament\Tests\TestCase;
use Filament\Http\Livewire\Auth\ForgotPassword;
use Filament\Tests\Database\Models\User;

class ForgotPasswordTest extends TestCase
{
    use WithoutMiddleware, RefreshDatabase;

    public $user;

    public function setUp(): void
    {
        parent::setUp();
        Notification::fake();
        $this->user = User::factory()->create();
    }

    public function test_can_see_forgot_password_form()
    {
        $this->get(route('filament.password.forgot')) 
            ->assertSuccessful()
            ->assertSee(__('Reset Password'));
    }

    public function test_email_is_required()
    {
        Livewire::test(ForgotPassword::class)
            ->call('sendEmail')
            ->assertHasErrors(['email' => 'required']);
    }

    public function test_email_is_valid_email()
    {
        Livewire::test(ForgotPassword::class)
            ->set('email', 'invalid')
            ->call('sendEmail')
            ->assertHasErrors(['email' => 'email']);
    }

    public function test_email_matches_existing_user()
    {
        Livewire::test(ForgotPassword::class)
            ->set('email', 'not@exists.com')
            ->call('sendEmail')
            ->assertHasErrors('email')
            ->assertSee(__('passwords.user'));
    }

    public function test_reset_token_not_created_when_already_exists()
    {
        Password::createToken($this->user);
        Livewire::test(ForgotPassword::class)
            ->set('email', $this->user->email)
            ->call('sendEmail')
            ->assertHasErrors('email')
            ->assertSee(__('passwords.throttled'));
    }

    public function test_can_request_password_reset_token()
    {
        $this->withoutExceptionHandling();
        Notification::assertNothingSent();

        Livewire::test(ForgotPassword::class)
            ->set('email', $this->user->email)
            ->call('sendEmail')
            ->assertSet('message', __('passwords.sent'));

        $this->assertDatabaseHas('password_resets', [
            'email' => $this->user->email,
        ]);

        Notification::assertSentTo([$this->user], ResetPasswordNotification::class);
    }
}