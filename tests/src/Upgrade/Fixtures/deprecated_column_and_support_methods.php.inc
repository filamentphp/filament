<?php

declare(strict_types=1);

namespace Filament\Tests\Upgrade\Fixtures\DeprecatedColumnAndSupportMethods;

use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\Exports\ExportColumn;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Tables\Columns\Column;
use Filament\Tables\Columns\IconColumn;
use Illuminate\Database\Eloquent\Model;

function configure(IconColumn $icon, Column $column, ExportColumn $export, Action $action, ActionGroup $group, Tab $tab, Model $record, ?IconColumn $nullableIcon, ?Action $nullableAction, array $arguments): void
{
    $icon->options(options: ['heroicon-o-check' => 'published'])->disableClick(condition: false);
    $icon->OPTIONS(fn (): array => ['heroicon-o-clock' => 'draft']);
    $nullableIcon?->options(options: [])?->disableClick();
    IconColumn::make('status')->options([])->disableClick();
    $column->disableClick();
    $column->disableClick(condition: fn (): bool => false);
    $column->disableClick(...$arguments);
    $column->queriesRelationships(record: $record);
    $export->queriesRelationships($record);
    $export->queriesRelationships(...$arguments);
    $action->indicator(indicator: fn (): string => 'Pending')->indicatorColor(color: 'warning');
    $action->indicator(indicator: 42);
    $group->indicator(3.5);
    $group->indicator(null)->indicatorColor(fn (): string => 'success');
    $tab->indicator(indicator: 17)->indicatorColor(color: null);
    $nullableAction?->indicator(indicator: null)?->indicatorColor(color: 'gray');
    $action->indicatorColor(...$arguments);
    $callback = $icon->options(...);
    $callback = $column->disableClick(...);
    $callback = $export->queriesRelationships(...);
    $callback = $action->indicator(...);
    $callback = $tab->indicator(...);
    $callback = $tab->indicatorColor(...);
    $action->badge();
    $group->badge();
}

class CustomIcon extends IconColumn
{
    public function configureIcon(): static
    {
        $this->options(options: []);

        return $this->disableClick();
    }
}

class CustomAction extends Action {}

class OtherCustomAction extends Action {}

function numericIndicators(int $count, float $rating): array
{
    return [Action::make('count')->indicator($count)->getBadge(), ActionGroup::make([])->indicator($rating)->getBadge()];
}

function subclasses(CustomIcon $icon, CustomAction $action, CustomAction | OtherCustomAction $union): void
{
    $icon->label('Status')->options([])->disableClick(false);
    $action->label('Edit')->indicator(indicator: 'New');
    $union->indicator(indicator: 'Union');
    $union?->label('Edit')?->indicator(indicator: 'Chained union');
}
-----
<?php

declare(strict_types=1);

namespace Filament\Tests\Upgrade\Fixtures\DeprecatedColumnAndSupportMethods;

use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\Exports\ExportColumn;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Tables\Columns\Column;
use Filament\Tables\Columns\IconColumn;
use Illuminate\Database\Eloquent\Model;

function configure(IconColumn $icon, Column $column, ExportColumn $export, Action $action, ActionGroup $group, Tab $tab, Model $record, ?IconColumn $nullableIcon, ?Action $nullableAction, array $arguments): void
{
    $icon->icons(icons: ['heroicon-o-check' => 'published'])->disabledClick(condition: false);
    $icon->icons(fn (): array => ['heroicon-o-clock' => 'draft']);
    $nullableIcon?->icons(icons: [])?->disabledClick();
    IconColumn::make('status')->icons([])->disabledClick();
    $column->disabledClick();
    $column->disabledClick(condition: fn (): bool => false);
    $column->disabledClick(...$arguments);
    $column->hasRelationship(record: $record);
    $export->hasRelationship($record);
    $export->hasRelationship(...$arguments);
    $action->badge(badge: fn (): string => 'Pending')->badgeColor(color: 'warning');
    $action->badge(badge: (string) 42);
    $group->badge((string) 3.5);
    $group->badge(null)->badgeColor(fn (): string => 'success');
    $tab->badge(badge: 17)->badgeColor(color: null);
    $nullableAction?->badge(badge: null)?->badgeColor(color: 'gray');
    $action->badgeColor(...$arguments);
    $callback = $icon->icons(...);
    $callback = $column->disabledClick(...);
    $callback = $export->hasRelationship(...);
    $callback = $action->indicator(...);
    $callback = $tab->badge(...);
    $callback = $tab->badgeColor(...);
    $action->badge();
    $group->badge();
}

class CustomIcon extends IconColumn
{
    public function configureIcon(): static
    {
        $this->icons(icons: []);

        return $this->disabledClick();
    }
}

class CustomAction extends Action {}

class OtherCustomAction extends Action {}

function numericIndicators(int $count, float $rating): array
{
    return [Action::make('count')->badge((string) $count)->getBadge(), ActionGroup::make([])->badge((string) $rating)->getBadge()];
}

function subclasses(CustomIcon $icon, CustomAction $action, CustomAction | OtherCustomAction $union): void
{
    $icon->label('Status')->icons([])->disabledClick(false);
    $action->label('Edit')->badge(badge: 'New');
    $union->badge(badge: 'Union');
    $union?->label('Edit')?->badge(badge: 'Chained union');
}
