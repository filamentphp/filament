<?php

namespace Filament\Tests\Fixtures\Pages;

use BackedEnum;
use Filament\Forms\Components\Select;
use Filament\Pages\Page;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;

class SelectTest extends Page
{
    protected string $view = 'pages.select-test';

    protected static string | BackedEnum | null $navigationIcon = Heroicon::OutlinedListBullet;

    protected static ?int $navigationSort = 3;

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill();
    }

    public function form(Schema $form): Schema
    {
        return $form
            ->schema([
                Select::make('status')
                    ->label('Single Select')
                    ->options([
                        'one' => 'One',
                        'two' => 'Two',
                        'three' => 'Three',
                    ])
                    ->native(false)
                    ->required()
                    ->extraAttributes(['data-testid' => 'single-select']),

                Select::make('multiple_status')
                    ->label('Multiple Select')
                    ->options([
                        'apple' => 'Apple',
                        'banana' => 'Banana',
                        'cherry' => 'Cherry',
                        'date' => 'Date',
                    ])
                    ->multiple()
                    ->extraAttributes(['data-testid' => 'multiple-select']),

                Select::make('searchable_status')
                    ->label('Searchable Select')
                    ->options([
                        'red' => 'Red',
                        'green' => 'Green',
                        'blue' => 'Blue',
                        'yellow' => 'Yellow',
                        'purple' => 'Purple',
                    ])
                    ->searchable()
                    ->extraAttributes(['data-testid' => 'searchable-select']),

                Select::make('clearable_status')
                    ->label('Clearable Select')
                    ->options([
                        'active' => 'Active',
                        'inactive' => 'Inactive',
                        'pending' => 'Pending',
                    ])
                    ->native(false)
                    ->extraAttributes(['data-testid' => 'clearable-select']),

                Select::make('dynamic_empty_options')
                    ->label('Dynamic Empty Options')
                    ->options(fn (): array => [])
                    ->native(false)
                    ->noOptionsMessage('No options available')
                    ->extraAttributes(['data-testid' => 'dynamic-empty-options-select']),

                Select::make('dynamic_with_options')
                    ->label('Dynamic With Options')
                    ->options(fn (): array => [
                        'option1' => 'Option 1',
                        'option2' => 'Option 2',
                    ])
                    ->native(false)
                    ->noOptionsMessage('No options available')
                    ->extraAttributes(['data-testid' => 'dynamic-with-options-select']),

                Select::make('dynamic_options_and_search_empty')
                    ->label('Dynamic Options And Search Empty')
                    ->options(fn (): array => [])
                    ->getSearchResultsUsing(fn (string $search): array => [])
                    ->getOptionLabelUsing(fn ($value): ?string => null)
                    ->native(false)
                    ->searchable()
                    ->noOptionsMessage('No options available')
                    ->extraAttributes(['data-testid' => 'dynamic-options-and-search-empty-select']),

                Select::make('static_empty_options')
                    ->label('Static Empty Options')
                    ->options([])
                    ->native(false)
                    ->noOptionsMessage('No options available')
                    ->extraAttributes(['data-testid' => 'static-empty-options-select']),

                Select::make('dynamic_options_with_results')
                    ->label('Dynamic Options With Results')
                    ->options(fn (): array => [
                        'dynamic1' => 'Dynamic Option 1',
                        'dynamic2' => 'Dynamic Option 2',
                    ])
                    ->getSearchResultsUsing(fn (string $search): array => [
                        'result1' => 'Search Result 1',
                    ])
                    ->getOptionLabelUsing(fn ($value): ?string => match ($value) {
                        'dynamic1' => 'Dynamic Option 1',
                        'dynamic2' => 'Dynamic Option 2',
                        'result1' => 'Search Result 1',
                        default => null,
                    })
                    ->native(false)
                    ->searchable()
                    ->noOptionsMessage('No options available')
                    ->extraAttributes(['data-testid' => 'dynamic-options-with-results-select']),

                Select::make('clearable_with_placeholder')
                    ->label('Clearable With Placeholder')
                    ->placeholder('Select an option...')
                    ->options([
                        'first' => 'First',
                        'second' => 'Second',
                        'third' => 'Third',
                    ])
                    ->native(false)
                    ->extraAttributes(['data-testid' => 'clearable-with-placeholder-select']),

                Select::make('native_dynamic_options_context')
                    ->label('Native Dynamic Options Context')
                    ->live()
                    ->options([
                        'first' => 'First context',
                        'second' => 'Second context',
                    ])
                    ->extraInputAttributes(['data-testid' => 'native-dynamic-options-context-select']),

                Select::make('native_dynamic_options_value')
                    ->label('Native Dynamic Options Value')
                    ->options(fn (Get $get): array => match ($get('native_dynamic_options_context')) {
                        'first' => [
                            'first_only' => 'First only',
                        ],
                        'second' => [
                            'second_only' => 'Second only',
                        ],
                        default => [],
                    })
                    ->extraInputAttributes(['data-testid' => 'native-dynamic-options-value-select']),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $this->form->getState();
    }
}
