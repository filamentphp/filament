<?php

namespace Filament\Tests\Feature\Auth;

use Filament\Http\Livewire\Auth\Logout;
use Filament\Models\FilamentUser;
use Filament\Tests\TestCase;
use Livewire\Livewire;

class LogoutTest extends TestCase
{
    /** @test */
    public function can_log_out()
    {
        $user = FilamentUser::factory()->create();

        $this->be($user);

        Livewire::test(Logout::class)
            ->call('submit')
            ->assertRedirect(route('filament.auth.login'));

        $this->assertGuest();
    }
}
