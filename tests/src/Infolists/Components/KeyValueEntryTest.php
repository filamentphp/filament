<?php

namespace Filament\Tests\Infolists\Components;

use Filament\Infolists\Components\KeyValueEntry;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Schemas\Schema;
use Filament\Tests\TestCase;
use Livewire\Component;

use function Filament\Tests\livewire;

uses(TestCase::class);

it('can render', function (): void {
    livewire(TestComponentWithKeyValueEntry::class)
        ->assertSuccessful()
        ->assertSeeText('name')
        ->assertSeeText('John Doe')
        ->assertSeeText('email')
        ->assertSeeText('john@example.com');
});

it('can render with custom key and value labels', function (): void {
    livewire(TestComponentWithLabeledKeyValueEntry::class)
        ->assertSuccessful()
        ->assertSeeText('Setting')
        ->assertSeeText('Value');
});

class TestComponentWithKeyValueEntry extends Component implements HasSchemas
{
    use InteractsWithSchemas;

    public function infolist(Schema $schema): Schema
    {
        return $schema
            ->state([
                'metadata' => [
                    'name' => 'John Doe',
                    'email' => 'john@example.com',
                ],
            ])
            ->components([
                KeyValueEntry::make('metadata'),
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

class TestComponentWithLabeledKeyValueEntry extends Component implements HasSchemas
{
    use InteractsWithSchemas;

    public function infolist(Schema $schema): Schema
    {
        return $schema
            ->state([
                'settings' => [
                    'theme' => 'dark',
                    'language' => 'en',
                ],
            ])
            ->components([
                KeyValueEntry::make('settings')
                    ->keyLabel('Setting')
                    ->valueLabel('Value'),
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
