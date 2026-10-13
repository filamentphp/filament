<?php

namespace Filament\Tests\Schemas\Components\Concerns;

use Filament\Tests\Fixtures\Models\User;
use Filament\Tests\TestCase;
use Illuminate\Support\Facades\Artisan;

uses(TestCase::class);

beforeEach(function (): void {
    Artisan::call('filament:assets');
});

it('can use `$set()` in `afterStateUpdatedJs()` to set another field value', function (): void {
    $this->actingAs(User::factory()->create());

    $page = visit('/after-state-updated-js-test');

    $page
        ->fill('#form\.name', 'John Doe')
        ->assertValue('#form\.email', 'john.doe@example.com')
        ->fill('#form\.name', 'Jane Smith')
        ->assertValue('#form\.email', 'jane.smith@example.com')
        ->fill('#form\.flex_name', 'Jane Doe')
        ->assertValue('#form\.flex_email', 'jane.doe@example.com')
        ->fill('#form\.flex_name', 'John Smith')
        ->assertValue('#form\.flex_email', 'john.smith@example.com')
        ->assertNoSmoke()
        ->assertNoAccessibilityIssues();

    $page->inDarkMode()
        ->assertNoSmoke()
        ->assertNoAccessibilityIssues();
});
