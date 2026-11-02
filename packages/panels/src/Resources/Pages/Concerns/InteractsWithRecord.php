<?php

namespace Filament\Resources\Pages\Concerns;

use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Support\Components\Attributes\Unexposed;
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

    #[Unexposed]
    public function mountCanAuthorizeAccess(): void
    {
        abort_unless(static::canAccess(['record' => $this->getRecord()]), 403);
    }

    #[Unexposed]
    public function hydrateCanAuthorizeAccess(): void
    {
        abort_unless(static::canAccess(['record' => $this->getRecord()]), 403);
    }

    #[Unexposed]
    public function getRecord(): Model
    {
        abort_unless($this->record instanceof Model, 404);

        return $this->record;
    }

    #[Unexposed]
    public function hasRecord(): bool
    {
        return filled($this->record);
    }

    #[Unexposed]
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
    #[Unexposed]
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
    #[Unexposed]
    public function getSubNavigationParameters(): array
    {
        return [
            'record' => $this->getRecord(),
        ];
    }

    #[Unexposed]
    public function getSubNavigation(): array
    {
        return static::getResource()::getRecordSubNavigation($this);
    }

    /**
     * @return array<string, mixed>
     */
    #[Unexposed]
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

    #[Unexposed]
    public function getDefaultActionRecord(Action $action): ?Model
    {
        return $this->hasRecord() ? $this->getRecord() : null;
    }

    #[Unexposed]
    public function getDefaultActionRecordTitle(Action $action): ?string
    {
        return $this->getRecordTitle();
    }

    #[Unexposed]
    public function getDefaultActionSuccessRedirectUrl(Action $action): ?string
    {
        return match (true) {
            $action instanceof DeleteAction, $action instanceof ForceDeleteAction => $this->getResourceUrl(),
            default => null,
        };
    }
}
