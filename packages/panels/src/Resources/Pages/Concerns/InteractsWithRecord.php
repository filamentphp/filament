<?php

namespace Filament\Resources\Pages\Concerns;

use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Facades\Filament;
use Filament\Support\Components\Attributes\Exposed;
use Filament\Support\Components\Attributes\Unexposed;
use Filament\Support\Events\RecordChangedBroadcast;
use Filament\Support\Facades\FilamentRealtime;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Broadcast;
use Illuminate\Support\Facades\Validator;
use Livewire\Attributes\Locked;
use Livewire\Attributes\Renderless;

use function Livewire\store;

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

    #[Unexposed]
    public function hasLiveRecordUpdates(): bool
    {
        return Filament::getCurrentOrDefaultPanel()->hasBroadcasting()
            && filled(config('filament.broadcasting.echo'))
            && $this->hasRecord()
            && filled(FilamentRealtime::getSubscription($this->getRecord()::class)?->getEvents());
    }

    #[Unexposed]
    public function getRecordBroadcastChannel(): string
    {
        return RecordChangedBroadcast::getChannel($this->getRecord());
    }

    /**
     * @return array<string, mixed> | null
     */
    #[Exposed]
    #[Renderless]
    public function authorizeRecordUpdates(string $socketId): ?array
    {
        if (! $this->hasRecord()) {
            return null;
        }

        abort_unless($this->hasLiveRecordUpdates(), 403);

        Validator::make(['socketId' => $socketId], ['socketId' => ['required', 'regex:/\A\d+\.\d+\z/']])->validate();

        $request = clone request();
        $request->replace([
            'channel_name' => 'private-' . $this->getRecordBroadcastChannel(),
            'socket_id' => $socketId,
        ]);

        return Broadcast::connection()->validAuthenticationResponse($request, true);
    }

    #[Exposed]
    public function refreshRecordPage(): ?string
    {
        if (! $this->hasRecord()) {
            return null;
        }

        abort_unless($this->hasLiveRecordUpdates(), 403);

        $this->forceRender();

        return $this->getBrowserTitle();
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

        // A refresh batched before this action must not force a render
        // after the action has removed the record and is redirecting.
        store($this)->unset('forceRender');

        if (store($this)->has('redirect')) {
            $this->skipRender();
        }
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
