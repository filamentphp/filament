<?php

namespace Filament\Resources\RelationManagers;

use Filament\Resources\Form;
use Filament\Resources\Table;
use Filament\Tables;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;
use Livewire\Component;

class RelationManager extends Component implements Tables\Contracts\HasTable
{
    use Tables\Concerns\InteractsWithTable;

    public Model $ownerRecord;

    protected static ?string $recordTitleAttribute = null;

    protected static string $relationship;

    protected static ?string $inverseRelationship = null;

    protected ?Form $resourceForm = null;

    protected ?Table $resourceTable = null;

    protected static ?string $label = null;

    protected static ?string $pluralLabel = null;

    protected static ?string $title = null;

    protected static string $view;

    public function mount(): void
    {
        if (! $this->canViewAny()) {
            $this->skipRender();
        }
    }

    protected function getTableQueryStringIdentifier(): ?string
    {
        return lcfirst(class_basename(static::class));
    }

    protected function getResourceForm(): Form
    {
        if (! $this->resourceForm) {
            $this->resourceForm = static::form(Form::make()->columns(2));
        }

        return $this->resourceForm;
    }

    protected function callHook(string $hook): void
    {
        if (! method_exists($this, $hook)) {
            return;
        }

        $this->{$hook}();
    }

    protected function can(string $action, ?Model $record = null): bool
    {
        $policy = Gate::getPolicyFor($model = $this->getRelatedModel());

        if ($policy === null || (! method_exists($policy, $action))) {
            return true;
        }

        return Gate::check($action, $record ?? $model);
    }

    protected function canViewAny(): bool
    {
        return $this->can('viewAny');
    }

    public static function form(Form $form): Form
    {
        return $form;
    }

    public function getInverseRelationshipName(): string
    {
        return static::$inverseRelationship ?? (string) Str::of(class_basename($this->ownerRecord))
            ->lower()
            ->plural()
            ->camel();
    }

    public static function table(Table $table): Table
    {
        return $table;
    }

    public static function getRelationshipName(): string
    {
        return static::$relationship;
    }

    public static function getTitle(): string
    {
        return static::$title ?? Str::title(static::getPluralRecordLabel());
    }

    public static function getRecordTitleAttribute(): ?string
    {
        return static::$recordTitleAttribute;
    }

    public static function getRecordTitle(?Model $record): ?string
    {
        return $record?->getAttribute(static::getRecordTitleAttribute()) ?? $record?->getKey();
    }

    protected static function getRecordLabel(): string
    {
        return static::$label ?? Str::singular(static::getPluralRecordLabel());
    }

    protected static function getPluralRecordLabel(): string
    {
        return static::$pluralLabel ?? (string) Str::of(static::getRelationshipName())
            ->kebab()
            ->replace('-', ' ');
    }

    protected function getRelatedModel(): string
    {
        return $this->getRelationship()->getQuery()->getModel()::class;
    }

    protected function getRelationship(): Relation
    {
        return $this->ownerRecord->{static::getRelationshipName()}();
    }

    protected function getDefaultTableSortColumn(): ?string
    {
        return $this->getResourceTable()->getDefaultSortColumn();
    }

    protected function getDefaultTableSortDirection(): ?string
    {
        return $this->getResourceTable()->getDefaultSortDirection();
    }

    protected function getTableActions(): array
    {
        return $this->getResourceTable()->getActions();
    }

    protected function getTableBulkActions(): array
    {
        return $this->getResourceTable()->getBulkActions();
    }

    protected function getTableColumns(): array
    {
        return $this->getResourceTable()->getColumns();
    }

    protected function getTableFilters(): array
    {
        return $this->getResourceTable()->getFilters();
    }

    protected function getTableHeaderActions(): array
    {
        return $this->getResourceTable()->getHeaderActions();
    }

    protected function getTableHeading(): ?string
    {
        return static::getTitle();
    }

    protected function getTableQuery(): Builder
    {
        return $this->getRelationship()->getQuery();
    }

    public function render(): View
    {
        return view(static::$view, $this->getViewData());
    }

    protected function getViewData(): array
    {
        return [];
    }
}
