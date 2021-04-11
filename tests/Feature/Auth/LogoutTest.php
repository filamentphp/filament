<?php

namespace Filament\Tests\Feature\Auth;

use Filament\Http\Livewire\Auth\Logout;
use Filament\Models\User;
use Filament\Tests\TestCase;
use Illuminate\Support\Facades\Route;
use Livewire\Livewire;

class LogoutTest extends TestCase
{
    /** @test */
    public function can_log_out()
    {
        $user = User::factory()->create();

        $this->be($user);

        Livewire::test(Logout::class)
            ->call('submit')
            ->assertRedirect(route('filament.auth.login'));

        $this->assertGuest();
    }

    /** @test */
    public function can_redirect_to_custom_route_after_log_out()
    {
        Route::get('custom-logout-redirect', function () {
            return true;
        })->name('filament.testing.auth.custom-logout-redirect');

        $this->app['config']->set('filament.auth.logout_redirect_route', 'filament.testing.auth.custom-logout-redirect');

        $user = User::factory()->create();

        $this->be($user);

        Livewire::test(Logout::class)
            ->call('submit')
            ->assertRedirect(route('filament.testing.auth.custom-logout-redirect'));

        $this->assertGuest();
    }
}
