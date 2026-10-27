<?php

namespace Filament\Tests\Upgrade\Fixtures\PersistentManualBoundaries;

use Closure;
use Filament\Actions\Action;
use Filament\Actions\ButtonAction;
use Filament\Actions\IconButtonAction;
use Filament\AvatarProviders\Contracts\AvatarProvider;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Concerns\RestrictsFileUploadsToSchemaComponents as UploadRestriction;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\BooleanColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\MultiSelectFilter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\View\TablesRenderHook;

class CustomHooks extends TablesRenderHook
{
    public const TOOLBAR_TOGGLE_COLUMN_TRIGGER_AFTER = 'custom::after';
    public const TOOLBAR_COLUMN_MANAGER_TRIGGER_BEFORE = 'custom::before';
}

class AdaptedUploads
{
    use InteractsWithSchemas;
    use UploadRestriction {
        shouldRestrictFileUploadsToSchemaComponents as restrictUploads;
    }
}

trait StandaloneUploadRestriction
{
    use UploadRestriction;
}

class CustomProvider implements AvatarProvider
{
    public function get(string $record): string
    {
        return $record;
    }
}

class CustomImageColumn extends ImageColumn
{
    public function getHeight(): string
    {
        return 'custom legacy getter';
    }
}

function configureUnion(ImageColumn | CustomImageColumn $column): void
{
    $column->getHeight();
}

class CustomSchema extends Schema
{
    protected bool | Closure $isLoadingDeferred = false;

    protected bool | Closure $isLazyLoaded = true;

    public function isLoadingDeferred(): bool
    {
        return false;
    }

    public function isLazyLoaded(): bool
    {
        return true;
    }
}

function inspectUnion(Schema | CustomSchema $schema): bool
{
    return $schema->isLoadingDeferred();
}

class ReviewableSchema extends Schema
{
    protected bool | Closure $isLoadingDeferred = false;

    public function isLoadingDeferred(): bool
    {
        return parent::isLoadingDeferred();
    }

    public function inspectProperty(): bool
    {
        return $this->evaluate($this->isLoadingDeferred);
    }
}

$filter = MultiSelectFilter::make('configured-later');
$filter->multiple(false);

return [
    CustomHooks::TOOLBAR_TOGGLE_COLUMN_TRIGGER_AFTER,
    CustomHooks::TOOLBAR_TOGGLE_COLUMN_TRIGGER_BEFORE,
    ButtonAction::make('typed')->label(fn (ButtonAction $action) => $action->getLabel()),
    IconButtonAction::make('unknown')->tap($configure),
    MultiSelectFilter::make('callback')->options(fn (SelectFilter $filter) => $filter->multiple(false)->getOptions()),
    MultiSelectFilter::make('nullsafe')->options(fn (SelectFilter $filter) => $filter?->multiple(false)->getOptions()),
    BadgeColumn::make('callback')->color(fn (TextColumn $column) => $column->badge(false)->getColor()),
    BooleanColumn::make('callback')->icon(fn (IconColumn $column) => $column?->boolean(false)->getIcon()),
    Action::make('canonical')->button()->label('Canonical'),
];
