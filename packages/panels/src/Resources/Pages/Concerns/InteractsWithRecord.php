<?php

namespace Filament\Resources\Pages\Concerns;

use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Support\Attributes\ServerOnly;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Model;
use Livewire\Attributes\Locked;

trait InteractsWithRecord
{
    protected bool $hasResolvedRecordForRequest = false;

    #[Locked]
    public Model | int | string | null $record;

    protected function resolveRecordPropertyFromLivewire(): void
    {
        if ($this->hasResolvedRecordForRequest || (! ($this->record instanceof Model))) {
            return;
        }

        parent::resolveRecordPropertyFromLivewire();

        $this->hasResolvedRecordForRequest = true;
    }

    #[ServerOnly]
    public function mountCanAuthorizeAccess(): void
    {
        abort_unless(static::canAccess(['record' => $this->getRecord()]), 403);
    }

    #[ServerOnly]
    public function hydrateCanAuthorizeAccess(): void
    {
        abort_unless(static::canAccess(['record' => $this->getRecord()]), 403);
    }

    #[ServerOnly]
    public function getRecord(): Model
    {
        abort_unless($this->record instanceof Model, 404);

        return $this->record;
    }

    #[ServerOnly]
    public function hasRecord(): bool
    {
        return filled($this->record);
    }

    #[ServerOnly]
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
    #[ServerOnly]
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

    protected function afterActionCalled(Action $action): void
    {
        parent::afterActionCalled($action);

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
    #[ServerOnly]
    public function getSubNavigationParameters(): array
    {
        return [
            'record' => $this->getRecord(),
        ];
    }

    #[ServerOnly]
    public function getSubNavigation(): array
    {
        return static::getResource()::getRecordSubNavigation($this);
    }

    /**
     * @return array<string, mixed>
     */
    #[ServerOnly]
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

    #[ServerOnly]
    public function getDefaultActionRecord(Action $action): ?Model
    {
        return $this->hasRecord() ? $this->getRecord() : null;
    }

    #[ServerOnly]
    public function getDefaultActionRecordTitle(Action $action): ?string
    {
        return $this->getRecordTitle();
    }

    #[ServerOnly]
    public function getDefaultActionSuccessRedirectUrl(Action $action): ?string
    {
        return match (true) {
            $action instanceof DeleteAction, $action instanceof ForceDeleteAction => $this->getResourceUrl(),
            default => null,
        };
    }
}
