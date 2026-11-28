<?php

use Filament\Actions\ImportAction;
use Filament\Actions\Imports\Downloaders\CsvDownloader;
use Filament\Actions\Imports\Events\ImportStarted;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\Import;
use Filament\Forms\Components\TextInput;
use Filament\Tests\Actions\TestCase;
use Filament\Tests\Fixtures\Models\Post;
use Filament\Tests\Fixtures\Models\User;
use Filament\Tests\Fixtures\Pages\Actions;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Exceptions;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use League\Csv\Reader;
use Livewire\Component;

use function Filament\Tests\livewire;
use function Pest\Laravel\assertDatabaseCount;
use function Pest\Laravel\assertDatabaseHas;

uses(TestCase::class);

beforeEach(function (): void {
    // Keep fake upload paths within the `imports.file_path` column's length limit.
    Storage::set('tmp-for-tests', Storage::build([
        'driver' => 'local',
        'root' => sys_get_temp_dir() . '/filament-imports-' . bin2hex(random_bytes(8)),
    ]));

    $this->busDispatcher = Bus::getFacadeRoot();
    Bus::fake();
    Event::fake([ImportStarted::class]);
});

afterEach(function (): void {
    File::deleteDirectory(Storage::disk('tmp-for-tests')->path(''));
});

it('submits a real `ImportAction` with CSV mapping and merged options without running the import', function (): void {
    $csv = "Article headline,Article body\nFirst article,First body\nSecond article,Second body\n";
    $user = auth()->user();

    livewire(ImportActionSubmissionPage::class)
        ->mountAction('import')
        ->fillForm(['file' => UploadedFile::fake()->createWithContent('articles.csv', $csv)])
        ->assertSchemaStateSet(['region' => 'Europe'])
        ->fillForm([
            'columnMap' => ['title' => 'Article headline', 'content' => 'Article body'],
            'mode' => 'replace',
        ])
        ->callMountedAction()
        ->assertHasNoFormErrors();

    Event::assertDispatched(ImportStarted::class, function (ImportStarted $event) use ($csv, $user): bool {
        $import = $event->getImport();

        expect($import->exists)->toBeTrue()
            ->and($import->importer)->toBe(SubmissionPostImporter::class)
            ->and($import->user->is($user))->toBeTrue()
            ->and($import->file_name)->toBe('articles.csv')
            ->and($import->total_rows)->toBe(2)
            ->and(strlen($import->file_path))->toBeLessThanOrEqual(255)
            ->and(file_get_contents($import->file_path))->toBe($csv)
            ->and($event->getColumnMap())->toBe(['title' => 'Article headline', 'content' => 'Article body'])
            ->and($event->getOptions())->toBe(['mode' => 'replace', 'source' => 'catalog', 'region' => 'Europe']);

        return true;
    });
    Event::assertDispatchedTimes(ImportStarted::class, 1);
    Bus::assertBatchCount(1);

    assertDatabaseCount('imports', 1);
    assertDatabaseHas('imports', ['total_rows' => 2, 'processed_rows' => 0, 'successful_rows' => 0, 'completed_at' => null]);
    assertDatabaseCount('posts', 0);
    assertDatabaseCount('jobs', 0);
});

