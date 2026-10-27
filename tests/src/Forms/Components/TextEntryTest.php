<?php

namespace Filament\Tests\Forms\Components;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;
use Filament\Tests\Fixtures\Livewire\Livewire;
use Filament\Tests\TestCase;
use Illuminate\Support\HtmlString;

use function Filament\Tests\livewire;

uses(TestCase::class);

it('can render', function (): void {
    livewire(TestComponentWithFormTextEntry::class)
        ->assertSuccessful();
});

it('can render with dynamic content', function (): void {
    livewire(TestComponentWithDynamicFormTextEntry::class)
        ->assertSuccessful();
});

it('can set and get string `state()`', function (): void {
    $placeholder = TextEntry::make('notice')->state('Hello world');
    expect($placeholder->getState())->toBe('Hello world');
});

it('can set and get `Htmlable` `state()`', function (): void {
    $html = new HtmlString('<strong>Bold</strong>');
    $placeholder = TextEntry::make('notice')->state($html);
    expect($placeholder->getState())->toBe($html);
});

it('returns `null` for `getState()` by default', function (): void {
    $placeholder = TextEntry::make('notice')->container(Schema::make(Livewire::make()));
    expect($placeholder->getState())->toBeNull();
});

class TestComponentWithFormTextEntry extends Livewire
{
    public function form(Schema $form): Schema
    {
        return $form
            ->schema([
                TextEntry::make('notice')
                    ->state('This is a placeholder'),
            ])
            ->statePath('data');
    }
}

class TestComponentWithDynamicFormTextEntry extends Livewire
{
    public function form(Schema $form): Schema
    {
        return $form
            ->schema([
                TextEntry::make('info')
                    ->state(fn (): string => 'Dynamic content: ' . now()->format('Y-m-d')),
            ])
            ->statePath('data');
    }
}
