<?php

namespace Filament\Actions\Exports\Jobs;

use Filament\Actions\Exports\Enums\ExportFormat;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;
use Filament\Notifications\Actions\Action as NotificationAction;
use Filament\Notifications\Notification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ExportCompletion implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public bool $deleteWhenMissingModels = true;

    protected Exporter $exporter;

    /**
     * @param  array<string, string>  $columnMap
     * @param  array<ExportFormat>  $formats
     * @param  array<string, mixed>  $options
     */
    public function __construct(
        protected Export $export,
        protected array $columnMap,
        protected array $formats = [],
        protected array $options = [],
    ) {
        $this->exporter = $this->export->getExporter(
            $this->columnMap,
            $this->options,
        );
    }

    public function handle(): void
    {
        $this->export->touch('completed_at');

        if (! $this->export->user instanceof Authenticatable) {
            return;
        }

        $failedRowsCount = $this->export->getFailedRowsCount();

        Notification::make()
            ->title(__('filament-actions::export.notifications.completed.title'))
            ->body($this->exporter::getCompletedNotificationBody($this->export))
            ->when(
                ! $failedRowsCount,
                fn (Notification $notification) => $notification->success(),
            )
            ->when(
                $failedRowsCount && ($failedRowsCount < $this->export->total_rows),
                fn (Notification $notification) => $notification->warning(),
            )
            ->when(
                $failedRowsCount === $this->export->total_rows,
                fn (Notification $notification) => $notification->danger(),
            )
            ->when(
                $failedRowsCount < $this->export->total_rows,
                fn (Notification $notification) => $notification->actions(array_map(
                    fn (ExportFormat $format): NotificationAction => $format->getDownloadNotificationAction($this->export),
                    $this->formats,
                )),
            )
            ->sendToDatabase($this->export->user);
    }
}
