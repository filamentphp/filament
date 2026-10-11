<?php

namespace Filament\Tests\Fixtures\Pages;

use BackedEnum;
use Filament\Forms\Components\ToggleButtons;
use Filament\Pages\Page;
use Filament\Schemas\Schema;
use Filament\Support\Enums\Size;
use Filament\Support\Icons\Heroicon;

class ToggleButtonsTest extends Page
{
    protected string $view = 'pages.toggle-buttons-test';

    protected static string | BackedEnum | null $navigationIcon = Heroicon::OutlinedSquares2x2;

    protected static ?int $navigationSort = 13;

    public ?array $data = [];

    public array $updatedStates = [];

    public function mount(): void
    {
        $this->form->fill();
    }

    public function form(Schema $form): Schema
    {
        return $form
            ->schema([
                ToggleButtons::make('field')
                    ->label('Test ToggleButtons')
                    ->options(['a' => 'Option A', 'b' => 'Option B'])
                    ->size(Size::Small)
                    ->extraAttributes(['data-testid' => 'toggle-buttons']),
                ToggleButtons::make('grouped_field')
                    ->label('Grouped ToggleButtons')
                    ->options(['a' => 'Option A', 'b' => 'Option B'])
                    ->icons([
                        'a' => Heroicon::Check,
                        'b' => Heroicon::XMark,
                    ])
                    ->size(Size::ExtraSmall)
                    ->grouped()
                    ->extraAttributes(['data-testid' => 'grouped-toggle-buttons']),
                ToggleButtons::make('boolean_field')
                    ->label('Boolean ToggleButtons')
                    ->boolean()
                    ->default(false)
                    ->grouped()
                    ->extraAttributes(['data-testid' => 'boolean-toggle-buttons']),
                ToggleButtons::make('disabled_options')
                    ->label('Disabled options')
                    ->options(['a' => 'Available', 'b' => 'Unavailable', 'c' => 'Unavailable with tooltip'])
                    ->disableOptionWhen(static fn (string $value): bool => $value !== 'a')
                    ->tooltips(['c' => 'This option is unavailable.'])
                    ->extraAttributes(['data-testid' => 'disabled-toggle-buttons']),
                ToggleButtons::make('multiple_field')
                    ->label('Multiple ToggleButtons')
                    ->options(['a' => 'Option A', 'b' => 'Option B'])
                    ->multiple()
                    ->extraAttributes(['data-testid' => 'multiple-toggle-buttons']),
                ...array_map(fn (bool $grouped): ToggleButtons => ToggleButtons::make($grouped ? 'grouped_blur' : 'blur')
                    ->label($grouped ? 'Grouped blur updates' : 'Blur updates')
                    ->options(['a' => 'Option A', 'b' => 'Option B'])
                    ->grouped($grouped)
                    ->live(onBlur: true)
                    ->when($grouped, static fn (ToggleButtons $component): ToggleButtons => $component->stateBindingModifiers(['blur']))
                    ->afterStateUpdated(function (mixed $state, ToggleButtons $component): void {
                        $this->updatedStates[$component->getName()][] = $state;
                    }), [false, true]),
                ...array_map(fn (bool $grouped): ToggleButtons => ToggleButtons::make($grouped ? 'grouped_debounced' : 'debounced')
                    ->label($grouped ? 'Grouped debounced updates' : 'Debounced updates')
                    ->options(['a' => 'Option A', 'b' => 'Option B'])
                    ->grouped($grouped)
                    ->live(debounce: 1000)
                    ->when($grouped, static fn (ToggleButtons $component): ToggleButtons => $component->stateBindingModifiers(['live', 'debounce', '1000ms']))
                    ->afterStateUpdated(function (mixed $state, ToggleButtons $component): void {
                        $this->updatedStates[$component->getName()][] = $state;
                    }), [false, true]),
                ...array_map(fn (string $name): ToggleButtons => ToggleButtons::make($name)
                    ->options(['a' => 'Option A', 'b' => 'Option B'])
                    ->grouped($name === 'deferred_debounced')
                    ->live(onBlur: $name === 'deferred_blur', debounce: $name === 'deferred_debounced' ? 300 : null, condition: false)
                    ->when($name === 'deferred_override', static fn (ToggleButtons $component): ToggleButtons => $component->live()->stateBindingModifiers([]))
                    ->afterStateUpdated(function (mixed $state, ToggleButtons $component): void {
                        $this->updatedStates[$component->getName()][] = $state;
                    }), ['deferred_blur', 'deferred_debounced', 'deferred_override']),
                ToggleButtons::make('live_field')
                    ->options(['a' => 'Option A', 'b' => 'Option B'])
                    ->grouped()
                    ->live()
                    ->afterStateUpdated(function (mixed $state): void {
                        $this->updatedStates['live_field'][] = $state;
                    }),
                ToggleButtons::make('fullWidthStacked')
                    ->label('Full-width stacked ToggleButtons')
                    ->options(['a' => 'Option A', 'b' => 'Option B'])
                    ->fullWidth(),
                ToggleButtons::make('fullWidthInline')
                    ->label('Full-width inline ToggleButtons')
                    ->options([
                        'draft' => 'Draft',
                        'review' => 'Needs additional review',
                        'response' => 'Awaiting customer response',
                        'published' => 'Published publicly',
                    ])
                    ->inline()
                    ->fullWidth(),
                ToggleButtons::make('fullWidthGrouped')
                    ->label('Full-width grouped ToggleButtons')
                    ->options(['a' => 'Option A', 'b' => 'Option B'])
                    ->grouped()
                    ->fullWidth(),
            ])
            ->statePath('data');
    }

    public function save(bool $slow = false): array
    {
        if ($slow) {
            usleep(500000);
        }

        return $this->form->getState();
    }
}
