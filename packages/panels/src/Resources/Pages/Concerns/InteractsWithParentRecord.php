<?php

namespace Filament\Resources\Pages\Concerns;

use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Livewire\Attributes\Locked;

trait InteractsWithParentRecord
{
    #[Locked]
    public ?Model $parentRecord = null;

    public function mountInteractsWithParentRecord(): void
    {
        $parentResourceRegistration = static::getResource()::getParentResourceRegistration();

        if (! $parentResourceRegistration) {
            return;
        }

        $this->parentRecord = $this->resolveParentRecord(
            request()->route()->parameter(
                $parentResourceRegistration->getParentRouteParameterName(),
            ),
        );

        $this->authorizeParentRecordAccess();
    }

    protected function authorizeParentRecordAccess(): void
    {
        abort_unless(static::getParentResource()::canView($this->getParentRecord()), 403);
    }

    protected function resolveParentRecord(int | string $key): Model
    {
        $record = static::getParentResource()::resolveRecordRouteBinding($key);

        if ($record === null) {
            throw (new ModelNotFoundException())->setModel($this->getModel(), [$key]);
        }

        return $record;
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
