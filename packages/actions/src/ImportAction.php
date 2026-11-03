<?php

namespace Filament\Actions;

use Closure;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\ImportDispatcher;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Jobs\ImportCsv;
use Filament\Actions\Imports\Models\Import;
use Filament\Actions\Testing\ImportFake;
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
use Generator;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Filesystem\AwsS3V3Adapter;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Storage;
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

    public static function fake(): ImportFake
    {
        $fake = app(ImportFake::class);

        app()->instance(ImportDispatcher::class, $fake);

        return $fake;
    }

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

                    $csvReader = CsvReader::from($csvStream);

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

                    $csvReader = CsvReader::from($csvStream);

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

            $csvReader = CsvReader::from($csvStream);

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

            /** @var Generator<int, array<array<string, string>>> $importChunks */
            $importChunks = $importChunkIterator->get();
            $job = $action->getJob();

            $options = array_merge(
                $action->getOptions(),
                Arr::except($data, ['file', 'columnMap']),
            );

            $jobConnection = app(ImportDispatcher::class)->dispatch(
                import: $import,
                importChunks: $importChunks,
                columnMap: $data['columnMap'],
                options: $options,
                job: $job,
                authGuard: $authGuard,
            );

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

        $sampledLineCount = 0;
        $scannedBytes = 0;
        $contentSample = '';

        while ((! feof($resource)) && ($sampledLineCount < 20) && ($scannedBytes < (1024 * 1024))) {
            $line = fgets($resource);

            if ($line === false) {
                break;
            }

            $scannedBytes += strlen($line);

            // Only lines holding a non-ASCII byte tell the encodings apart, so ASCII-only
            // lines are skipped and an accent on line 500 still reaches the sample, as long
            // as it lies within the first mebibyte of the file.
            if (! preg_match('/[\x80-\xFF]/', $line)) {
                continue;
            }

            $contentSample .= $line;
            $sampledLineCount++;
        }

        // The encoding of a subset should be declared before the encoding of its superset.
        // The multi-byte pages keep their original order. EUC-JP is checked only after
        // GB18030 has failed or been vetoed, and every EUC-JP two-byte character is also
        // valid GB18030, so EUC-JP is returned only for rare samples, in practice ones
        // holding JIS X 0212 three-byte characters.
        $encodings = [
            'UTF-8',
            'SJIS-win',
            'EUC-KR',
            'GB18030',
            'EUC-JP',
            'Windows-1252',
        ];

        // Western text is often structurally valid in a multi-byte encoding too, but it
        // then decodes to one or two CJK characters glued to ASCII letters or digits, or
        // to a lone half-width katakana between ASCII characters: the Windows-1252 bytes
        // of `Félix` are valid Shift JIS, but decode to `F`, one kanji and `ix`. Real CJK
        // text almost always contains a run of CJK characters standing on its own, or
        // three CJK characters in a row, so a multi-byte verdict is vetoed when the decoded
        // sample shows glued characters but no such run. A file whose only CJK text is
        // one or two characters glued to ASCII, such as a `3個` quantity or `B級品` grade
        // column under an ASCII header, is the known exception and is read as Windows-1252.
        $multiByteEncodings = [
            'SJIS-win',
            'EUC-KR',
            'GB18030',
            'EUC-JP',
        ];

        $gluedCharacterPattern = <<<'REGEX'
            ~
            # an ASCII letter or digit directly followed by a non-ASCII character
            [A-Za-z0-9][^\x00-\x7F]
            # or a non-ASCII character directly followed by an ASCII letter or digit
            | [^\x00-\x7F][A-Za-z0-9]
            # or a half-width katakana with no non-ASCII character on either side
            | (?<![^\x00-\x7F])[\x{FF61}-\x{FF9F}](?![^\x00-\x7F])
            ~ux
            REGEX;

        $cjkRunPattern = <<<'REGEX'
            ~
            # a whole run of non-ASCII characters, other than a lone half-width katakana,
            # with no ASCII letter or digit directly before or after it
            (?<![A-Za-z0-9\x80-\x{10FFFF}])(?![\x{FF61}-\x{FF9F}](?![^\x00-\x7F]))[^\x00-\x7F]++(?![A-Za-z0-9])
            # or three non-ASCII characters in a row, none of them from the private-use area
            | [^\x00-\x7F\x{E000}-\x{F8FF}]{3}
            ~ux
            REGEX;

        foreach ($encodings as $encoding) {
            if (! mb_check_encoding($contentSample, $encoding)) {
                continue;
            }

            if (in_array($encoding, $multiByteEncodings, true)) {
                $decodedSample = mb_convert_encoding($contentSample, 'UTF-8', $encoding);

                if (
                    preg_match($gluedCharacterPattern, $decodedSample) &&
                    (! preg_match($cjkRunPattern, $decodedSample))
                ) {
                    continue;
                }
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

                $csvReader = CsvReader::from($csvStream);

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

    /**
     * @param  Model | array<string, mixed> | null  $record
     * @return Model | array<string, mixed> | null
     */
    protected function ensureCorrectRecordType(Model | array | null $record): Model | array | null
    {
        return $record;
    }
}
