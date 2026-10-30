<?php

namespace Filament\Tests\Upgrade\Fixtures\LazyLoadingCalls;

use Filament\Forms\Components\LivewireField;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationGroup;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Components\Livewire;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Filament\Widgets\Widget;
use Illuminate\Database\Eloquent\Model;

function configure(Schema $schema, Table $table, ?Table $nullableTable, Livewire $embedded, LivewireField $field, TextInput $input, Tabs $tabs, Tab $tab, RelationGroup $group, Model $record, array $arguments): void
{
    $schema->deferLoading(condition: false)->isLoadingDeferred();
    $table->deferLoading(...$arguments)->isLoadingDeferred();
    $nullableTable?->deferLoading();
    $callback = $table->deferLoading(...);
    $embedded->lazy(condition: false)->isLazy();
    $field->lazy()->isLazy();
    $tab->deferBadge(condition: true)->isBadgeDeferred();
    $group->deferBadge()->isBadgeDeferred();
    $tabs->getDeferredTabBadges();
    $tabs->hasDeferredBadges();
    Widget::isLazy();
    RelationManager::isLazy();
    RelationManager::isBadgeDeferred(ownerRecord: $record, pageClass: 'Page');
    $schema->lazy()->isLazy();
    $input->lazy()->isLazy();
    $input->live(onBlur: true);
    $nullableTable?->deferLoading()->isLoadingDeferred();
    $nullableInput = rand(0, 1) ? $input : null;
    $nullableInput?->lazy();
}

function mixedReceivers(Schema|Table $loading, Livewire|TextInput $different, Table|string $partial, Table|Unrelated $unrelated, $unknown): void
{
    $loading->isLoadingDeferred();
    $different->lazy();
    $partial->deferLoading();
    $unrelated->deferLoading();
    $unknown->lazy();
    $method = 'lazy';
    $different->{$method}();
    $callback = [$different, 'lazy'];
}

class Unrelated
{
    public function lazy(): void {}
    public function deferLoading(): void {}
}
-----
<?php

namespace Filament\Tests\Upgrade\Fixtures\LazyLoadingCalls;

use Filament\Forms\Components\LivewireField;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationGroup;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Components\Livewire;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Filament\Widgets\Widget;
use Illuminate\Database\Eloquent\Model;

function configure(Schema $schema, Table $table, ?Table $nullableTable, Livewire $embedded, LivewireField $field, TextInput $input, Tabs $tabs, Tab $tab, RelationGroup $group, Model $record, array $arguments): void
{
    $schema->lazyLoaded(condition: false)->isLazyLoaded();
    $table->lazyLoaded(...$arguments)->isLazyLoaded();
    $nullableTable?->lazyLoaded();
    $callback = $table->lazyLoaded(...);
    $embedded->lazyLoaded(condition: false)->isLazyLoaded();
    $field->lazyLoaded()->isLazyLoaded();
    $tab->lazyLoadedBadge(condition: true)->isBadgeLazyLoaded();
    $group->lazyLoadedBadge()->isBadgeLazyLoaded();
    $tabs->getLazyLoadedTabBadges();
    $tabs->hasLazyLoadedBadges();
    Widget::isLazyLoaded();
    RelationManager::isLazyLoaded();
    RelationManager::isBadgeLazyLoaded(ownerRecord: $record, pageClass: 'Page');
    $schema->live(onBlur: true)->isLiveOnBlur();
    $input->live(onBlur: true)->isLiveOnBlur();
    $input->live(onBlur: true);
    $nullableTable?->lazyLoaded()->isLazyLoaded();
    $nullableInput = rand(0, 1) ? $input : null;
    $nullableInput?->live(onBlur: true);
}

function mixedReceivers(Schema|Table $loading, Livewire|TextInput $different, Table|string $partial, Table|Unrelated $unrelated, $unknown): void
{
    $loading->isLazyLoaded();
    $different->lazy();
    $partial->deferLoading();
    $unrelated->deferLoading();
    $unknown->lazy();
    $method = 'lazy';
    $different->{$method}();
    $callback = [$different, 'lazy'];
}

class Unrelated
{
    public function lazy(): void {}
    public function deferLoading(): void {}
}
