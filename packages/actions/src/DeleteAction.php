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

    protected bool | Closure $allowedToDelete = true;

    protected string | Closure $deleteNotAllowedNotificationTitle;

    protected string | Closure $deleteNotAllowedNotificationBody;

    public static function getDefaultName(): ?string
    {
        return 'delete';
    }

    public function allowedToDelete(bool | Closure $condition = true): static
    {
        $this->allowedToDelete = $condition;

        return $this;
    }

    public function isAllowedToDelete(): bool
    {
        return (bool) $this->evaluate($this->allowedToDelete);
    }

    public function deleteNotAllowedNotificationTitle(string | Closure $deleteNotAllowedNotificationTitle): static
    {
        $this->deleteNotAllowedNotificationTitle = $deleteNotAllowedNotificationTitle;

        return $this;
    }

    public function getDeletionNotAllowedNotificationTitle(): string
    {
        return $this->evaluate($this->deleteNotAllowedNotificationTitle);
    }

    public function deleteNotAllowedNotificationBody(string | Closure $deleteNotAllowedNotificationBody): static
    {
        $this->deleteNotAllowedNotificationBody = $deleteNotAllowedNotificationBody;

        return $this;
    }

    public function getDeletionNotAllowedNotificationBody(): string
    {
        return $this->evaluate($this->deleteNotAllowedNotificationBody);
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
            if (! $this->isAllowedToDelete()) {
                Notification::make()
                    ->title($this->getDeletionNotAllowedNotificationTitle())
                    ->body($this->getDeletionNotAllowedNotificationBody())
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
