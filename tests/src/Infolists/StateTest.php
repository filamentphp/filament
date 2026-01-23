<?php

namespace Filament\Tests\Infolists;

use Filament\Infolists\Components\KeyValueEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Schemas\Schema;
use Filament\Tests\TestCase;
use Livewire\Component;

use function Filament\Tests\livewire;

uses(TestCase::class);

it('can resolve `getConstantState()` for TextEntry in nested sections', function (): void {
    livewire(NestedInfolistComponent::class)
        ->assertOk()
        ->assertSchemaComponentStateSet('page.block.content', 'deeply nested content', 'infolist')
        ->assertSeeText('deeply nested content');
});

it('can resolve `getConstantState()` for KeyValueEntry in nested sections', function (): void {
    livewire(NestedKeyValueInfolistComponent::class)
        ->assertOk()
        ->assertSchemaComponentStateSet('page.block.configuration', ['config_key' => 'config_value'], 'infolist')
        ->assertSeeText('config_key')
        ->assertSeeText('config_value');
});

class NestedInfolistComponent extends Component implements HasSchemas
{
    use InteractsWithSchemas;

    public function infolist(Schema $schema): Schema
    {
        return $schema
            ->constantState([
                'page' => [
                    'block' => [
                        'content' => 'deeply nested content',
                    ],
                ],
            ])
            ->components([
                Section::make('Page')
                    ->statePath('page')
                    ->schema([
                        Section::make('Block')
                            ->statePath('block')
                            ->schema([
                                TextEntry::make('content'),
                            ]),
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

class NestedKeyValueInfolistComponent extends Component implements HasSchemas
{
    use InteractsWithSchemas;

    public function infolist(Schema $schema): Schema
    {
        return $schema
            ->constantState([
                'page' => [
                    'block' => [
                        'configuration' => [
                            'config_key' => 'config_value',
                        ],
                    ],
                ],
            ])
            ->components([
                Section::make('Page')
                    ->statePath('page')
                    ->schema([
                        Section::make('Block')
                            ->statePath('block')
                            ->schema([
                                KeyValueEntry::make('configuration'),
                            ]),
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
