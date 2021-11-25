<?php

namespace Filament\Resources\RelationManagers;

use Filament\Resources\Form;
use Filament\Resources\Table;
use Filament\Tables;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;
use Livewire\Component;

class RelationManager extends Component implements Tables\Contracts\HasTable
{
    use Tables\Concerns\InteractsWithTable;

    public Model $ownerRecord;

    protected static string $relationship;

    protected static string $view;

    protected function callHook(string $hook): void
    {
        if (! method_exists($this, $hook)) {
            return;
        }

        $this->{$hook}();
    }

    protected function can(string $action, ?Model $record = null): bool
    {
        $policy = Gate::getPolicyFor($this->getRelatedModel());

        if ($policy === null || ! method_exists($policy, $action)) {
            return true;
        }

        return Gate::check($action, $record);
    }

    protected function canAccess(): bool
    {
        return $this->can('viewAny');
    }

    protected function canCreate(): bool
    {
        return $this->can('create');
    }

    protected function canEdit(Model $record): bool
    {
        return $this->can('update', $record);
    }

    protected function canDelete(Model $record): bool
    {
        return $this->can('delete', $record);
    }

    protected function canDeleteAny(): bool
    {
        return $this->can('deleteAny');
    }

    protected function canView(Model $record): bool
    {
        return $this->can('view', $record);
    }

    public static function form(Form $form): Form
    {
        return $form;
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
        return static::$title ?? (string) Str::of(static::getRelationshipName())
            ->kebab()
            ->replace('-', ' ')
            ->title();
    }

    protected function getRelatedModel(): string
    {
        return $this->getRelationship()->getQuery()->getModel()::class;
    }

    protected function getRelationship(): Relation
    {
        return $this->ownerRecord->{static::getRelationshipName()}();
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
