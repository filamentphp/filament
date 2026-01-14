<?php

namespace Filament\Tests\Infolists\Components;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Schemas\Schema;
use Filament\Tests\TestCase;
use Livewire\Component;

use function Filament\Tests\livewire;

uses(TestCase::class);

it('can render', function (): void {
    livewire(TestComponentWithTextEntry::class)
        ->assertSuccessful()
        ->assertSeeText('Test Name');
});

it('can format state using `formatStateUsing()`', function (): void {
    livewire(TestComponentWithFormattedTextEntry::class)
        ->assertSuccessful()
        ->assertSeeText('HELLO WORLD');
});

it('can display multiple values', function (): void {
    livewire(TestComponentWithMultipleTextEntry::class)
        ->assertSuccessful()
        ->assertSeeText('Tag 1')
        ->assertSeeText('Tag 2')
        ->assertSeeText('Tag 3');
});

class TestComponentWithTextEntry extends Component implements HasSchemas
{
    use InteractsWithSchemas;

    public function infolist(Schema $schema): Schema
    {
        return $schema
            ->state([
                'name' => 'Test Name',
            ])
            ->components([
                TextEntry::make('name'),
            ]);
    }

    public function render(): string
    {
        return <<<'BLADE'
            <div>
                {{ $this->infolist }}
            </div>
            BLADE;
    }
}

class TestComponentWithFormattedTextEntry extends Component implements HasSchemas
{
    use InteractsWithSchemas;

    public function infolist(Schema $schema): Schema
    {
        return $schema
            ->state([
                'message' => 'hello world',
            ])
            ->components([
                TextEntry::make('message')
                    ->formatStateUsing(fn (string $state): string => strtoupper($state)),
            ]);
    }

    public function render(): string
    {
        return <<<'BLADE'
            <div>
                {{ $this->infolist }}
            </div>
            BLADE;
    }
}

class TestComponentWithMultipleTextEntry extends Component implements HasSchemas
{
    use InteractsWithSchemas;

    public function infolist(Schema $schema): Schema
    {
        return $schema
            ->state([
                'tags' => ['Tag 1', 'Tag 2', 'Tag 3'],
            ])
            ->components([
                TextEntry::make('tags'),
            ]);
    }

    public function render(): string
    {
        return <<<'BLADE'
            <div>
                {{ $this->infolist }}
            </div>
            BLADE;
    }
}
