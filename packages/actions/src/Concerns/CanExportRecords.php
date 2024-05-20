<?php

namespace Filament\Actions\Concerns;

use AnourValar\EloquentSerialize\Facades\EloquentSerializeFacade;
use Closure;
use Filament\Actions\ExportAction;
use Filament\Actions\Exports\Enums\ExportFormat;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Jobs\CreateXlsxFile;
use Filament\Actions\Exports\Jobs\ExportCompletion;
use Filament\Actions\Exports\Jobs\PrepareCsvExport;
use Filament\Actions\Exports\Models\Export;
use Filament\Forms;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\Split;
use Filament\Notifications\Notification;
use Filament\Support\Facades\FilamentIcon;
use Filament\Tables\Actions\ExportAction as ExportTableAction;
use Filament\Tables\Actions\ExportBulkAction as ExportTableBulkAction;
use Filament\Tables\Contracts\HasTable;
use Illuminate\Bus\PendingBatch;
use Illuminate\Foundation\Bus\PendingChain;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Number;
use Illuminate\Support\Str;
use Livewire\Component;

trait CanExportRecords
{
    /**
     * @var class-string<Exporter>
     */
    protected string $exporter;

    protected ?string $job = null;

    protected int | Closure $chunkSize = 100;

    protected int | Closure | null $maxRows = null;

    protected string | Closure | null $csvDelimiter = null;

    /**
     * @var array<string, mixed> | Closure
     */
    protected array | Closure $options = [];

    protected string | Closure | null $fileDisk = null;

    protected string | Closure | null $fileName = null;

    /**
     * @var array<ExportFormat> | Closure | null
     */
    protected array | Closure | null $formats = null;

    protected ?Closure $modifyQueryUsing = null;

    protected bool | Closure $hasColumnMapping = true;

