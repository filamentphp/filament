<?php

namespace Filament\Tests\Infolists\Components;

use Filament\Infolists\Components\ColorEntry;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Schemas\Schema;
use Filament\Tests\TestCase;
use Livewire\Component;

use function Filament\Tests\livewire;

uses(TestCase::class);

it('can render', function (): void {
    livewire(TestComponentWithColorEntry::class)
        ->assertSuccessful();
});

it('can render with hex color', function (): void {
    livewire(TestComponentWithHexColorEntry::class)
        ->assertSuccessful();
});

class TestComponentWithColorEntry extends Component implements HasSchemas
{
    use InteractsWithSchemas;

    public function infolist(Schema $schema): Schema
    {
        return $schema
            ->state([
                'color' => '#ff0000',
            ])
            ->components([
                ColorEntry::make('color'),
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

class TestComponentWithHexColorEntry extends Component implements HasSchemas
{
    use InteractsWithSchemas;

    public function infolist(Schema $schema): Schema
    {
        return $schema
            ->state([
                'primary_color' => '#3b82f6',
            ])
            ->components([
                ColorEntry::make('primary_color'),
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
