<?php

namespace Filament\Tests\Feature;

use Livewire\Livewire;
use Filament\Tests\TestCase;
use Filament\Tests\Database\Models\User;
use Filament\Http\Livewire\Profile;
use Filament\Http\Livewire\Account;

class ProfileTest extends TestCase
{
    public $user;

    public function setUp(): void
    {
        $this->user = User::factory()->create();
    }

    public function test_can_see_profile_and_user_account_form()
    {
        Livewire::actingAs($this->user)
            ->test(Profile::class)
            ->assertSee(__('Profile'))
            ->assertSeeLivewire('filament-account');
    }
}