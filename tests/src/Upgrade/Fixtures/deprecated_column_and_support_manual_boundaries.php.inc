<?php

declare(strict_types=1);

namespace Filament\Tests\Upgrade\Fixtures\DeprecatedColumnAndSupportManualBoundaries;

use Closure;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\Exports\ExportColumn;
use Filament\Infolists\Components\TextEntry;
use Filament\QueryBuilder\Constraints\Constraint;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Support\Concerns\HasBadge;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\SelectColumn;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Database\Eloquent\Model;

function manual(IconColumn $icon, Action $action, ActionGroup $group, Tab $tab, array $arguments, string | int $badge, $unknown): void
{
    $icon->options(...$arguments);
    $action->indicator(...$arguments);
    $tab->indicator(...$arguments);
    $action->indicator(indicator: 17);
    $group->indicator(2.5);
    $action->indicator($badge);
    $group->indicator($unknown);
    $callback = $action->indicator(...);
    $callback = $group->indicator(...);
    $method = 'indicator';
    $action->{$method}('New');
    $action->{'indicator'}('New');
    $callback = [$action, 'indicator'];
    $unknown->indicator('New');
    IconColumn::options([]);
}

class AliasOverride extends IconColumn
{
    public function options(array | Arrayable | Closure $customOptions): static
    {
        return parent::options($customOptions);
    }
}

class InheritedAliasOverride extends AliasOverride {}

class ReplacementOverride extends IconColumn
{
    public function icons(array | Closure $customIcons): static
    {
        return $this;
    }
}

class BadgeOverride extends Action
{
    public function badge(string | Closure | null $customBadge = null): static
    {
        return $this;
    }
}

trait CustomIndicator
{
    public function indicator(string $label): static
    {
        return $this;
    }
}

class TraitOverride extends Tab
{
    use CustomIndicator;
}

class AdaptedVisibility extends Tab
{
    use HasBadge {
        badgeColor as protected;
    }
}

class RelationshipOverride extends ExportColumn
{
    public function hasRelationship(Model $customRecord): bool
    {
        return true;
    }
}

class StandaloneBadge
{
    use HasBadge;
}

function overrides(InheritedAliasOverride $alias, ReplacementOverride $replacement, BadgeOverride $badge, TraitOverride $trait, AdaptedVisibility $visibility, RelationshipOverride $export, StandaloneBadge $standalone, IconColumn | ReplacementOverride $union, Model $record): void
{
    $alias->options(customOptions: []);
    $alias->label('Status')->options(customOptions: []);
    $replacement->options([]);
    $replacement?->label('Status')?->options([]);
    $badge->indicator(indicator: 'New');
    $badge->label('Edit')->indicator('New');
    $trait->indicator(label: 'New');
    $visibility->indicatorColor('warning');
    $export->queriesRelationships($record);
    $standalone->indicator('New');
    $union->options([]);
}

function unrelated(SelectColumn $select, SelectFilter $filter, Constraint $constraint, TextEntry $entry, Section $section, IconColumn | SelectColumn $union): void
{
    $select->options([]);
    $filter->options([])->indicator('Active');
    $filter->queriesRelationships();
    $constraint->queriesRelationships();
    $entry->badge();
    $section->hasRelationship();
    $union->options([]);
}
