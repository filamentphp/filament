<?php

use Filament\Tests\Models\User;
use Filament\Tests\TestCase;

uses(TestCase::class);

it('can log a user out', function () {
    $this
        ->actingAs(User::factory()->createOne())
        ->post(route('filament.auth.logout'))
        ->assertRedirect(route('filament.auth.login'));

    $this->assertGuest();
});
