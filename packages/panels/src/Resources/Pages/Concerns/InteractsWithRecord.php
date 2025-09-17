<?php

namespace Filament\Resources\Pages\Concerns;

use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Livewire\Attributes\Locked;

trait InteractsWithRecord
{
    #[Locked]
    public Model | int | string | null $record;

    public function mountCanAuthorizeAccess(): void
    {
        abort_unless(static::canAccess(['record' => $this->getRecord()]), 403);
    }

    protected function resolveRecord(int | string $key): Model
    {
        $this->mountParentRecord();

        $parentRecord = $this->getParentRecord();
        $modifyQuery = null;

        if ($parentRecord) {
            $modifyQuery = fn (Builder $query) => static::getResource()::scopeEloquentQueryToParent($query, $parentRecord);
        }

        $record = static::getResource()::resolveRecordRouteBinding($key, $modifyQuery);

        if ($record === null) {
            throw (new ModelNotFoundException)->setModel($this->getModel(), [$key]);
        }

        if ($parentRecord) {
            $record->setRelation(static::getResource()::getParentResourceRegistration()->getInverseRelationshipName(), $parentRecord);
        }

        return $record;
    }

    public function getRecord(): Model
    {
        return $this->record;
    }

    public function hasRecord(): bool
    {
        return filled($this->record);
    }

    public function getRecordTitle(): string | Htmlable
    {
        $resource = static::getResource();

        if (! $resource::hasRecordTitle()) {
            return $resource::getTitleCaseModelLabel();
        }

        return $resource::getRecordTitle($this->getRecord());
    }

    /**
     * @return array<string>
     */
    public function getBreadcrumbs(): array
    {
        $breadcrumbs = $this->getResourceBreadcrumbs();

        $resource = static::getResource();
        $record = $this->getRecord();

        if ($record->exists && $resource::hasRecordTitle()) {
            if ($resource::hasPage('view') && $resource::canView($record)) {
                $breadcrumbs[
                    $this->getResourceUrl('view')
                ] = $this->getRecordTitle();
            } elseif ($resource::hasPage('edit') && $resource::canEdit($record)) {
                $breadcrumbs[
                    $this->getResourceUrl('edit')
                ] = $this->getRecordTitle();
            } else {
                $breadcrumbs[] = $this->getRecordTitle();
            }
        }

        $breadcrumbs[] = $this->getBreadcrumb();

        return $breadcrumbs;
    }

    protected function afterActionCalled(): void
    {
        if ($this->getRecord()->exists) {
            return;
        }

        // Ensure that Livewire does not attempt to dehydrate
        // a record that does not exist.
        $this->record = null;
    }

    /**
     * @return array<string, mixed>
     */
    public function getSubNavigationParameters(): array
    {
        return [
            'record' => $this->getRecord(),
        ];
    }

    public function getSubNavigation(): array
    {
        return static::getResource()::getRecordSubNavigation($this);
    }

    /**
     * @return array<string, mixed>
     */
    public function getWidgetData(): array
    {
        return [
            'record' => $this->getRecord(),
        ];
    }

    /**
     * @return Model|class-string<Model>|null
     */
    protected function getMountedActionSchemaModel(): Model | string | null
    {
        return $this->getRecord();
    }

    public function getDefaultActionRecord(Action $action): ?Model
    {
        return $this->hasRecord() ? $this->getRecord() : null;
    }

    public function getDefaultActionRecordTitle(Action $action): ?string
    {
        return $this->getRecordTitle();
    }

    public function getDefaultActionSuccessRedirectUrl(Action $action): ?string
    {
        return match (true) {
            $action instanceof DeleteAction, $action instanceof ForceDeleteAction => $this->getResourceUrl(),
            default => null,
        };
    }
}
