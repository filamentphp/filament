<?php

namespace Filament\Tests\Forms\Components;

use Filament\Forms\Components\KeyValue;
use Filament\Schemas\Schema;
use Filament\Tests\Fixtures\Livewire\Livewire;
use Filament\Tests\Fixtures\Models\User;
use Filament\Tests\TestCase;
use Illuminate\Support\Facades\Artisan;

use function Filament\Tests\livewire;

uses(TestCase::class);

beforeEach(function (): void {
    Artisan::call('filament:assets');
});

it('can render', function (): void {
    livewire(TestComponentWithKeyValue::class)
        ->assertSuccessful();
});

it('can set and get state', function (): void {
    livewire(TestComponentWithKeyValue::class)
        ->fillForm(['metadata' => ['key1' => 'value1', 'key2' => 'value2']])
        ->assertSchemaStateSet(['metadata' => ['key1' => 'value1', 'key2' => 'value2']]);
});

it('can render when not addable', function (): void {
    livewire(TestComponentWithReadOnlyKeyValue::class)
        ->assertSuccessful();
});

it('can render when reorderable', function (): void {
    livewire(TestComponentWithReorderableKeyValue::class)
        ->assertSuccessful();
});

class TestComponentWithKeyValue extends Livewire
{
    public function form(Schema $form): Schema
    {
        return $form
            ->schema([
                KeyValue::make('metadata'),
            ])
            ->statePath('data');
    }
}

class TestComponentWithReadOnlyKeyValue extends Livewire
{
    public function form(Schema $form): Schema
    {
        return $form
            ->schema([
                KeyValue::make('metadata')
                    ->addable(false)
                    ->deletable(false),
            ])
            ->statePath('data');
    }
}

class TestComponentWithReorderableKeyValue extends Livewire
{
    public function form(Schema $form): Schema
    {
        return $form
            ->schema([
                KeyValue::make('metadata')->reorderable(),
            ])
            ->statePath('data');
    }
}

it('can add a new row in the browser', function (): void {
    $this->actingAs(User::factory()->create());

    visit('/key-value-test')
        ->assertSee('Key Value Test')
        ->assertSee('Basic Key-Value')
        ->waitForText('Add row')
        ->click('[data-testid="basic-key-value"] .fi-fo-key-value-add-action-ctn')
        ->assertNoSmoke()
        ->assertNoAccessibilityIssues();

    visit('/key-value-test')
        ->inDarkMode()
        ->assertNoAccessibilityIssues();
});

it('can click add row button multiple times in the browser', function (): void {
    $this->actingAs(User::factory()->create());

    visit('/key-value-test')
        ->assertSee('Reorderable Key-Value')
        ->waitForText('Add row')
        ->click('[data-testid="reorderable-key-value"] .fi-fo-key-value-add-action-ctn')
        ->waitForText('Key')
        ->click('[data-testid="reorderable-key-value"] .fi-fo-key-value-add-action-ctn')
        ->assertNoSmoke();
});

it('does not show add button when `addable(false)` in the browser', function (): void {
    $this->actingAs(User::factory()->create());

    visit('/key-value-test')
        ->assertSee('Not Addable')
        ->assertMissing('[data-testid="not-addable-key-value"] .fi-fo-key-value-add-action-ctn')
        ->assertNoSmoke();
});

it('does not show delete button when `deletable(false)` in the browser', function (): void {
    $this->actingAs(User::factory()->create());

    visit('/key-value-test')
        ->assertSee('Not Deletable')
        ->assertMissing('[data-testid="not-deletable-key-value"] tbody .fi-has-action')
        ->assertNoSmoke();
});

it('shows custom add action label in the browser', function (): void {
    $this->actingAs(User::factory()->create());

    visit('/key-value-test')
        ->assertSee('Custom Labels')
        ->assertSee('Add New Setting')
        ->assertNoSmoke();
});

it('displays custom key and value labels in the browser', function (): void {
    $this->actingAs(User::factory()->create());

    visit('/key-value-test')
        ->assertSee('Setting Name')
        ->assertSee('Setting Value')
        ->assertNoSmoke();
});

it('does not show add button when component is disabled in the browser', function (): void {
    $this->actingAs(User::factory()->create());

    visit('/key-value-test')
        ->assertSee('Disabled Key-Value')
        ->assertMissing('[data-testid="disabled-key-value"] .fi-fo-key-value-add-action-ctn')
        ->assertNoSmoke();
});

it('shows reorder handles when `reorderable()` in the browser', function (): void {
    $this->actingAs(User::factory()->create());

    visit('/key-value-test')
        ->assertSee('Reorderable Key-Value')
        ->click('[data-testid="reorderable-key-value"] .fi-fo-key-value-add-action-ctn')
        ->waitForText('Key')
        ->assertPresent('[data-testid="reorderable-key-value"] .fi-fo-key-value-table-row-sortable-handle')
        ->assertNoSmoke();
});
