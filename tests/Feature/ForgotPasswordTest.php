<?php

namespace Filament\Tests\Feature;

use Illuminate\Auth\Notifications\ResetPassword as ResetPasswordNotification;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Password;
use Livewire\Livewire;
use Filament\Tests\TestCase;
use Filament\Tests\Database\Models\User;
use Filament\Http\Livewire\Auth\ForgotPassword;

class ForgotPasswordTest extends TestCase
{
    use WithoutMiddleware;

    public $user;

    public function setUp(): void
    {
        parent::setUp();

        Notification::fake();
        Notification::assertNothingSent();

        $this->user = User::factory()->create();
    }

    public function test_can_see_forgot_password_form()
    {
        $this->get(route('filament.password.forgot')) 
            ->assertSuccessful()
            ->assertSee(__('filament::auth.resetPassword'));
    }

    public function test_email_is_required()
    {
        Livewire::test(ForgotPassword::class)
            ->call('submit')
            ->assertHasErrors(['email' => 'required']);
    }

    public function test_email_is_valid_email()
    {
        Livewire::test(ForgotPassword::class)
            ->set('email', 'invalid')
            ->call('submit')
            ->assertHasErrors(['email' => 'email']);
    }

    public function test_email_matches_existing_user()
    {
        Livewire::test(ForgotPassword::class)
            ->set('email', 'not@exists.com')
            ->call('submit')
            ->assertHasErrors('email')
            ->assertSee(__('passwords.user'));
    }

    public function test_reset_token_not_created_when_already_exists()
    {
        Password::createToken($this->user);
        Livewire::test(ForgotPassword::class)
            ->set('email', $this->user->email)
            ->call('submit')
            ->assertHasErrors('email')
            ->assertSee(__('passwords.throttled'));
    }

    public function test_can_request_password_reset_token()
    {
        // $this->withoutExceptionHandling();
        
        Livewire::test(ForgotPassword::class)
            ->set('email', $this->user->email)
            ->call('submit')
            ->assertDispatchedBrowserEvent('notify', __('passwords.sent'));

        $this->assertDatabaseHas('password_resets', [
            'email' => $this->user->email,
        ]);

        Notification::assertSentTo([$this->user], ResetPasswordNotification::class);
    }
}