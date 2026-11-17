<?php

use Filament\Actions\ImportAction;
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

class ImportActionSubmissionPage extends Actions
{
    public bool $canImport = true;

    public ?string $csvEncoding = null;

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
