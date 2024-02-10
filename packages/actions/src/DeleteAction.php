<?php

namespace Filament\Actions;

use Closure;
use Illuminate\Database\Eloquent\Model;
use Filament\Notifications\Notification;
use Filament\Support\Facades\FilamentIcon;
use Filament\Actions\Concerns\CanCustomizeProcess;

class DeleteAction extends Action
{
    use CanCustomizeProcess;

    protected bool | Closure $deletable = true;

    protected string | Closure $notDeletableNotificationTitle;

    protected string | Closure $notDeletableNotificationBody;

    public static function getDefaultName(): ?string
    {
        return 'delete';
    }

    public function deletable(bool | Closure $condition = true): static
    {
        $this->deletable = $condition;

        return $this;
    }

    public function isDeletable(): bool
    {
        return (bool) $this->evaluate($this->deletable);
    }

    public function notDeletableNotificationTitle(string | Closure $notDeletableNotificationTitle): static
    {
        $this->notDeletableNotificationTitle = $notDeletableNotificationTitle;

        return $this;
    }

    public function getNotDeletableNotificationTitle(): string
    {
        return $this->evaluate($this->notDeletableNotificationTitle);
    }

    public function notDeletableNotificationBody(string | Closure $notDeletableNotificationBody): static
    {
        $this->notDeletableNotificationBody = $notDeletableNotificationBody;

        return $this;
    }

    public function getNotDeletableNotificationBody(): string
    {
        return $this->evaluate($this->notDeletableNotificationBody);
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->label(__('filament-actions::delete.single.label'));

        $this->modalHeading(fn (): string => __('filament-actions::delete.single.modal.heading', ['label' => $this->getRecordTitle()]));

        $this->modalSubmitActionLabel(__('filament-actions::delete.single.modal.actions.delete.label'));

        $this->successNotificationTitle(__('filament-actions::delete.single.notifications.deleted.title'));

        $this->color('danger');

        $this->groupedIcon(FilamentIcon::resolve('actions::delete-action.grouped') ?? 'heroicon-m-trash');

        $this->requiresConfirmation();

        $this->modalIcon(FilamentIcon::resolve('actions::delete-action.modal') ?? 'heroicon-o-trash');

        $this->keyBindings(['mod+d']);

        $this->hidden(static function (Model $record): bool {
            if (! method_exists($record, 'trashed')) {
                return false;
            }

            return $record->trashed();
        });

        $this->action(function (): void {
            if (! $this->isDeletable()) {
                Notification::make()
                    ->title($this->getNotDeletableNotificationTitle())
                    ->body($this->getNotDeletableNotificationBody())
                    ->danger()
                    ->send();

                return;
            }

            $result = $this->process(static fn (Model $record) => $record->delete());

            if (! $result) {
                $this->failure();

                return;
            }

            $this->success();
        });
    }
}
