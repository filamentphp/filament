<?php

namespace Filament\Tests\Fixtures\Pages;

use BackedEnum;
use Filament\Forms\Components\KeyValue;
use Filament\Pages\Page;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;

class KeyValueTest extends Page
{
    protected string $view = 'pages.key-value-test';

    protected static string | BackedEnum | null $navigationIcon = Heroicon::OutlinedTableCells;

    protected static ?int $navigationSort = 4;

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill([
            'metadata' => [
                'existing_key' => 'existing_value',
            ],
            'prefilled' => [
                'name' => 'John Doe',
                'email' => 'john@example.com',
                'role' => 'admin',
            ],
        ]);
    }

    public function form(Schema $form): Schema
    {
        return $form
            ->schema([
                KeyValue::make('metadata')
                    ->label('Basic Key-Value')
                    ->keyLabel('Key')
                    ->valueLabel('Value')
                    ->keyPlaceholder('Enter key')
                    ->valuePlaceholder('Enter value')
                    ->extraAttributes(['data-testid' => 'basic-key-value']),

                KeyValue::make('prefilled')
                    ->label('Prefilled Key-Value')
                    ->keyLabel('Property')
                    ->valueLabel('Data')
                    ->keyPlaceholder('Enter property')
                    ->valuePlaceholder('Enter data')
                    ->extraAttributes(['data-testid' => 'prefilled-key-value']),

                KeyValue::make('reorderable_data')
                    ->label('Reorderable Key-Value')
                    ->reorderable()
                    ->keyPlaceholder('Enter key')
                    ->valuePlaceholder('Enter value')
                    ->extraAttributes(['data-testid' => 'reorderable-key-value']),

                KeyValue::make('readonly_keys')
                    ->label('Read-Only Keys')
                    ->editableKeys(false)
                    ->keyPlaceholder('Key')
                    ->valuePlaceholder('Value')
                    ->default([
                        'fixed_key' => 'editable_value',
                    ])
                    ->extraAttributes(['data-testid' => 'readonly-keys-key-value']),

                KeyValue::make('readonly_values')
                    ->label('Read-Only Values')
                    ->editableValues(false)
                    ->keyPlaceholder('Key')
                    ->valuePlaceholder('Value')
                    ->default([
                        'editable_key' => 'fixed_value',
                    ])
                    ->extraAttributes(['data-testid' => 'readonly-values-key-value']),

                KeyValue::make('not_addable')
                    ->label('Not Addable')
                    ->addable(false)
                    ->keyPlaceholder('Key')
                    ->valuePlaceholder('Value')
                    ->default([
                        'key1' => 'value1',
                    ])
                    ->extraAttributes(['data-testid' => 'not-addable-key-value']),

                KeyValue::make('not_deletable')
                    ->label('Not Deletable')
                    ->deletable(false)
                    ->keyPlaceholder('Key')
                    ->valuePlaceholder('Value')
                    ->default([
                        'key1' => 'value1',
                    ])
                    ->extraAttributes(['data-testid' => 'not-deletable-key-value']),

                KeyValue::make('custom_labels')
                    ->label('Custom Labels')
                    ->keyLabel('Setting Name')
                    ->valueLabel('Setting Value')
                    ->keyPlaceholder('Enter setting name')
                    ->valuePlaceholder('Enter setting value')
                    ->addActionLabel('Add New Setting')
                    ->extraAttributes(['data-testid' => 'custom-labels-key-value']),

                KeyValue::make('disabled_field')
                    ->label('Disabled Key-Value')
                    ->disabled()
                    ->keyPlaceholder('Key')
                    ->valuePlaceholder('Value')
                    ->default([
                        'disabled_key' => 'disabled_value',
                    ])
                    ->extraAttributes(['data-testid' => 'disabled-key-value']),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $this->form->getState();
    }
}
