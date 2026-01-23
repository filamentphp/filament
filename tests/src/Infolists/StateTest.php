<?php

namespace Filament\Tests\Infolists;

use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\KeyValueEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
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

it('can render entry within form that reads form data', function (): void {
    livewire(EntryInFormComponent::class)
        ->assertSuccessful()
        ->assertSchemaComponentStateSet('display', 'Current value: initial', 'form')
        ->assertSeeText('Current value: initial')
        ->set('data.input_field', 'updated value')
        ->assertSchemaComponentStateSet('display', 'Current value: updated value', 'form')
        ->assertSeeText('Current value: updated value');
});

class EntryInFormComponent extends Component implements HasSchemas
{
    use InteractsWithSchemas;

    public array $data = [];

    public function mount(): void
    {
        $this->form->fill([
            'input_field' => 'initial',
        ]);
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->statePath('data')
            ->components([
                TextInput::make('input_field')
                    ->live(),
                TextEntry::make('display')
                    ->label('Display')
                    ->state(fn (Get $get): string => 'Current value: ' . $get('input_field')),
            ]);
    }

    public function render(): string
    {
        return <<<'BLADE'
            <div>
                {{ $this->form }}
            </div>
            BLADE;
    }
}

it('can assert entry state within form using `assertSchemaComponentStateSet()`', function (): void {
    livewire(EntryStateInFormComponent::class)
        ->assertSuccessful()
        ->assertSchemaComponentStateSet('summary', 'Form field value: test input', 'form')
        ->assertSeeText('Form field value: test input');
});

class EntryStateInFormComponent extends Component implements HasSchemas
{
    use InteractsWithSchemas;

    public array $data = [];

    public function mount(): void
    {
        $this->form->fill([
            'input' => 'test input',
        ]);
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->statePath('data')
            ->components([
                TextInput::make('input'),
                TextEntry::make('summary')
                    ->state(fn (Get $get): string => 'Form field value: ' . $get('input')),
            ]);
    }

    public function render(): string
    {
        return <<<'BLADE'
            <div>
                {{ $this->form }}
            </div>
            BLADE;
    }
}