it('uses `csvEncoding()` for column mapping and imported values', function (?string $encoding, array $headers, array $rows, bool $shouldDetectCsvEncoding = false): void {
    Bus::swap($this->busDispatcher);
    app()->bind(Authenticatable::class, User::class);
    Exceptions::fake()->throwOnReport();

    $csv = implode(',', $headers) . "\r\n";

    foreach ($rows as $row) {
        $csv .= implode(',', $row) . "\r\n";
    }

    livewire(ImportActionSubmissionPage::class)
        ->set('csvEncoding', $encoding)
        ->set('shouldDetectCsvEncoding', $shouldDetectCsvEncoding)
        ->mountAction('import')
        ->fillForm(['file' => UploadedFile::fake()->createWithContent('articles.csv', mb_convert_encoding($csv, $encoding ?? 'UTF-8', 'UTF-8'))])
        ->assertSchemaStateSet(['columnMap' => ['title' => $headers[0], 'content' => $headers[1]]])
        ->callMountedAction()
        ->assertHasNoFormErrors();

    expect(Post::query()->orderBy('id')->get(['title', 'content'])->toArray())->toBe(array_map(
        static fn (array $row): array => ['title' => $row[0], 'content' => $row[1]],
        $rows,
    ));
})->with([
    'Windows-1252 headers and punctuation' => ['Windows-1252', ['Intitulé', 'Résumé'], [['Félix', 'O’Brien – €'], ['þú', '½ kg']]],
    'Windows-1252 accents after the sample' => ['Windows-1252', ['title', 'content'], [...array_fill(0, 20, ['Row', 'Body']), ['André', 'O’Brien']]],
    'Shift_JIS quantities' => ['SJIS-win', ['件名', '内容'], [['3個', '1000円'], ['ｱ', 'A級品']]],
    'UTF-16LE headers' => ['UTF-16LE', ['Intitulé', 'Résumé'], [['Félix', '3個']]],
    'explicit UTF-8' => ['UTF-8', ['Intitulé', 'Résumé'], [['Félix', '3個']]],
    'automatic UTF-8' => [null, ['Intitulé', 'Résumé'], [['Félix', '3個']]],
    'custom detection' => ['Windows-1252', ['Intitulé', 'Résumé'], [['Félix', 'O’Brien – €']], true],
    'custom detection falling back to automatic detection' => [null, ['Intitulé', 'Résumé'], [['Félix', '3個']], true],
]);

it('preserves CSV cells when importing RFC CSV and re-importing generated CSV', function (string $source, ?string $delimiter): void {
    Bus::swap($this->busDispatcher);
    app()->bind(Authenticatable::class, User::class);
    Exceptions::fake()->throwOnReport();
    $values = ['quote\\",;CELL_MARKER', 'trailing,;slash\\', "line one\r\nline two", 'ordinary'];
    $expected = array_map(static fn (string $value): array => ['title' => $value, 'content' => 'NEIGHBOR_MARKER'], $values);
    $headers = ['title' => 'title', 'content' => 'Body\\",;HEADER_MARKER'];
    $csvRows = array_map(static fn (array $row): array => array_combine(array_values($headers), array_values($row)), $expected);

    ImportColumn::configureUsing(
        static fn (ImportColumn $column) => $column
            ->exampleHeader($headers[$column->getName()])
            ->label($headers[$column->getName()])
            ->examples($column->getName() === 'title' ? $values : array_fill(0, count($values), 'NEIGHBOR_MARKER')),
        isImportant: true,
    );

    if ($source === 'RFC') {
        $separator = $delimiter ?? ',';
        $content = "title{$separator}\"Body\\\"\",;HEADER_MARKER\"\r\n\"quote\\\"\",;CELL_MARKER\"{$separator}NEIGHBOR_MARKER\r\n\"trailing,;slash\\\"{$separator}NEIGHBOR_MARKER\r\n\"line one\r\nline two\"{$separator}NEIGHBOR_MARKER\r\nordinary{$separator}NEIGHBOR_MARKER\r\n";
    } elseif ($source === 'example') {
        $response = ImportAction::make()
            ->importer(SubmissionPostImporter::class)
            ->csvDelimiter($delimiter)
            ->livewire(new class extends Component {})
            ->getModalAction('downloadExample')
            ->call();
        ob_start();
        $response->sendContent();
        $content = ob_get_clean();
    } else {
        $import = Import::create([
            'file_name' => 'articles.csv',
            'file_path' => 'articles.csv',
            'importer' => SubmissionPostImporter::class,
            'total_rows' => count($values),
            'user_id' => auth()->id(),
        ]);
        foreach ($csvRows as $data) {
            $import->failedRows()->create(['data' => $data, 'validation_error' => 'INERT_ERROR']);
        }
        $response = app(CsvDownloader::class)($import);
        ob_start();
        $response->sendContent();
        $content = ob_get_clean();
    }

    $reader = Reader::fromString($content);
    $reader->setEscape('');
    $reader->setDelimiter($source === 'failure' ? ',' : ($delimiter ?? ','));
    $reader->setHeaderOffset(0);
    expect(array_values(iterator_to_array($reader->getRecords())))->toBe(array_map(
        static fn (array $row): array => $source === 'failure' ? [...$row, 'error' => 'INERT_ERROR'] : $row,
        $csvRows,
    ));

    ImportAction::configureUsing(
        static fn (ImportAction $action) => $action->csvDelimiter($source === 'failure' ? null : $delimiter),
        during: fn () => livewire(ImportActionSubmissionPage::class)
            ->mountAction('import')
            ->fillForm(['file' => UploadedFile::fake()->createWithContent('articles.csv', $content)])
            ->assertSchemaStateSet(['columnMap' => $headers])
            ->callMountedAction()
            ->assertHasNoFormErrors(),
    );

    expect(Post::query()->orderBy('id')->get(['title', 'content'])->toArray())->toBe($expected);
})->with([
    'RFC with detected delimiter' => ['RFC', null],
    'RFC with semicolon delimiter' => ['RFC', ';'],
    'example with detected delimiter' => ['example', null],
    'example with semicolon delimiter' => ['example', ';'],
    'failed rows' => ['failure', null],
]);

