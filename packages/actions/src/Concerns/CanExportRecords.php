<?php

namespace Filament\Actions\Concerns;

use AnourValar\EloquentSerialize\Facades\EloquentSerializeFacade;
use Closure;
use Filament\Actions\Action;
use Filament\Actions\ExportAction;
use Filament\Actions\ExportBulkAction;
use Filament\Actions\Exports\Enums\Contracts\ExportFormat as ExportFormatInterface;
use Filament\Actions\Exports\Enums\ExportFormat;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Jobs\CreateXlsxFile;
use Filament\Actions\Exports\Jobs\ExportCompletion;
use Filament\Actions\Exports\Jobs\PrepareCsvExport;
use Filament\Actions\Exports\Models\Export;
use Filament\Actions\View\ActionsIconAlias;
use Filament\Facades\Filament;
use Filament\Forms;
use Filament\Notifications\Notification;
use Filament\Schemas\Components\Actions;
use Filament\Schemas\Components\Fieldset;
use Filament\Schemas\Components\Flex;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Support\Enums\Size;
use Filament\Support\Enums\Width;
use Filament\Support\Facades\FilamentIcon;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Contracts\HasTable;
use Illuminate\Bus\PendingBatch;
use Illuminate\Foundation\Bus\PendingChain;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Number;
use Livewire\Component;
use LogicException;

trait CanExportRecords
{
    /**
     * @var class-string<Exporter>
     */
    protected string $exporter;

    protected ?string $job = null;

    protected int | Closure $chunkSize = 100;

    protected int | Closure $columnMappingColumns = 1;

    protected int | Closure | null $maxRows = null;

    protected string | Closure | null $csvDelimiter = null;

    /**
     * @var array<string, mixed> | Closure
     */
    protected array | Closure $options = [];

    protected string | Closure | null $fileDisk = null;

    protected string | Closure | null $fileName = null;

    /**
     * @var array<ExportFormatInterface> | Closure | null
     */
    protected array | Closure | null $formats = null;

    protected ?Closure $modifyQueryUsing = null;

    protected bool | Closure $hasColumnMapping = true;

    protected bool | Closure $isEnablingVisibleTableColumnsByDefault = false;

    protected string | Closure | null $authGuard = null;

