<?php

namespace Filament\Resources\Pages\Concerns;

use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Livewire\Attributes\Locked;
use Livewire\Livewire;

use function Filament\Support\original_request;

trait InteractsWithParentRecord
{
    protected bool $hasResolvedParentRecordForRequest = false;

    #[Locked]
    public ?Model $parentRecord = null;

    public function bootInteractsWithParentRecord(): void
    {
        if (Livewire::isLivewireRequest()) {
            return;
        }

        $this->mountParentRecord();
    }

    public function hydrateInteractsWithParentRecord(): void
    {
        if (! static::getParentResource()) {
            return;
        }

        $this->authorizeParentRecordAccess();
    }

    public function mountParentRecord(): void
    {
        if ($this->hasResolvedParentRecordForRequest) {
            return;
        }

        $parentResourceRegistration = static::getResource()::getParentResourceRegistration();

        if (! $parentResourceRegistration) {
            $this->hasResolvedParentRecordForRequest = true;

            return;
        }

        $this->parentRecord = $this->resolveParentRecord($this->getParentRecordRouteParameters());

        $this->hasResolvedParentRecordForRequest = true;

        $this->authorizeParentRecordAccess();
    }

    /**
     * @return array<string, mixed>
     */
    protected function getParentRecordRouteParameters(): array
    {
        return original_request()->route()?->parameters() ?? [];
    }

    protected function authorizeParentRecordAccess(): void
    {
        $parentResourceRegistration = static::getResource()::getParentResourceRegistration();

        while ($parentResourceRegistration) {
            $parentResource = $parentResourceRegistration->getParentResource();

            abort_unless($parentResource::canAccess(), 403);

            $parentResourceRegistration = $parentResource::getParentResourceRegistration();
        }
    }

    /**
     * @param  array<string, mixed>  $parameters
     */
    protected function resolveParentRecord(array $parameters): Model
    {
        $modifyQuery = fn (Builder $query): Builder => $query->useWritePdo();

        $parentResourceRegistration = static::getResource()::getParentResourceRegistration();
        $parentRecord = null;
        $parentResourceRegistrations = [];
        $hasParentRecordRouteParameters = false;

        while ($parentResourceRegistration) {
            $parentResourceRegistrations[] = $parentResourceRegistration;

            if (array_key_exists($parentResourceRegistration->getParentRouteParameterName(), $parameters)) {
                $hasParentRecordRouteParameters = true;
            }

            $parentResourceRegistration = $parentResourceRegistration->getParentResource()::getParentResourceRegistration();
        }

        if ((! $hasParentRecordRouteParameters) && $this->parentRecord) {
            $parentResource = $parentResourceRegistrations[0]->getParentResource();
            $parentRecordKey = filled($routeKeyName = $parentResource::getRecordRouteKeyName())
                ? $this->parentRecord->getAttribute($routeKeyName)
                : $this->parentRecord->getRouteKey();

            if ((! is_int($parentRecordKey)) && (! is_string($parentRecordKey))) {
                throw (new ModelNotFoundException)->setModel($parentResource::getModel());
            }

            $parentRecord = $parentResource::resolveRecordRouteBinding(
                $parentRecordKey,
                fn (Builder $query): Builder => $query->useWritePdo()->whereKey($this->parentRecord->getKey()),
            );

            if (($parentRecord === null)
                || ($parentRecord::class !== $this->parentRecord::class)
                || ((string) $parentRecord->getKey() !== (string) $this->parentRecord->getKey())) {
                throw (new ModelNotFoundException)->setModel($parentResource::getModel(), [$parentRecordKey]);
            }

            return $parentRecord;
        }

        if (count($parentResourceRegistrations)) {
            $parentResourceRegistrations = array_reverse($parentResourceRegistrations);
            $parentRecord = null;
            $previousParentResourceRegistration = null;

            foreach ($parentResourceRegistrations as $parentResourceRegistration) {
                $previousParentRecord = $parentRecord;

                $parentResource = $parentResourceRegistration->getParentResource();
                $parentRecordKey = $parameters[$parentResourceRegistration->getParentRouteParameterName()] ?? null;

                if ($parentRecordKey instanceof Model) {
                    $parentRecordKey = filled($routeKeyName = $parentResource::getRecordRouteKeyName())
                        ? $parentRecordKey->getAttribute($routeKeyName)
                        : $parentRecordKey->getRouteKey();
                }

                if ((! is_int($parentRecordKey)) && (! is_string($parentRecordKey))) {
                    throw (new ModelNotFoundException)->setModel($parentResource::getModel());
                }

                $parentRecord = $parentResource::resolveRecordRouteBinding(
                    $parentRecordKey,
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

                $modifyQuery = fn (Builder $query): Builder => $parentResourceRegistration->getChildResource()::scopeEloquentQueryToParent($query->useWritePdo(), $parentRecord);

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
