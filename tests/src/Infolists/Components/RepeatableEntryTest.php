<?php

namespace Filament\Tests\Infolists\Components;

use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Schemas\Schema;
use Filament\Tests\TestCase;
use Livewire\Component;

use function Filament\Tests\livewire;

uses(TestCase::class);

it('can render', function (): void {
    livewire(TestComponentWithRepeatableEntry::class)
        ->assertSuccessful()
        ->assertSeeText('First Item')
        ->assertSeeText('Second Item');
});

it('can render nested entries', function (): void {
    livewire(TestComponentWithNestedRepeatableEntry::class)
        ->assertSuccessful()
        ->assertSeeText('John')
        ->assertSeeText('john@example.com')
        ->assertSeeText('Jane')
        ->assertSeeText('jane@example.com');
});

class TestComponentWithRepeatableEntry extends Component implements HasSchemas
{
    use InteractsWithSchemas;

    public function infolist(Schema $schema): Schema
    {
        return $schema
            ->state([
                'items' => [
                    ['name' => 'First Item'],
                    ['name' => 'Second Item'],
                ],
            ])
            ->components([
                RepeatableEntry::make('items')
                    ->schema([
                        TextEntry::make('name'),
                    ]),
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

class TestComponentWithNestedRepeatableEntry extends Component implements HasSchemas
{
    use InteractsWithSchemas;

    public function infolist(Schema $schema): Schema
    {
        return $schema
            ->state([
                'users' => [
                    ['name' => 'John', 'email' => 'john@example.com'],
                    ['name' => 'Jane', 'email' => 'jane@example.com'],
                ],
            ])
            ->components([
                RepeatableEntry::make('users')
                    ->schema([
                        TextEntry::make('name'),
                        TextEntry::make('email'),
                    ]),
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
