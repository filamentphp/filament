<?php

namespace Filament\Tests\Feature\Auth;

use Filament\Http\Livewire\Auth\Logout;
use Filament\Models\FilamentUser;
use Filament\Tests\TestCase;
use Livewire\Livewire;

class LogoutTest extends TestCase
{
    public function test_authenticated_user_can_log_out()
    {
        $user = FilamentUser::factory()->create();

        $this->be($user, 'filament');

        Livewire::test(Logout::class)
            ->call('logout')
            ->assertRedirect(route('filament.auth.login'));

        $this->assertGuest('filament');
    }
}