    protected function setUp(): void
    {
        parent::setUp();

        $this->label(fn (ExportAction | ExportTableAction | ExportTableBulkAction $action): string => __('filament-actions::export.label', ['label' => $action->getPluralModelLabel()]));

        $this->modalHeading(fn (ExportAction | ExportTableAction | ExportTableBulkAction $action): string => __('filament-actions::export.modal.heading', ['label' => $action->getPluralModelLabel()]));

        $this->modalSubmitActionLabel(__('filament-actions::export.modal.actions.export.label'));

        $this->groupedIcon(FilamentIcon::resolve('actions::export-action.grouped') ?? 'heroicon-m-arrow-down-tray');

        $this->form(fn (ExportAction | ExportTableAction | ExportTableBulkAction $action): array => [
            ...($action->hasColumnMapping() ? [Fieldset::make(__('filament-actions::export.modal.form.columns.label'))
                ->columns(1)
                ->inlineLabel()
                ->schema(function () use ($action): array {
                    return array_map(
                        fn (ExportColumn $column): Split => Split::make([
                            Forms\Components\Checkbox::make('isEnabled')
                                ->label(__('filament-actions::export.modal.form.columns.form.is_enabled.label', ['column' => $column->getName()]))
                                ->hiddenLabel()
                                ->default($column->isEnabledByDefault())
                                ->live()
                                ->grow(false),
                            Forms\Components\TextInput::make('label')
                                ->label(__('filament-actions::export.modal.form.columns.form.label.label', ['column' => $column->getName()]))
                                ->hiddenLabel()
                                ->default($column->getLabel())
                                ->placeholder($column->getLabel())
                                ->disabled(fn (Forms\Get $get): bool => ! $get('isEnabled'))
                                ->required(fn (Forms\Get $get): bool => (bool) $get('isEnabled')),
                        ])
                            ->verticallyAlignCenter()
                            ->statePath($column->getName()),
                        $action->getExporter()::getColumns(),
                    );
                })
                ->statePath('columnMap')] : []),
            ...$action->getExporter()::getOptionsFormComponents(),
        ]);

        $this->action(function (ExportAction | ExportTableAction | ExportTableBulkAction $action, array $data, Component $livewire) {
            $exporter = $action->getExporter();

            if ($livewire instanceof HasTable) {
                $query = $livewire->getTableQueryForExport();
            } else {
                $query = $exporter::getModel()::query();
            }

            $query = $exporter::modifyQuery($query);

            if ($this->modifyQueryUsing) {
                $query = $this->evaluate($this->modifyQueryUsing, [
                    'query' => $query,
                ]) ?? $query;
            }

            $records = $action instanceof ExportTableBulkAction ? $action->getRecords() : null;

            $totalRows = $records ? $records->count() : $query->count();
            $maxRows = $action->getMaxRows() ?? $totalRows;

            if ($maxRows < $totalRows) {
                Notification::make()
                    ->title(__('filament-actions::export.notifications.max_rows.title'))
                    ->body(trans_choice('filament-actions::export.notifications.max_rows.body', $maxRows, [
                        'count' => Number::format($maxRows),
                    ]))
                    ->danger()
                    ->send();

                return;
            }

            $user = auth()->user();

            $options = array_merge(
                $action->getOptions(),
                Arr::except($data, ['columnMap']),
            );

            if ($action->hasColumnMapping()) {
                $columnMap = collect($data['columnMap'])
                    ->dot()
                    ->reduce(fn (Collection $carry, mixed $value, string $key): Collection => $carry->mergeRecursive([
                        Str::beforeLast($key, '.') => [Str::afterLast($key, '.') => $value],
                    ]), collect())
                    ->filter(fn (array $column): bool => $column['isEnabled'] ?? false)
                    ->mapWithKeys(fn (array $column, string $columnName): array => [$columnName => $column['label']])
                    ->all();
            } else {
                $columnMap = collect($exporter::getColumns())
                    ->mapWithKeys(fn (ExportColumn $column): array => [$column->getName() => $column->getLabel()])
                    ->all();
            }

            $export = app(Export::class);
            $export->user()->associate($user);
            $export->exporter = $exporter;
            $export->total_rows = $totalRows;

            $exporter = $export->getExporter(
                columnMap: $columnMap,
                options: $options,
            );

            $export->file_disk = $action->getFileDisk() ?? $exporter->getFileDisk();
            $export->save();

            $export->file_name = $action->getFileName($export) ?? $exporter->getFileName($export);
            $export->save();

            $formats = $action->getFormats() ?? $exporter->getFormats();
            $hasCsv = in_array(ExportFormat::Csv, $formats);
            $hasXlsx = in_array(ExportFormat::Xlsx, $formats);

            $serializedQuery = EloquentSerializeFacade::serialize($query);

            $job = $action->getJob();
            $jobQueue = $exporter->getJobQueue();
            $jobConnection = $exporter->getJobConnection();
            $jobBatchName = $exporter->getJobBatchName();

            // We do not want to send the loaded user relationship to the queue in job payloads,
            // in case it contains attributes that are not serializable, such as binary columns.
            $export->unsetRelation('user');

            $makeCreateXlsxFileJob = fn (): CreateXlsxFile => app(CreateXlsxFile::class, [
                'export' => $export,
                'columnMap' => $columnMap,
                'options' => $options,
            ]);

            Bus::chain([
                Bus::batch([new $job(
                    $export,
                    query: $serializedQuery,
                    columnMap: $columnMap,
                    options: $options,
                    chunkSize: $action->getChunkSize(),
                    records: $action instanceof ExportTableBulkAction ? $action->getRecords()->all() : null,
                )])
                    ->when(
                        filled($jobQueue),
                        fn (PendingBatch $batch) => $batch->onQueue($jobQueue),
                    )
                    ->when(
                        filled($jobConnection),
                        fn (PendingBatch $batch) => $batch->onConnection($jobConnection),
                    )
                    ->when(
                        filled($jobBatchName),
                        fn (PendingBatch $batch) => $batch->name($jobBatchName),
                    )
                    ->allowFailures(),
                ...(($hasXlsx && (! $hasCsv)) ? [$makeCreateXlsxFileJob()] : []),
                app(ExportCompletion::class, [
                    'export' => $export,
                    'columnMap' => $columnMap,
                    'formats' => $formats,
                    'options' => $options,
                ]),
                ...(($hasXlsx && $hasCsv) ? [$makeCreateXlsxFileJob()] : []),
            ])
                ->when(
                    filled($jobQueue),
                    fn (PendingChain $chain) => $chain->onQueue($jobQueue),
                )
                ->when(
                    filled($jobConnection),
                    fn (PendingChain $chain) => $chain->onConnection($jobConnection),
                )
                ->dispatch();

            Notification::make()
                ->title($action->getSuccessNotificationTitle())
                ->body(trans_choice('filament-actions::export.notifications.started.body', $export->total_rows, [
                    'count' => Number::format($export->total_rows),
                ]))
                ->success()
                ->send();
        });

        $this->color('gray');

        $this->modalWidth('xl');

        $this->successNotificationTitle(__('filament-actions::export.notifications.started.title'));

        if (! $this instanceof ExportTableBulkAction) {
            $this->model(fn (ExportAction | ExportTableAction $action): string => $action->getExporter()::getModel());
        }
    }

