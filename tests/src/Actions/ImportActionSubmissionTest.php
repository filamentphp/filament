<?php

use Filament\Actions\ImportAction;
use Filament\Actions\Imports\Events\ImportStarted;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\Import;
use Filament\Forms\Components\TextInput;
use Filament\Tests\Actions\TestCase;
use Filament\Tests\Fixtures\Models\Post;
use Filament\Tests\Fixtures\Pages\Actions;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Event;

use function Filament\Tests\livewire;
use function Pest\Laravel\assertDatabaseCount;
use function Pest\Laravel\assertDatabaseHas;

uses(TestCase::class);

beforeEach(function (): void {
    Bus::fake();
    Event::fake([ImportStarted::class]);
});

it('submits a real `ImportAction` with CSV mapping and merged options without running the import', function (): void {
    $csv = "Article headline,Article body\nFirst article,First body\nSecond article,Second body\n";
    $user = auth()->user();

    livewire(ImportActionSubmissionPage::class)
        ->mountAction('import')
        ->setActionData(['file' => UploadedFile::fake()->createWithContent('articles.csv', $csv)])
        ->assertActionDataSet(['region' => 'Europe'])
        ->setActionData([
            'columnMap' => ['title' => 'Article headline', 'content' => 'Article body'],
            'mode' => 'replace',
        ])
        ->callMountedAction()
        ->assertHasNoActionErrors();

    Event::assertDispatched(ImportStarted::class, function (ImportStarted $event) use ($csv, $user): bool {
        $import = $event->getImport();

        expect($import->exists)->toBeTrue()
            ->and($import->importer)->toBe(SubmissionPostImporter::class)
            ->and($import->user->is($user))->toBeTrue()
            ->and($import->file_name)->toBe('articles.csv')
            ->and($import->total_rows)->toBe(2)
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

it('does not start an import when the mapping or options form is invalid', function (array $data, array $errors): void {
    livewire(ImportActionSubmissionPage::class)
        ->mountAction('import')
        ->setActionData(['file' => UploadedFile::fake()->createWithContent('articles.csv', "Article headline,Article body\nFirst article,First body\n")])
        ->setActionData([
            'columnMap' => ['title' => 'Article headline', 'content' => 'Article body'],
            ...$data,
        ])
        ->callMountedAction()
        ->assertHasActionErrors($errors);

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
        ->setActionData(['file' => UploadedFile::fake()->createWithContent('articles.csv', "Article headline,Article body\nFirst article,First body\n")])
        ->setActionData(['columnMap' => ['title' => 'Article headline', 'content' => 'Article body']])
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

    protected function getHeaderActions(): array
    {
        return [
            ImportAction::make()
                ->importer(SubmissionPostImporter::class)
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
            ImportColumn::make('title')->requiredMapping()->rules(['required']),
            ImportColumn::make('content')->requiredMapping()->rules(['required']),
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

    public static function getCompletedNotificationBody(Import $import): string
    {
        return 'Import completed';
    }
}