it('preserves CSV rows and fields when a Windows-1252 suffix invalidates the sampled Shift-JIS encoding', function (?string $encoding): void {
    Bus::swap($this->busDispatcher);
    app()->bind(Authenticatable::class, User::class);
    Exceptions::fake()->throwOnReport();

    $prefix = "Before;Metadata\r\ntitle;content\r\n" . str_repeat("Sample;£\r\n", 20);
    $csv = mb_convert_encoding($prefix . "\"café\";\"First; field\r\nSecond line\"\r\nLast;Fin\r\n", 'Windows-1252', 'UTF-8');

    expect(mb_check_encoding(mb_convert_encoding($prefix, 'Windows-1252', 'UTF-8'), 'SJIS-win'))->toBeTrue()
        ->and(mb_check_encoding($csv, 'SJIS-win'))->toBeFalse();

    livewire(ImportActionSubmissionPage::class)
        ->set('csvEncoding', $encoding)
        ->set('csvDelimiter', ';')
        ->set('headerOffset', 1)
        ->mountAction('import')
        ->fillForm(['file' => UploadedFile::fake()->createWithContent('articles.csv', $csv)])
        ->assertSchemaStateSet(['columnMap' => ['title' => 'title', 'content' => 'content']])
        ->callMountedAction()
        ->assertHasNoFormErrors();

    expect(Import::query()->sole()->total_rows)->toBe(23)
        ->and(Post::query()->count())->toBe(23)
        ->and(Post::query()->orderBy('id')->get(['title', 'content'])->take(-2)->values()->toArray())->toBe([
            ['title' => 'café', 'content' => "First; field\r\nSecond line"],
            ['title' => 'Last', 'content' => 'Fin'],
        ]);
})->with([
    'automatic encoding' => [null],
    'explicit Windows-1252' => ['Windows-1252'],
]);

it('does not start an import when the mapping or options form is invalid', function (array $data, array $errors): void {
    livewire(ImportActionSubmissionPage::class)
        ->mountAction('import')
        ->fillForm(['file' => UploadedFile::fake()->createWithContent('articles.csv', "Article headline,Article body\nFirst article,First body\n")])
        ->fillForm([
            'columnMap' => ['title' => 'Article headline', 'content' => 'Article body'],
            ...$data,
        ])
        ->callMountedAction()
        ->assertHasFormErrors($errors);

    Event::assertNotDispatched(ImportStarted::class);
    Bus::assertBatchCount(0);
    assertDatabaseCount('imports', 0);
    assertDatabaseCount('posts', 0);
})->with([
    'required mapping' => [['columnMap' => ['title' => null, 'content' => 'Article body']], ['columnMap.title' => 'required']],
    'invalid option' => [['mode' => 'invalid'], ['mode' => 'in']],
]);

