<?php

namespace Filament\Resources\Pages\Concerns;

use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Livewire\Attributes\Locked;
use Livewire\Livewire;

trait InteractsWithParentRecord
{
    #[Locked]
    public ?Model $parentRecord = null;

    public function bootInteractsWithParentRecord(): void
    {
        if (Livewire::isLivewireRequest()) {
            return;
        }

        $this->mountParentRecord();
    }

    public function mountParentRecord(): void
    {
        if ($this->parentRecord) {
            return;
        }

        $parentResourceRegistration = static::getResource()::getParentResourceRegistration();

        if (! $parentResourceRegistration) {
            return;
        }

        $this->parentRecord = $this->resolveParentRecord(request()->route()->parameters());

        $this->authorizeParentRecordAccess();
    }

    protected function authorizeParentRecordAccess(): void
    {
        abort_unless(static::getParentResource()::canView($this->getParentRecord()) || static::getParentResource()::canEdit($this->getParentRecord()), 403);
    }

    /**
     * @param  array<string, mixed>  $parameters
     */
    protected function resolveParentRecord(array $parameters): Model
    {
        $modifyQuery = null;

        $parentResourceRegistration = static::getResource()::getParentResourceRegistration();
        $parentRecord = null;
        $parentResourceRegistrations = [];

        while ($parentResourceRegistration) {
            $parentResourceRegistrations[] = $parentResourceRegistration;

            $parentResourceRegistration = $parentResourceRegistration->getParentResource()::getParentResourceRegistration();
        }

        if (count($parentResourceRegistrations)) {
            $parentResourceRegistrations = array_reverse($parentResourceRegistrations);
            $parentRecord = null;
            $previousParentResourceRegistration = null;

            foreach ($parentResourceRegistrations as $parentResourceRegistration) {
                $previousParentRecord = $parentRecord;

                $parentResource = $parentResourceRegistration->getParentResource();
                $parentRecord = $parentResource::resolveRecordRouteBinding(
                    $parentRecordKey = $parameters[
                        $parentResourceRegistration->getParentRouteParameterName()
                    ] ?? null,
                    $modifyQuery,
                );

                if ($parentRecord === null) {
                    throw (new ModelNotFoundException)->setModel($parentResource::getModel(), [$parentRecordKey]);
                }

                if ($previousParentRecord) {
                    $parentRecord->setRelation(
                        $previousParentResourceRegistration->getInverseRelationshipName(),
                        $previousParentRecord,
                    );
                }

                $modifyQuery = fn (Builder $query) => $parentResourceRegistration->getChildResource()::scopeEloquentQueryToParent($query, $parentRecord);

                $previousParentResourceRegistration = $parentResourceRegistration;
            }
        }

        return $parentRecord;
    }

    public function getParentRecord(): ?Model
    {
        return $this->parentRecord;
    }

    public function getParentRecordTitle(): string | Htmlable | null
    {
        $resource = static::getParentResource();

        if (! $resource::hasRecordTitle()) {
            return $resource::getTitleCaseModelLabel();
        }

        return $resource::getRecordTitle($this->getParentRecord());
    }

    public static function getParentResource(): ?string
    {
        return static::getResource()::getParentResourceRegistration()?->getParentResource();
    }
}
