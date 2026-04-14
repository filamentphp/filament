<?php

namespace Filament\Tests\Forms\Components;

use Filament\Forms\Components\Placeholder;
use Filament\Schemas\Schema;
use Filament\Tests\Fixtures\Livewire\Livewire;
use Filament\Tests\TestCase;
use Illuminate\Support\HtmlString;

use function Filament\Tests\livewire;

uses(TestCase::class);

it('can render', function (): void {
    livewire(TestComponentWithPlaceholder::class)
        ->assertSuccessful();
});

it('can render with dynamic content', function (): void {
    livewire(TestComponentWithDynamicPlaceholder::class)
        ->assertSuccessful();
});

it('can set and get string `content()`', function (): void {
    $placeholder = Placeholder::make('notice')->content('Hello world');
    expect($placeholder->getContent())->toBe('Hello world');
});

it('can set and get `Htmlable` `content()`', function (): void {
    $html = new HtmlString('<strong>Bold</strong>');
    $placeholder = Placeholder::make('notice')->content($html);
    expect($placeholder->getContent())->toBe($html);
});

it('returns `null` for `getContent()` by default', function (): void {
    $placeholder = Placeholder::make('notice')->container(Schema::make(Livewire::make()));
    expect($placeholder->getContent())->toBeNull();
});

class TestComponentWithPlaceholder extends Livewire
{
    public function form(Schema $form): Schema
    {
        return $form
            ->schema([
                Placeholder::make('notice')
                    ->content('This is a placeholder'),
            ])
            ->statePath('data');
    }
}

class TestComponentWithDynamicPlaceholder extends Livewire
{
    public function form(Schema $form): Schema
    {
        return $form
            ->schema([
                Placeholder::make('info')
                    ->content(fn (): string => 'Dynamic content: ' . now()->format('Y-m-d')),
            ])
            ->statePath('data');
    }
}
