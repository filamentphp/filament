<?php

namespace Filament\Tests\Feature;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Password;
use Livewire\Livewire;
use Filament\Tests\TestCase;
use Filament;
use Filament\Tests\Database\Models\User;
use Filament\Http\Livewire\Auth\{
    Login,
    ResetPassword,
};

class ResetPasswordTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        $this->token = Password::createToken($this->user);
    }

    public function test_can_see_forgot_password_form_with_valid_token()
    {
        $this->get(route('filament.password.reset', $this->token))
            ->assertSuccessful()
            ->assertSee(__('Reset Password'));
    }

    public function test_email_is_required()
    {
        Livewire::test(ResetPassword::class, ['token' => $this->token])
            ->call('submit')
            ->assertHasErrors(['email' => 'required']);
    }

    public function test_email_is_valid_email()
    {
        Livewire::test(ResetPassword::class, ['token' => $this->token])
            ->set('email', 'Something')
            ->call('submit')
            ->assertHasErrors(['email' => 'email']);
    }

    public function test_password_is_required()
    {
        Livewire::test(ResetPassword::class, ['token' => $this->token])
            ->call('submit')
            ->assertHasErrors(['password' => 'required']);
    }

    public function test_password_is_minimum_eight_characters()
    {
        Livewire::test(ResetPassword::class, ['token' => $this->token])
            ->set('password', 'test')
            ->call('submit')
            ->assertHasErrors(['password' => 'min']);
    }

    public function test_password_is_confirmed()
    {
        Livewire::test(ResetPassword::class, ['token' => $this->token])
            ->set('password', 'test')
            ->set('password_confirmation', 'testzz')
            ->call('submit')
            ->assertHasErrors(['password' => 'confirmed']);
    }

    public function test_cant_reset_password_with_expired_token()
    {
        DB::table('password_resets')
            ->where('email', $this->user->email)
            ->update(['created_at' => now()->subDay()]);

        Livewire::test(ResetPassword::class, ['token' => $this->token])
            ->set('email', $this->user->email)
            ->set('password', 'newpassword')
            ->set('password_confirmation', 'newpassword')
            ->call('submit')
            ->assertHasErrors('email')
            ->assertSee(__('passwords.token'));
    }

    public function test_email_must_match_token_to_reset_password()
    {
        Livewire::test(ResetPassword::class, ['token' => $this->token])
            ->set('email', 'other@example.com')
            ->set('password', 'newpassword')
            ->set('password_confirmation', 'newpassword')
            ->call('submit')
            ->assertHasErrors('email')
            ->assertSee(__('passwords.user'));
    }

    public function test_user_can_reset_password_with_valid_token()
    {
        $dashboard = Filament::home();
        
        Livewire::test(ResetPassword::class, ['token' => $this->token])
            ->set('email', $this->user->email)
            ->set('password', 'newpassword')
            ->set('password_confirmation', 'newpassword')
            ->call('submit')
            ->assertHasNoErrors()
            ->assertRedirect($dashboard);

        Livewire::test(Login::class)
            ->set('email', $this->user->email)
            ->set('password', 'newpassword')
            ->call('submit')
            ->assertRedirect($dashboard);
    }
}