    public static function getDefaultName(): ?string
    {
        return 'export';
    }

    /**
     * @param  class-string<Exporter>  $exporter
     */
    public function exporter(string $exporter): static
    {
        $this->exporter = $exporter;

        return $this;
    }

    /**
     * @param  class-string | null  $job
     */
    public function job(?string $job): static
    {
        $this->job = $job;

        return $this;
    }

    public function chunkSize(int | Closure $size): static
    {
        $this->chunkSize = $size;

        return $this;
    }

    public function maxRows(int | Closure | null $rows): static
    {
        $this->maxRows = $rows;

        return $this;
    }

    public function csvDelimiter(string | Closure | null $delimiter): static
    {
        $this->csvDelimiter = $delimiter;

        return $this;
    }

    /**
     * @return class-string<Exporter>
     */
    public function getExporter(): string
    {
        return $this->exporter;
    }

    /**
     * @return class-string
     */
    public function getJob(): string
    {
        return $this->job ?? PrepareCsvExport::class;
    }

    public function getChunkSize(): int
    {
        return $this->evaluate($this->chunkSize);
    }

    public function getMaxRows(): ?int
    {
        return $this->evaluate($this->maxRows);
    }

    /**
     * @param  array<string, mixed> | Closure  $options
     */
    public function options(array | Closure $options): static
    {
        $this->options = $options;

        return $this;
    }

    /**
     * @return array<string, mixed>
     */
    public function getOptions(): array
    {
        return $this->evaluate($this->options);
    }

    public function fileDisk(string | Closure | null $disk): static
    {
        $this->fileDisk = $disk;

        return $this;
    }

    public function getFileDisk(): ?string
    {
        return $this->evaluate($this->fileDisk);
    }

    public function fileName(string | Closure | null $name): static
    {
        $this->fileName = $name;

        return $this;
    }

    public function getFileName(Export $export): ?string
    {
        return $this->evaluate($this->fileName, [
            'export' => $export,
        ]);
    }

    /**
     * @param  array<ExportFormat> | Closure | null  $formats
     */
    public function formats(array | Closure | null $formats): static
    {
        $this->formats = $formats;

        return $this;
    }

    /**
     * @return array<ExportFormat> | null
     */
    public function getFormats(): ?array
    {
        return $this->evaluate($this->formats);
    }

    public function modifyQueryUsing(?Closure $callback): static
    {
        $this->modifyQueryUsing = $callback;

        return $this;
    }

    public function columnMapping(bool | Closure $condition = true): static
    {
        $this->hasColumnMapping = $condition;

        return $this;
    }

    public function hasColumnMapping(): bool
    {
        return (bool) $this->evaluate($this->hasColumnMapping);
    }
}
