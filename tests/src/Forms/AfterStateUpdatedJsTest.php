<?php

namespace Filament\Tests\Forms;

use Filament\Tests\Fixtures\Models\User;
use Filament\Tests\TestCase;
use Illuminate\Support\Facades\Artisan;

uses(TestCase::class);

beforeEach(function (): void {
    Artisan::call('filament:assets');
});

it('can use `$set()` in `afterStateUpdatedJs()` to set another field value', function (): void {
    $this->actingAs(User::factory()->create());

    visit('/after-state-updated-js-test')
        ->assertSee('Name')
        ->assertSee('Email')
        ->fill('#form\.name', 'John Doe')
        ->wait(1)
        ->assertValue('#form\.email', 'john.doe@example.com')
        ->fill('#form\.name', 'Jane Smith')
        ->wait(1)
        ->assertValue('#form\.email', 'jane.smith@example.com')
        ->fill('#form\.flex_name', 'Jane Doe')
        ->wait(1)
        ->assertValue('#form\.flex_email', 'jane.doe@example.com')
        ->fill('#form\.flex_name', 'John Smith')
        ->wait(1)
        ->assertValue('#form\.flex_email', 'john.smith@example.com')
        ->assertNoSmoke()
        ->assertNoAccessibilityIssues();

    visit('/after-state-updated-js-test')
        ->inDarkMode()
        ->assertNoAccessibilityIssues();
});
