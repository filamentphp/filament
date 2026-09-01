<?php

namespace Filament\Resources;

use Filament\Facades\Filament;
use Filament\Panel;
use Filament\Resources\RelationManagers\RelationGroup;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Resources\RelationManagers\RelationManagerConfiguration;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Filament\Widgets\Widget;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Traits\Macroable;

/**
 * @template TModel of Model = Model
 * @template TConfiguration of ResourceConfiguration = ResourceConfiguration
 */
abstract class Resource
{
    use Macroable {
        Macroable::__call as dynamicMacroCall;
    }
    use Resource\Concerns\BelongsToCluster;

    /** @use Resource\Concerns\BelongsToParent<TModel> */
    use Resource\Concerns\BelongsToParent;

    /** @use Resource\Concerns\BelongsToTenant<TModel> */
    use Resource\Concerns\BelongsToTenant;

    use Resource\Concerns\CanGenerateUrls;
    use Resource\Concerns\HasAuthorization;
    use Resource\Concerns\HasBreadcrumbs;
    use Resource\Concerns\HasConfiguration;

    /** @use Resource\Concerns\HasGlobalSearch<TModel> */
    use Resource\Concerns\HasGlobalSearch;

    /** @use Resource\Concerns\HasLabels<TModel> */
    use Resource\Concerns\HasLabels;

    use Resource\Concerns\HasNavigation;
    use Resource\Concerns\HasPages;

    /** @use Resource\Concerns\HasRoutes<TModel> */
    use Resource\Concerns\HasRoutes;

    protected static bool $isDiscovered = true;

    /**
     * @var ?class-string<TModel>
     */
    protected static ?string $model = null;

    public static function form(Schema $schema): Schema
    {
        return $schema;
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema;
    }

    public static function table(Table $table): Table
    {
        return $table;
    }

    public static function configureTable(Table $table): void
    {
        $table
            ->modelLabel(static::getModelLabel(...))
            ->pluralModelLabel(static::getPluralModelLabel(...))
            ->recordTitleAttribute(static::getRecordTitleAttribute(...))
            ->recordTitle(static::getRecordTitle(...))
            ->authorizeReorder(static::canReorder(...));

        static::table($table); /** @phpstan-ignore staticMethod.resultUnused */
    }

    /**
     * @return Builder<TModel>
     */
    public static function getEloquentQuery(): Builder
    {
        // Security: Override this method to scope queries to the current
        // user's permissions. By default all records are returned
        // (subject to tenant scoping if active). Failing to scope
        // in multi-user apps can expose unauthorized records.

        $query = static::getModel()::query();

        if (! static::isScopedToTenant()) {
            $panel = Filament::getCurrentOrDefaultPanel();

            if ($panel?->hasTenancy()) {
                $query->withoutGlobalScope($panel->getTenancyScopeName());
            }
        }

        return $query; /** @phpstan-ignore return.type */
    }

    /**
     * @return class-string<TModel>
     */
    public static function getModel(): string
    {
        return static::$model ?? (string) str(class_basename(static::class))
            ->beforeLast('Resource')
            ->prepend(app()->getNamespace() . 'Models\\');
    }

    /**
     * @return array<class-string<RelationManager> | RelationGroup | RelationManagerConfiguration>
     */
    public static function getRelations(): array
    {
        return [];
    }

    /**
     * @return array<class-string<Widget>>
     */
    public static function getWidgets(): array
    {
        return [];
    }

    public static function isEmailVerificationRequired(Panel $panel): bool
    {
        return $panel->isEmailVerificationRequired();
    }

    public static function isDiscovered(): bool
    {
        return static::$isDiscovered;
    }
}
