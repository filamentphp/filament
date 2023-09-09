<?php

namespace Filament\Resources\Pages\Concerns;

use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Str;
use Livewire\Attributes\Locked;

trait InteractsWithRecord
{
    #[Locked]
    public Model | int | string | null $record;

    protected function resolveRecord(int | string $key): Model
    {
        $record = static::getResource()::resolveRecordRouteBinding($key);

        if ($record === null) {
            throw (new ModelNotFoundException())->setModel($this->getModel(), [$key]);
        }

        return $record;
    }

    public function getRecord(): Model
    {
        return $this->record;
    }

    public function getRecordTitle(): string | Htmlable
    {
        $resource = static::getResource();

        if (! $resource::hasRecordTitle()) {
            return Str::headline($resource::getModelLabel());
        }

        return $resource::getRecordTitle($this->getRecord());
    }

    /**
     * @return array<string>
     */
    public function getBreadcrumbs(): array
    {
        $resource = static::getResource();

        $breadcrumbs = [
            $resource::getUrl() => $resource::getBreadcrumb(),
        ];

        $record = $this->getRecord();

        if ($record->exists && $resource::hasRecordTitle()) {
            if ($resource::hasPage('view') && $resource::canView($record)) {
                $breadcrumbs[
                    $resource::getUrl('view', ['record' => $record])
                ] = $this->getRecordTitle();
            } elseif ($resource::hasPage('edit') && $resource::canEdit($record)) {
                $breadcrumbs[
                    $resource::getUrl('edit', ['record' => $record])
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
}