it('does not dispatch an import when authorization is revoked before the server call', function (): void {
    livewire(ImportActionSubmissionPage::class)
        ->mountAction('import')
        ->fillForm(['file' => UploadedFile::fake()->createWithContent('articles.csv', "Article headline,Article body\nFirst article,First body\n")])
        ->fillForm(['columnMap' => ['title' => 'Article headline', 'content' => 'Article body']])
        ->set('canImport', false)
        ->call('callMountedAction');

    Event::assertNotDispatched(ImportStarted::class);
    Bus::assertBatchCount(0);
    assertDatabaseCount('imports', 0);
    assertDatabaseCount('posts', 0);
});

it('does not start an import when a required mapping is discarded by its selection cast', function (mixed $state): void {
    livewire(ImportActionSubmissionPage::class)
        ->mountAction('import')
        ->fillForm(['file' => UploadedFile::fake()->createWithContent('articles.csv', "Article headline,Article body\nFirst article,First body\n")])
        ->fillForm(['columnMap' => ['title' => 'Article headline', 'content' => 'Article body']])
        ->set('mountedActions.0.data.columnMap.title', $state)
        ->assertSet('mountedActions.0.data.columnMap.title', $state)
        ->call('callMountedAction')
        ->assertHasFormErrors(['columnMap.title' => ['prohibited']]);

    Event::assertNotDispatched(ImportStarted::class);
    Bus::assertBatchCount(0);
    assertDatabaseCount('imports', 0);
    assertDatabaseCount('posts', 0);
})->with([
    'array' => [['Article headline']],
    'false' => [false],
]);

class ImportActionSubmissionPage extends Actions
{
    public bool $canImport = true;

    public ?string $csvEncoding = null;

    public ?string $csvDelimiter = null;

    public ?int $headerOffset = null;

    public bool $shouldDetectCsvEncoding = false;

    protected function getHeaderActions(): array
    {
        return [
            ImportAction::make()
                ->importer(SubmissionPostImporter::class)
                ->csvEncoding(function (mixed $resource): ?string {
                    if (! $this->shouldDetectCsvEncoding) {
                        return $this->csvEncoding;
                    }

                    $sample = stream_get_contents($resource, 4096);

                    return mb_check_encoding($sample, 'UTF-8') ? null : 'Windows-1252';
                })
                ->csvDelimiter(fn (): ?string => $this->csvDelimiter)
                ->headerOffset(fn (): ?int => $this->headerOffset)
                ->options(['mode' => 'static', 'source' => 'catalog'])
                ->authorize(fn (): bool => $this->canImport),
        ];
    }
}

class SubmissionPostImporter extends Importer
{
    protected static ?string $model = Post::class;

    public static function getColumns(): array
    {
        return [
            ImportColumn::make('title')->guesses(['Intitulé', '件名'])->requiredMapping()->rules(['required']),
            ImportColumn::make('content')->guesses(['Résumé', '内容'])->requiredMapping()->rules(['required']),
        ];
    }

    public static function getOptionsFormComponents(): array
    {
        return [
            TextInput::make('mode')->default('append')->required()->in(['append', 'replace']),
            TextInput::make('region')->default('Europe')->required(),
        ];
    }

    public function resolveRecord(): ?Post
    {
        return app(Post::class);
    }

    public function getJobConnection(): ?string
    {
        return 'sync';
    }

    public static function getCompletedNotificationBody(Import $import): string
    {
        return 'Import completed';
    }
}
