<?php

namespace Filament\Tests\Forms;

use Filament\Tests\Fixtures\Models\User;
use Filament\Tests\TestCase;
use Illuminate\Support\Facades\Artisan;

uses(TestCase::class);

beforeEach(function (): void {
    Artisan::call('filament:assets');
});

it('handles wizard validation and navigation in the browser', function (): void {
    $this->actingAs(User::factory()->create());

    visit('/wizard-test')
        ->assertSee('Order')
        ->click('button[type="button"] >> text=Next')
        ->assertNoSmoke()
        ->assertSee('Order')
        ->fill('[data-testid="wizard-quantity-input"]', '2')
        ->click('button[type="button"] >> text=Next')
        ->assertNoSmoke()
        ->assertSee('Delivery')
        ->assertNoAccessibilityIssues();
});
