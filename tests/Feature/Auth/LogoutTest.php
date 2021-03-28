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
    public function can_log_out_with_user_defined_route()
    {
        Route::get('/custom-post-logout', [
            'as' => 'logout.custom',
            'action' => function () {
                return 'This is a custom logout route';
            },
        ]);

        config()->set('filament.logout_redirect_route', 'logout.custom');

        $user = User::factory()->create();

        $this->be($user);

        Livewire::test(Logout::class)
            ->call('submit')
            ->assertRedirect(route('logout.custom'));

        $this->assertGuest();
    }
}
