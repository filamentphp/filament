<?php

namespace Filament\Actions\Imports;

use Filament\Actions\Action;
use Filament\Actions\Imports\Events\ImportCompleted;
use Filament\Actions\Imports\Events\ImportStarted;
use Filament\Actions\Imports\Models\Import;
use Filament\Notifications\Notification;
use Illuminate\Bus\PendingBatch;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Number;

class ImportDispatcher
{
    /**
     * @param  iterable<array<array<string, string>>>  $importChunks
     * @param  array<string, string>  $columnMap
     * @param  array<string, mixed>  $options
     */
    public function dispatch(Import $import, iterable $importChunks, array $columnMap, array $options, string $job, ?string $authGuard): ?string
    {
        // Do not send the loaded user relationship in job payloads, since it may
        // contain attributes that are not serializable, such as binary columns.
        $import->unsetRelation('user');

        $importJobs = collect($importChunks)
            ->map(fn (array $importChunk): object => app($job, [
                'import' => $import,
                'rows' => base64_encode(serialize($importChunk)),
                'columnMap' => $columnMap,
                'options' => $options,
            ]));

        $importer = $import->getImporter(
            columnMap: $columnMap,
            options: $options,
        );

        event(new ImportStarted($import, $columnMap, $options));

        Bus::batch($importJobs->all())
            ->allowFailures()
            ->when(
                filled($jobQueue = $importer->getJobQueue()),
                fn (PendingBatch $batch) => $batch->onQueue($jobQueue),
            )
            ->when(
                filled($jobConnection = $importer->getJobConnection()),
                fn (PendingBatch $batch) => $batch->onConnection($jobConnection),
            )
            ->when(
                filled($jobBatchName = $importer->getJobBatchName()),
                fn (PendingBatch $batch) => $batch->name($jobBatchName),
            )
            ->finally(function () use ($authGuard, $columnMap, $import, $jobConnection, $options): void {
                $import->touch('completed_at');

                event(new ImportCompleted($import, $columnMap, $options));

                if (! $import->user instanceof Authenticatable) { /** @phpstan-ignore instanceof.alwaysTrue */
                    return;
                }

                $import->columnMap($columnMap);
                $import->options($options);

                $failedRowsCount = $import->getFailedRowsCount();

                $isSynchronous = ($jobConnection === 'sync') || (blank($jobConnection) && (config('queue.default') === 'sync'));

                $notification = Notification::make()
                    ->title($import->importer::getCompletedNotificationTitle($import))
                    ->body($import->importer::getCompletedNotificationBody($import))
                    ->when(
                        ! $failedRowsCount,
                        fn (Notification $notification) => $notification->success(),
                    )
                    ->when(
                        $failedRowsCount && ($failedRowsCount < $import->total_rows),
                        fn (Notification $notification) => $notification->warning(),
                    )
                    ->when(
                        $failedRowsCount === $import->total_rows,
                        fn (Notification $notification) => $notification->danger(),
                    )
                    ->when(
                        $failedRowsCount,
                        fn (Notification $notification) => $notification->actions([
                            Action::make('downloadFailedRowsCsv')
                                ->label(trans_choice('filament-actions::import.notifications.completed.actions.download_failed_rows_csv.label', $failedRowsCount, [
                                    'count' => Number::format($failedRowsCount),
                                ]))
                                ->color('danger')
                                ->url(URL::signedRoute('filament.imports.failed-rows.download', ['authGuard' => $authGuard, 'import' => $import], absolute: false), shouldOpenInNewTab: true)
                                ->markAsRead(),
                        ]),
                    )
                    ->when(
                        $isSynchronous,
                        fn (Notification $notification) => $notification->persistent(),
                    );

                $notification = $import->importer::modifyCompletedNotification($notification, $import);

                if ($isSynchronous) {
                    $notification->send();
                } else {
                    $notification->sendToDatabase($import->user, isEventDispatched: true);
                }
            })
            ->dispatch();

        return $jobConnection;
    }
}
