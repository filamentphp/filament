<?php

namespace Filament\Actions\Concerns;

use Filament\Actions\Action;
use Filament\Facades\Filament;
use Filament\Support\Components\Attributes\Exposed;
use Filament\Support\Components\Attributes\Unexposed;
use Filament\Support\Events\ModelChangedBroadcast;
use Filament\Support\Facades\FilamentRealtime;
use Filament\Support\Livewire\Partials\PartialsComponentHook;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Broadcast;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Livewire\Attributes\Locked;
use Livewire\Attributes\Renderless;

trait InteractsWithActionRecordUpdates
{
    /**
     * @var array<int, array{key: string, signature: string, channel: string}>
     */
    #[Locked]
    public array $mountedActionRecords = [];

    protected function rememberMountedActionRecord(Action $action): void
    {
        $actionNestingIndex = $action->getNestingIndex();
        unset($this->mountedActionRecords[$actionNestingIndex]);

        if ((! $action->shouldReceiveRecordUpdates()) || (! ($record = $action->getRecord()) instanceof Model) || (! $record->exists)) {
            return;
        }

        $this->mountedActionRecords[$actionNestingIndex] = [
            'key' => (string) Str::uuid(),
            'signature' => $this->getActionRecordSignature($action),
            'channel' => ModelChangedBroadcast::getChannel($record),
        ];
    }

    protected function getActionRecordSignature(Action $action): string
    {
        return hash('sha256', json_encode([
            array_map(
                static fn (array $mountedAction): array => Arr::only($mountedAction, ['name', 'arguments', 'context']),
                array_slice($this->mountedActions, 0, $action->getNestingIndex() + 1),
            ),
            ModelChangedBroadcast::getChannel($action->getRecord()),
        ], JSON_THROW_ON_ERROR));
    }

    /**
     * @return array{key: string, signature: string, channel: string} | null
     */
    #[Unexposed]
    public function getActionRecordUpdates(Action $action): ?array
    {
        if (
            (! $action->shouldReceiveRecordUpdates())
            || (! ($record = $action->getRecord()) instanceof Model)
            || (! $record->exists)
            || blank(config('filament.broadcasting.echo'))
            || (class_exists(Filament::class) && (Filament::getCurrentPanel()?->hasBroadcasting() === false))
            || blank(FilamentRealtime::getSubscription($record::class)?->getEvents())
        ) {
            return null;
        }

        $subscription = $this->mountedActionRecords[$action->getNestingIndex()] ?? null;

        if ((! $subscription) || ($subscription['signature'] !== $this->getActionRecordSignature($action))) {
            return null;
        }

        return $subscription;
    }

    protected function resolveActionRecordUpdates(string $key): ?Action
    {
        $actionNestingIndex = array_search($key, array_map(static fn (array $subscription): string => $subscription['key'], $this->mountedActionRecords), true);

        if ($actionNestingIndex === false) {
            return null;
        }

        $action = $this->getMountedAction($actionNestingIndex);

        abort_unless($action instanceof Action, 404);

        foreach (array_slice($this->getMountedActions(), 0, $actionNestingIndex + 1) as $parentAction) {
            $parentAction->clearVisibilityCache();
            abort_unless($parentAction->isAuthorized() && (! $parentAction->isDisabled()), 403);
        }

        if (($this->getActionRecordUpdates($action)['key'] ?? null) !== $key) {
            return null;
        }

        return $action;
    }

    /**
     * @return array<string, mixed> | null
     */
    #[Exposed]
    #[Renderless]
    public function authorizeActionRecordUpdates(string $key, string $socketId): ?array
    {
        if (! $action = $this->resolveActionRecordUpdates($key)) {
            return null;
        }

        Validator::make(['socketId' => $socketId], ['socketId' => ['required', 'regex:/\A\d+\.\d+\z/']])->validate();

        $request = clone request();
        $request->replace([
            'channel_name' => 'private-' . ModelChangedBroadcast::getChannel($action->getRecord()),
            'socket_id' => $socketId,
        ]);

        return Broadcast::connection()->validAuthenticationResponse($request, true);
    }

    #[Exposed]
    public function refreshAction(string $key): bool
    {
        if (! $action = $this->resolveActionRecordUpdates($key)) {
            return false;
        }

        $schema = $this->getMountedActionSchema(mountedAction: $action);

        app(PartialsComponentHook::class)->renderPartial($this, fn (): array => [
            "action-modals.{$action->getNestingIndex()}" => $action->renderModal(),
        ]);

        $action->callRecordUpdateReceived(['form' => $schema, 'schema' => $schema]);

        return true;
    }
}
