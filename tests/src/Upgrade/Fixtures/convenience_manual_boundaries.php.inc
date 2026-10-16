<?php

namespace Filament\Tests\Upgrade\Fixtures\ConvenienceManualBoundaries;

use Filament\Actions\Action;
use Filament\Actions\ButtonAction;
use Filament\Actions\IconButtonAction;
use Filament\Tables\Filters\MultiSelectFilter;
use Filament\Tables\Filters\SelectFilter;

class CustomButton extends ButtonAction {}

class CustomIconButton extends IconButtonAction {}

class CustomFilter extends MultiSelectFilter {}

/** @param array<IconButtonAction> $iconActions */
function customize(ButtonAction $action, MultiSelectFilter $filter, array $iconActions = []): ButtonAction
{
    $filter->multiple(false);

    return $action;
}

function component(ButtonAction|IconButtonAction|MultiSelectFilter $component): ButtonAction|IconButtonAction|MultiSelectFilter
{
    return $component;
}

function action(ButtonAction|IconButtonAction $action): ButtonAction|IconButtonAction
{
    return $action;
}

ButtonAction::configureUsing(static fn (ButtonAction $action) => $action->link());
MultiSelectFilter::configureUsing(static fn (MultiSelectFilter $filter) => $filter->multiple(false));

$make = 'configureUsing';

return [
    ButtonAction::{$make}(static fn (ButtonAction $action) => $action->link()),
    IconButtonAction::{$make}(static fn (IconButtonAction $action) => $action->link()),
    MultiSelectFilter::{$make}(static fn (MultiSelectFilter $filter) => $filter->multiple(false)),
    MultiSelectFilter::make(...),
    ButtonAction::make(...),
    IconButtonAction::make(...),
    new ButtonAction('unconfigured'),
    new MultiSelectFilter('unconfigured'),
    ButtonAction::class,
    IconButtonAction::class,
    MultiSelectFilter::class,
    $action instanceof IconButtonAction,
    'Filament\Actions\IconButtonAction',
    CustomButton::make('custom'),
    CustomIconButton::make('custom'),
    CustomFilter::make('custom'),
    Action::make('unchanged'),
    SelectFilter::make('unchanged'),
    $class::make('dynamic'),
];
-----
<?php

namespace Filament\Tests\Upgrade\Fixtures\ConvenienceManualBoundaries;

use Filament\Actions\Action;
use Filament\Tables\Filters\SelectFilter;

class CustomButton extends Action {}

class CustomIconButton extends Action {}

class CustomFilter extends SelectFilter {}

/** @param array<Action> $iconActions */
function customize(Action $action, SelectFilter $filter, array $iconActions = []): Action
{
    $filter->multiple(false);

    return $action;
}

function component(Action|SelectFilter $component): Action|SelectFilter
{
    return $component;
}

function action(Action $action): Action
{
    return $action;
}

Action::configureUsing(static fn (Action $action) => $action->link());
SelectFilter::configureUsing(static fn (SelectFilter $filter) => $filter->multiple(false));

$make = 'configureUsing';

return [
    Action::{$make}(static fn (Action $action) => $action->link()),
    Action::{$make}(static fn (Action $action) => $action->link()),
    SelectFilter::{$make}(static fn (SelectFilter $filter) => $filter->multiple(false)),
    SelectFilter::make(...),
    Action::make(...),
    Action::make(...),
    new Action('unconfigured'),
    new SelectFilter('unconfigured'),
    Action::class,
    Action::class,
    SelectFilter::class,
    $action instanceof Action,
    'Filament\Actions\IconButtonAction',
    CustomButton::make('custom'),
    CustomIconButton::make('custom'),
    CustomFilter::make('custom'),
    Action::make('unchanged'),
    SelectFilter::make('unchanged'),
    $class::make('dynamic'),
];