    protected function setUp(): void
    {
        parent::setUp();

        $this->label(fn (ExportAction | ExportBulkAction $action): string => __('filament-actions::export.label', ['label' => $action->getPluralModelLabel()]));

        $this->modalHeading(fn (ExportAction | ExportBulkAction $action): string => __('filament-actions::export.modal.heading', ['label' => $action->getTitleCasePluralModelLabel()]));

        $this->modalSubmitActionLabel(__('filament-actions::export.modal.actions.export.label'));

        $this->groupedIcon(FilamentIcon::resolve(ActionsIconAlias::EXPORT_ACTION_GROUPED) ?? Heroicon::ArrowDownTray);

        $this->schema(fn (ExportAction | ExportBulkAction $action): array => [
            ...($action->hasColumnMapping() ? [Fieldset::make(__('filament-actions::export.modal.form.columns.label'))
                ->columns(match ($columns = $action->getColumnMappingColumns()) {
                    1 => 1,
                    2 => [
                        'sm' => 2,
                        'lg' => 2,
                    ],
                    3 => [
                        'sm' => 2,
                        'lg' => 3,
                    ],
                    default => [
                        'sm' => 2,
                        'md' => 3,
                        'lg' => $columns,
                    ],
                })
                ->schema(function () use ($action): array {
                    $isEnablingVisibleTableColumnsByDefault = $action->isEnablingVisibleTableColumnsByDefault();
                    $visibleTableColumnNames = $isEnablingVisibleTableColumnsByDefault ? $action->getVisibleTableColumnNames() : [];

                    $columns = $action->getExporter()::getColumns();
                    $hasMultipleToggleableColumns = count($columns) > 1;

                    return [
                        ...($hasMultipleToggleableColumns ? [Actions::make([
                            Action::make('selectAll')
                                ->label(__('filament-actions::export.modal.form.columns.actions.select_all.label'))
                                ->link()
                                ->size(Size::Small)
                                ->action(function (Set $set) use ($columns): void {
                                    foreach ($columns as $column) {
                                        $set("{$column->getName()}.isEnabled", true);
                                    }
                                })
                                ->visible(function (Get $get) use ($columns): bool {
                                    foreach ($columns as $column) {
                                        if (! $get("{$column->getName()}.isEnabled")) {
                                            return true;
                                        }
                                    }

                                    return false;
                                }),
                            Action::make('deselectAll')
                                ->label(__('filament-actions::export.modal.form.columns.actions.deselect_all.label'))
                                ->link()
                                ->size(Size::Small)
                                ->action(function (Set $set) use ($columns): void {
                                    foreach ($columns as $column) {
                                        $set("{$column->getName()}.isEnabled", false);
                                    }
                                })
                                ->visible(function (Get $get) use ($columns): bool {
                                    foreach ($columns as $column) {
                                        if ($get("{$column->getName()}.isEnabled")) {
                                            return true;
                                        }
                                    }

                                    return false;
                                }),
                        ])->columnSpanFull()] : []),
                        ...array_map(
                            fn (ExportColumn $column): Flex => Flex::make([
                                Forms\Components\Checkbox::make('isEnabled')
                                    ->label(__('filament-actions::export.modal.form.columns.form.is_enabled.label', ['column' => $column->getName()]))
                                    ->hiddenLabel()
                                    ->default(
                                        $isEnablingVisibleTableColumnsByDefault
                                            ? (in_array($column->getName(), $visibleTableColumnNames) && $column->isEnabledByDefault())
                                            : $column->isEnabledByDefault()
                                    )
                                    ->live()
                                    ->grow(false),
                                Forms\Components\TextInput::make('label')
                                    ->label(__('filament-actions::export.modal.form.columns.form.label.label', ['column' => $column->getName()]))
                                    ->hiddenLabel()
                                    ->default($column->getLabel())
                                    ->placeholder($column->getLabel())
                                    ->disabled(fn (Get $get): bool => ! $get('isEnabled'))
                                    ->required(fn (Get $get): bool => (bool) $get('isEnabled')),
                            ])
                                ->verticallyAlignCenter()
                                ->statePath($column->getName()),
                            $columns,
                        ),
                    ];
                })
                ->statePath('columnMap')] : []),
            ...$action->getExporter()::getOptionsFormComponents(),
        ]);

        $this->action(function (ExportAction | ExportBulkAction $action, array $data, Component $livewire): void {
            $exporter = $action->getExporter();

            if ($livewire instanceof HasTable) {
                $query = $livewire->getTableQueryForExport();
            } else {
                $query = $exporter::getModel()::query();
            }

            $query = $exporter::modifyQuery($query);

            $options = array_merge(
                $action->getOptions(),
                Arr::except($data, ['columnMap']),
            );

            if ($this->modifyQueryUsing) {
                $query = $this->evaluate($this->modifyQueryUsing, [
                    'query' => $query,
                    'options' => $options,
                ]) ?? $query;
            }

            $records = $action instanceof ExportBulkAction ? $action->getIndividuallyAuthorizedSelectedRecords() : null;

            $totalRows = $records ? $records->count() : $query->toBase()->getCountForPagination();

            if ((! $records) && $query->getQuery()->limit) {
                $totalRows = min($totalRows, $query->getQuery()->limit);
            }

            $maxRows = $action->getMaxRows() ?? $totalRows;

            if ($maxRows < $totalRows) {
                $action->failureNotification(
                    Notification::make()
                        ->title(__('filament-actions::export.notifications.max_rows.title'))
                        ->body(trans_choice('filament-actions::export.notifications.max_rows.body', $maxRows, [
                            'count' => Number::format($maxRows),
                        ]))
                        ->danger(),
                );

                $action->failure();

                return;
            }

            $authGuard = $action->getAuthGuard();

            $user = auth($authGuard)->user();

            if ($action->hasColumnMapping()) {
                $columnMap = collect($exporter::getColumns())
                    ->filter(fn (ExportColumn $column): bool => (bool) data_get($data['columnMap'], "{$column->getName()}.isEnabled", false))
                    ->mapWithKeys(fn (ExportColumn $column): array => [
                        $column->getName() => data_get($data['columnMap'], "{$column->getName()}.label", $column->getLabel()),
                    ])
                    ->all();
            } else {
                $isEnablingVisibleTableColumnsByDefault = $action->isEnablingVisibleTableColumnsByDefault();
                $visibleTableColumnNames = $isEnablingVisibleTableColumnsByDefault ? $action->getVisibleTableColumnNames() : [];

                $columnMap = collect($exporter::getColumns())
                    ->when(
                        $isEnablingVisibleTableColumnsByDefault,
                        fn ($columns): Collection => $columns->filter(
                            fn (ExportColumn $column): bool => in_array($column->getName(), $visibleTableColumnNames) && $column->isEnabledByDefault(),
                        ),
                    )
                    ->mapWithKeys(fn (ExportColumn $column): array => [$column->getName() => $column->getLabel()])
                    ->all();
            }

            if (empty($columnMap)) {
                Notification::make()
                    ->title(__('filament-actions::export.notifications.no_columns.title'))
                    ->body(__('filament-actions::export.notifications.no_columns.body'))
                    ->danger()
                    ->send();

                $action->halt();

                return;
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
            // Temporary save to obtain the sequence number of the export file.
            $export->save();

            // Delete the export directory to prevent data contamination from previous exports with the same ID.
            $export->deleteFileDirectory();

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
                Bus::batch([app($job, [
                    'export' => $export,
                    'query' => $serializedQuery,
                    'columnMap' => $columnMap,
                    'options' => $options,
                    'chunkSize' => $action->getChunkSize(),
                    'records' => $records?->all(),
                ])])
                    ->allowFailures()
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
                    ),
                ...(($hasXlsx && (! $hasCsv)) ? [$makeCreateXlsxFileJob()] : []),
                app(ExportCompletion::class, [
                    'authGuard' => $authGuard,
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

            if (
                ($jobConnection === 'sync')
                || (blank($jobConnection) && (config('queue.default') === 'sync'))
            ) {
                $action->successNotification(null);
                $action->successNotificationTitle(null);

                return;
            }

            $action->successNotification(
                Notification::make()
                    ->title($action->getSuccessNotificationTitle())
                    ->body(trans_choice('filament-actions::export.notifications.started.body', $export->total_rows, [
                        'count' => Number::format($export->total_rows),
                    ]))
                    ->success(),
            );
        });

        $this->defaultColor('gray');

        $this->modalWidth(static fn (ExportAction | ExportBulkAction $action): Width => match ($action->getColumnMappingColumns()) {
            1 => Width::Medium,
            2 => Width::ThreeExtraLarge,
            3 => Width::FiveExtraLarge,
            default => Width::SevenExtraLarge,
        });

        $this->successNotificationTitle(__('filament-actions::export.notifications.started.title'));

        if (! $this instanceof ExportBulkAction) {
            $this->model(fn (ExportAction $action): string => $action->getExporter()::getModel());
        }
    }

    public static function getDefaultName(): ?string
    {
        return 'export';
    }

    public function columnMappingColumns(int | Closure $columns): static
    {
        $this->columnMappingColumns = $columns;

        return $this;
    }

    public function getColumnMappingColumns(): int
    {
        return $this->evaluate($this->columnMappingColumns);
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
     * @return array<string>
     */
    public function getVisibleTableColumnNames(): array
    {
        if (! $this->getLivewire() instanceof HasTable) {
            throw new LogicException('Cannot get visible table columns from a non-table Livewire component.');
        }

        return array_keys($this->getLivewire()->getTable()->getVisibleColumns());
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
     * @param  array<ExportFormatInterface> | Closure | null  $formats
     */
    public function formats(array | Closure | null $formats): static
    {
        $this->formats = $formats;

        return $this;
    }

    /**
     * @return array<ExportFormatInterface> | null
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

    public function enableVisibleTableColumnsByDefault(bool | Closure $condition = true): static
    {
        $this->isEnablingVisibleTableColumnsByDefault = $condition;

        return $this;
    }

    public function isEnablingVisibleTableColumnsByDefault(): bool
    {
        return (bool) $this->evaluate($this->isEnablingVisibleTableColumnsByDefault);
    }

    public function authGuard(string | Closure | null $authGuard): static
    {
        $this->authGuard = $authGuard;

        return $this;
    }

    public function getAuthGuard(): string
    {
        $guard = $this->evaluate($this->authGuard);

        if (filled($guard)) {
            return $guard;
        }

        if (class_exists(Filament::class) && Filament::isServing()) {
            return Filament::getAuthGuard();
        }

        $authGuard = auth();

        if (! property_exists($authGuard, 'name')) {
            return config('auth.defaults.guard') ?? 'web';
        }

        return $authGuard->name;
    }
}
