<?php

use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\TextEntry;
use Filament\Navigation\NavigationGroup;
use Filament\Schemas\Components\Section;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Filament\Tests\Fixtures\Livewire\Livewire;
use Filament\Tests\TestCase;

uses(TestCase::class);

it('merges each stateless attribute callback registration independently', function (string $profile): void {
    [$component, $setter, $getter, $arguments] = match ($profile) {
        'component' => [TextInput::make('value'), 'extraAttributes', 'getExtraAttributes', []],
        'alpine' => [Section::make('value'), 'extraAlpineAttributes', 'getExtraAlpineAttributes', []],
        'input' => [TextInput::make('value'), 'extraInputAttributes', 'getExtraInputAttributes', []],
        'field wrapper' => [TextInput::make('value'), 'extraFieldWrapperAttributes', 'getExtraFieldWrapperAttributes', []],
        'entry wrapper' => [TextEntry::make('value'), 'extraEntryWrapperAttributes', 'getExtraEntryWrapperAttributes', []],
        'dropdown' => [ActionGroup::make([]), 'extraDropdownAttributes', 'getExtraDropdownAttributes', []],
        'modal overlay' => [Action::make('value'), 'extraModalOverlayAttributes', 'getExtraModalOverlayAttributes', []],
        'modal window' => [Action::make('value'), 'extraModalWindowAttributes', 'getExtraModalWindowAttributes', []],
        'picker trigger' => [DateTimePicker::make('value'), 'extraTriggerAttributes', 'getExtraTriggerAttributes', []],
        'sidebar' => [NavigationGroup::make('value'), 'extraSidebarAttributes', 'getExtraSidebarAttributes', []],
        'topbar' => [NavigationGroup::make('value'), 'extraTopbarAttributes', 'getExtraTopbarAttributes', []],
        'cell' => [TextColumn::make('value'), 'extraCellAttributes', 'getExtraCellAttributes', []],
        'header' => [TextColumn::make('value'), 'extraHeaderAttributes', 'getExtraHeaderAttributes', []],
        'image' => [ImageColumn::make('value'), 'extraImgAttributes', 'getExtraImgAttributes', []],
        'record link' => [Table::make(new class extends Livewire implements HasTable
        {
            use InteractsWithTable;
        }), 'extraRecordLinkAttributes', 'getExtraRecordLinkAttributes', [[]]],
    };

    app()->instance('attribute-evaluation-counter', (object) ['value' => 0]);

    $component->{$setter}(['class' => 'existing']);

    foreach (range(1, 2) as $registration) {
        $component->{$setter}(static fn (): array => ['data-evaluation-' . ++app('attribute-evaluation-counter')->value => 'yes'], merge: true);
    }

    expect($component->{$getter}(...$arguments))->toEqual([
        'data-evaluation-2' => 'yes',
        'data-evaluation-1' => 'yes',
        'class' => 'existing',
    ]);

    app('attribute-evaluation-counter')->value = 0;
    $attributes = function (): array {
        return ['data-evaluation-' . ++app('attribute-evaluation-counter')->value => 'yes'];
    };

    $component->{$setter}($attributes);
    $component->{$setter}($attributes, merge: true);

    expect(app('attribute-evaluation-counter')->value)->toBe(0)
        ->and($component->{$getter}(...$arguments))->toEqual([
            'data-evaluation-1' => 'yes',
            'data-evaluation-2' => 'yes',
        ]);

    $component->{$setter}($attributes, merge: true);
    $component->{$setter}(['class' => 'replacement']);
    $component->{$setter}(['class' => 'other'], merge: true);
    $component->{$setter}(['class' => 'replacement'], merge: true);

    expect($component->{$getter}(...$arguments))->toBe(['class' => 'other replacement'])
        ->and(app('attribute-evaluation-counter')->value)->toBe(2);
})->with([
    'component', 'alpine', 'input', 'field wrapper', 'entry wrapper',
    'dropdown', 'modal overlay', 'modal window', 'picker trigger', 'sidebar',
    'topbar', 'cell', 'header', 'image', 'record link',
]);
