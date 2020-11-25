<?php

namespace Filament\Tests\Feature;

use Livewire\Livewire;
use Filament\Tests\TestCase;
use Filament;
use Filament\Tests\Database\Models\User;
use Filament\Http\Livewire\Auth\Logout;

class LogoutTest extends TestCase
{
    public function test_authenticated_user_can_see_log_out_button()
    {
        $this->withoutExceptionHandling();
        $user = User::factory()->create();
        $this->actingAs($user)
            ->get(Filament::home())
            ->assertSeeLivewire('filament-logout');
    }

    public function test_authenticated_user_can_log_out()
    {
        $user = User::factory()->create();
        Livewire::actingAs($user)
            ->test(Logout::class)
            ->call('logout')
            ->assertRedirect(route('filament.login'))
            ->assertSessionHas('message', __('filament::auth.loggedout'));

        $this->assertGuest();
    }

    public function test_guest_cannot_log_out()
    {
        Livewire::test(Logout::class)
            ->call('logout')
            ->assertRedirect(route('filament.login'));
    }
}