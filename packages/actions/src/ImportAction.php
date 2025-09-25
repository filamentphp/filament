<?php

namespace Filament\Actions;

use Closure;
use Filament\Actions\Imports\Events\ImportCompleted;
use Filament\Actions\Imports\Events\ImportStarted;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Jobs\ImportCsv;
use Filament\Actions\Imports\Models\Import;
use Filament\Actions\View\ActionsIconAlias;
use Filament\Facades\Filament;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Notifications\Notification;
use Filament\Schemas\Components\Fieldset;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Support\ChunkIterator;
use Filament\Support\Facades\FilamentIcon;
use Filament\Support\Icons\Heroicon;
use Illuminate\Bus\PendingBatch;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Filesystem\AwsS3V3Adapter;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Number;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use League\Csv\Bom;
use League\Csv\CharsetConverter;
use League\Csv\Info;
use League\Csv\Reader as CsvReader;
use League\Csv\Statement;
use League\Csv\Writer;
use Livewire\Component;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use SplTempFileObject;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ImportAction extends Action
{
    /**
     * @var class-string<Importer>
     */
    protected string $importer;

    protected ?string $job = null;

    protected int | Closure $chunkSize = 100;

    protected int | Closure | null $maxRows = null;

    protected int | Closure | null $headerOffset = null;

    protected string | Closure | null $csvDelimiter = null;

    /**
     * @var array<string, mixed> | Closure
     */
    protected array | Closure $options = [];

    /**
     * @var array<string | array<mixed> | Closure>
     */
    protected array $fileValidationRules = [];

    protected string | Closure | null $authGuard = null;

    protected function setUp(): void
    {
        parent::setUp();

        $this->label(fn (ImportAction $action): string => __('filament-actions::import.label', ['label' => $action->getPluralModelLabel()]));

        $this->modalHeading(fn (ImportAction $action): string => __('filament-actions::import.modal.heading', ['label' => $action->getTitleCasePluralModelLabel()]));

        $this->modalDescription(fn (ImportAction $action): Htmlable => $action->getModalAction('downloadExample'));

        $this->modalSubmitActionLabel(__('filament-actions::import.modal.actions.import.label'));

        $this->groupedIcon(FilamentIcon::resolve(ActionsIconAlias::IMPORT_ACTION_GROUPED) ?? Heroicon::ArrowUpTray);

        $this->schema(fn (ImportAction $action): array => array_merge([
            FileUpload::make('file')
                ->label(__('filament-actions::import.modal.form.file.label'))
                ->placeholder(__('filament-actions::import.modal.form.file.placeholder'))
                ->acceptedFileTypes(['text/csv', 'text/x-csv', 'application/csv', 'application/x-csv', 'text/comma-separated-values', 'text/x-comma-separated-values', 'text/plain', 'application/vnd.ms-excel'])
                ->rules($action->getFileValidationRules())
                ->afterStateUpdated(function (FileUpload $component, Component $livewire, Set $set, ?TemporaryUploadedFile $state) use ($action): void {
                    if (! $state instanceof TemporaryUploadedFile) {
                        return;
                    }

                    try {
                        $livewire->validateOnly($component->getStatePath());
                    } catch (ValidationException $exception) {
                        $component->state([]);

                        throw $exception;
                    }

                    $csvStream = $this->getUploadedFileStream($state);

                    if (! $csvStream) {
                        return;
                    }

                    $csvReader = CsvReader::createFromStream($csvStream);

                    if (filled($csvDelimiter = $this->getCsvDelimiter($csvReader))) {
                        $csvReader->setDelimiter($csvDelimiter);
                    }

                    $csvReader->setHeaderOffset($action->getHeaderOffset() ?? 0);

                    $csvColumns = $csvReader->getHeader();

                    $lowercaseCsvColumnValues = array_map(Str::lower(...), $csvColumns);
                    $lowercaseCsvColumnKeys = array_combine(
                        $lowercaseCsvColumnValues,
                        $csvColumns,
                    );

                    $set('columnMap', array_reduce($action->getImporter()::getColumns(), function (array $carry, ImportColumn $column) use ($lowercaseCsvColumnKeys, $lowercaseCsvColumnValues) {
                        $carry[$column->getName()] = $lowercaseCsvColumnKeys[
                        Arr::first(
                            array_intersect(
                                $lowercaseCsvColumnValues,
                                $column->getGuesses(),
                            ),
                        )
                        ] ?? null;

                        return $carry;
                    }, []));
                })
                ->storeFiles(false)
                ->visibility('private')
                ->required()
                ->hiddenLabel(),
            Fieldset::make(__('filament-actions::import.modal.form.columns.label'))
                ->columns(1)
                ->inlineLabel()
                ->schema(function (Get $get) use ($action): array {
                    $csvFile = $get('file');

                    if (! $csvFile instanceof TemporaryUploadedFile) {
                        return [];
                    }

                    $csvStream = $this->getUploadedFileStream($csvFile);

                    if (! $csvStream) {
                        return [];
                    }

                    $csvReader = CsvReader::createFromStream($csvStream);

                    if (filled($csvDelimiter = $this->getCsvDelimiter($csvReader))) {
                        $csvReader->setDelimiter($csvDelimiter);
                    }

                    $csvReader->setHeaderOffset($action->getHeaderOffset() ?? 0);

                    $csvColumns = $csvReader->getHeader();
                    $csvColumnOptions = array_combine($csvColumns, $csvColumns);

                    return array_map(
                        fn (ImportColumn $column): Select => $column->getSelect()->options($csvColumnOptions),
                        $action->getImporter()::getColumns(),
                    );
                })
                ->statePath('columnMap')
                ->visible(fn (Get $get): bool => $get('file') instanceof TemporaryUploadedFile),
        ], $action->getImporter()::getOptionsFormComponents()));

        $this->action(function (ImportAction $action, array $data): void {
            /** @var TemporaryUploadedFile $csvFile */
            $csvFile = $data['file'];

            $csvStream = $this->getUploadedFileStream($csvFile);

            if (! $csvStream) {
                return;
            }

            $csvReader = CsvReader::createFromStream($csvStream);

            if (filled($csvDelimiter = $this->getCsvDelimiter($csvReader))) {
                $csvReader->setDelimiter($csvDelimiter);
            }

            $csvReader->setHeaderOffset($action->getHeaderOffset() ?? 0);
            $csvResults = (new Statement)->process($csvReader);

            $totalRows = $csvResults->count();
            $maxRows = $action->getMaxRows() ?? $totalRows;

            if ($maxRows < $totalRows) {
                $action->failureNotification(
                    Notification::make()
                        ->title(__('filament-actions::import.notifications.max_rows.title'))
                        ->body(trans_choice('filament-actions::import.notifications.max_rows.body', $maxRows, [
                            'count' => Number::format($maxRows),
                        ]))
                        ->danger(),
                );

                $action->failure();

                return;
            }

            $authGuard = $action->getAuthGuard();

            $user = auth($authGuard)->user();

            $import = app(Import::class);
            $import->user()->associate($user);
            $import->file_name = $csvFile->getClientOriginalName();
            $import->file_path = $csvFile->getRealPath();
            $import->importer = $action->getImporter();
            $import->total_rows = $totalRows;
            $import->save();

            $importChunkIterator = new ChunkIterator($csvResults->getRecords(), chunkSize: $action->getChunkSize());

            /** @var array<array<array<string, string>>> $importChunks */
            $importChunks = $importChunkIterator->get(); /** @phpstan-ignore varTag.nativeType */
            $job = $action->getJob();

            $options = array_merge(
                $action->getOptions(),
                Arr::except($data, ['file', 'columnMap']),
            );

            // We do not want to send the loaded user relationship to the queue in job payloads,
            // in case it contains attributes that are not serializable, such as binary columns.
            $import->unsetRelation('user');

            $importJobs = collect($importChunks)
                ->map(fn (array $importChunk): object => app($job, [
                    'import' => $import,
                    'rows' => base64_encode(serialize($importChunk)),
                    'columnMap' => $data['columnMap'],
                    'options' => $options,
                ]));

            $columnMap = $data['columnMap'];

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

                    $failedRowsCount = $import->getFailedRowsCount();

                    Notification::make()
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
                            ($jobConnection === 'sync') ||
                            (blank($jobConnection) && (config('queue.default') === 'sync')),
                            fn (Notification $notification) => $notification
                                ->persistent()
                                ->send(),
                            fn (Notification $notification) => $notification->sendToDatabase($import->user, isEventDispatched: true),
                        );
                })
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
                    ->body(trans_choice('filament-actions::import.notifications.started.body', $import->total_rows, [
                        'count' => Number::format($import->total_rows),
                    ]))
                    ->success(),
            );
        });

        $this->registerModalActions([
            Action::make('downloadExample')
                ->label(__('filament-actions::import.modal.actions.download_example.label'))
                ->link()
                ->action(function (): StreamedResponse {
                    $columns = $this->getImporter()::getColumns();

                    $csv = Writer::createFromFileObject(new SplTempFileObject);

                    if (filled($csvDelimiter = $this->getCsvDelimiter())) {
                        $csv->setDelimiter($csvDelimiter);
                    }

                    $csv->insertOne(array_map(
                        fn (ImportColumn $column): string => $column->getExampleHeader(),
                        $columns,
                    ));

                    $columnExamples = array_map(
                        fn (ImportColumn $column): array => $column->getExamples(),
                        $columns,
                    );

                    $exampleRowsCount = array_reduce(
                        $columnExamples,
                        fn (int $count, array $exampleData): int => max($count, count($exampleData)),
                        initial: 0,
                    );

                    $exampleRows = [];

                    foreach ($columnExamples as $exampleData) {
                        for ($i = 0; $i < $exampleRowsCount; $i++) {
                            $exampleRows[$i][] = $exampleData[$i] ?? '';
                        }
                    }

                    $csv->insertAll($exampleRows);

                    return response()->streamDownload(function () use ($csv): void {
                        $csv->setOutputBOM(Bom::Utf8);

                        echo $csv->toString();
                    }, __('filament-actions::import.example_csv.file_name', ['importer' => (string) str($this->getImporter())->classBasename()->kebab()]), [
                        'Content-Type' => 'text/csv; charset=UTF-8',
                    ]);
                }),
        ]);

        $this->defaultColor('gray');

        $this->modalWidth('xl');

        $this->successNotificationTitle(__('filament-actions::import.notifications.started.title'));

        $this->model(fn (ImportAction $action): string => $action->getImporter()::getModel());
    }

    /**
     * @return resource | false
     */
    public function getUploadedFileStream(TemporaryUploadedFile $file)
    {
        $fileDisk = invade($file)->disk; /** @phpstan-ignore-line */
        if (config("filesystems.disks.{$fileDisk}.driver") !== 's3') {
            $resource = $file->readStream();
        } else {
            /** @var AwsS3V3Adapter $s3Adapter */
            $s3Adapter = Storage::disk($fileDisk)->getAdapter();

            invade($s3Adapter)->client->registerStreamWrapper(); /** @phpstan-ignore-line */
            $fileS3Path = (string) str('s3://' . config("filesystems.disks.{$fileDisk}.bucket") . '/' . $file->getRealPath())->replace('\\', '/');

            $resource = fopen($fileS3Path, mode: 'r', context: stream_context_create([
                's3' => [
                    'seekable' => true,
                ],
            ]));
        }

        $inputEncoding = $this->detectCsvEncoding($resource);
        $outputEncoding = 'UTF-8';

        if (
            filled($inputEncoding) &&
            (Str::lower($inputEncoding) !== Str::lower($outputEncoding))
        ) {
            CharsetConverter::register();

            stream_filter_append(
                $resource,
                CharsetConverter::getFiltername($inputEncoding, $outputEncoding),
                STREAM_FILTER_READ,
            );
        }

        return $resource;
    }

    protected function detectCsvEncoding(mixed $resource): ?string
    {
        rewind($resource);

        $lineCount = 0;
        $contentSample = '';

        while ((! feof($resource)) && ($lineCount < 20)) {
            $line = fgets($resource);

            if ($line === false) {
                break;
            }

            $contentSample .= $line;
            $lineCount++;
        }

        // The encoding of a subset should be declared before the encoding of its superset.
        $encodings = [
            'UTF-8',
            'SJIS-win',
            'EUC-KR',
            'ISO-8859-1',
            'GB18030',
            'Windows-1251',
            'Windows-1252',
            'EUC-JP',
        ];

        foreach ($encodings as $encoding) {
            if (! mb_check_encoding($contentSample, $encoding)) {
                continue;
            }

            return $encoding;
        }

        return null;
    }

    public static function getDefaultName(): ?string
    {
        return 'import';
    }

    /**
     * @param  class-string<Importer>  $importer
     */
    public function importer(string $importer): static
    {
        $this->importer = $importer;

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

    public function headerOffset(int | Closure | null $offset): static
    {
        $this->headerOffset = $offset;

        return $this;
    }

    public function csvDelimiter(string | Closure | null $delimiter): static
    {
        $this->csvDelimiter = $delimiter;

        return $this;
    }

    /**
     * @return class-string<Importer>
     */
    public function getImporter(): string
    {
        return $this->importer;
    }

    /**
     * @return class-string
     */
    public function getJob(): string
    {
        return $this->job ?? ImportCsv::class;
    }

    public function getChunkSize(): int
    {
        return $this->evaluate($this->chunkSize);
    }

    public function getMaxRows(): ?int
    {
        return $this->evaluate($this->maxRows);
    }

    public function getHeaderOffset(): ?int
    {
        return $this->evaluate($this->headerOffset);
    }

    public function getCsvDelimiter(?CsvReader $reader = null): ?string
    {
        return $this->evaluate($this->csvDelimiter) ?? $this->guessCsvDelimiter($reader);
    }

    protected function guessCsvDelimiter(?CsvReader $reader = null): ?string
    {
        if (! $reader) {
            return null;
        }

        $delimiterCounts = Info::getDelimiterStats($reader, delimiters: [',', ';', '|', "\t"], limit: 10);

        return array_search(max($delimiterCounts), $delimiterCounts);
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

    /**
     * @param  string | array<mixed> | Closure  $rules
     */
    public function fileRules(string | array | Closure $rules): static
    {
        $this->fileValidationRules = [
            ...$this->fileValidationRules,
            $rules,
        ];

        return $this;
    }

    /**
     * @return array<mixed>
     */
    public function getFileValidationRules(): array
    {
        $fileRules = [
            'extensions:csv,txt',
            fn (): Closure => function (string $attribute, mixed $value, Closure $fail): void {
                $csvStream = $this->getUploadedFileStream($value);

                if (! $csvStream) {
                    return;
                }

                $csvReader = CsvReader::createFromStream($csvStream);

                if (filled($csvDelimiter = $this->getCsvDelimiter($csvReader))) {
                    $csvReader->setDelimiter($csvDelimiter);
                }

                $csvReader->setHeaderOffset($this->getHeaderOffset() ?? 0);

                $csvColumns = $csvReader->getHeader();

                $duplicateCsvColumns = [];

                foreach (array_count_values($csvColumns) as $header => $count) {
                    if ($count <= 1) {
                        continue;
                    }

                    $duplicateCsvColumns[] = $header;
                }

                if (empty($duplicateCsvColumns)) {
                    return;
                }

                $filledDuplicateCsvColumns = array_filter($duplicateCsvColumns, fn ($value): bool => filled($value));

                $fail(trans_choice('filament-actions::import.modal.form.file.rules.duplicate_columns', count($filledDuplicateCsvColumns), [
                    'columns' => implode(', ', $filledDuplicateCsvColumns),
                ]));
            },
        ];

        foreach ($this->fileValidationRules as $rules) {
            $rules = $this->evaluate($rules);

            if (is_string($rules)) {
                $rules = explode('|', $rules);
            }

            $fileRules = [
                ...$fileRules,
                ...$rules,
            ];
        }

        return $fileRules;
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
