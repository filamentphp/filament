<?php

namespace Filament\Resources\Pages\Concerns;

use Filament\Actions\Action;
use Illuminate\Contracts\Support\Htmlable;
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
        $record = static::getResource()::resolveRecordRouteBinding($key);

        if ($record === null) {
            throw (new ModelNotFoundException)->setModel($this->getModel(), [$key]);
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
            return $resource::getTitleCaseModelLabel();
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

        if (filled($cluster = static::getCluster())) {
            return $cluster::unshiftClusterBreadcrumbs($breadcrumbs);
        }

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

    protected function getMountedActionFormModel(): Model | string | null
    {
        return $this->getRecord();
    }

    protected function configureAction(Action $action): void
    {
        $action
            ->record($this->getRecord())
            ->recordTitle($this->getRecordTitle());
    }
}
