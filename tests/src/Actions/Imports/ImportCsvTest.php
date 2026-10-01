<?php

use Filament\Actions\Imports\ContentGenerators\CsvImportFailureContentGenerator;
use Filament\Actions\Imports\Exceptions\RowImportFailedException;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\ImportDispatcher;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Jobs\ImportCsv;
use Filament\Actions\Imports\Models\FailedImportRow;
use Filament\Actions\Imports\Models\Import;
use Filament\Tests\Actions\TestCase;
use Filament\Tests\Fixtures\Models\Post;
use Filament\Tests\Fixtures\Models\User;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Exceptions;
use League\Csv\Reader;
use League\Csv\Writer;

uses(TestCase::class);

beforeEach(function (): void {
    app()->bind(Authenticatable::class, User::class);
    Exceptions::fake();
    $this->import = Import::create([
        'user_id' => auth()->id(),
        'file_name' => 'posts.csv',
        'file_path' => 'posts.csv',
        'importer' => AccountingPostImporter::class,
        'total_rows' => 7,
        'processed_rows' => 2,
        'successful_rows' => 2,
    ]);
});

it('accounts for mixed rows and preserves only permitted original values in the failure CSV', function (bool $serialized, bool $masked): void {
    $rows = [
        ['Headline' => 'First', 'Credential' => 'secret-first', 'Notes' => 'ordinary-first'],
        ['Headline' => '', 'Credential' => 'secret-invalid', 'Notes' => 'ordinary-invalid'],
        ['Headline' => 'Rejected', 'Credential' => 'secret-rejected', 'Notes' => 'ordinary-rejected'],
        ['Headline' => 'Broken', 'Credential' => 'secret-broken', 'Notes' => 'ordinary-broken'],
        ['Headline' => 'Last', 'Credential' => 'secret-last', 'Notes' => 'ordinary-last'],
    ];
    $columnMap = ['title' => 'Headline', 'content' => 'Credential'];
    $options = ['mask' => $masked];

    if ($serialized) {
        Bus::fake();
        // The nested importer must not serialize the loaded user either.
        $this->import->user->setAttribute('binary', "loaded-user-marker\xFF");
        app(ImportDispatcher::class)->dispatch($this->import, [$rows], $columnMap, $options, ImportCsv::class, 'web');
        $job = Bus::batched(static fn (): bool => true)->sole()->jobs->sole();
        $payload = serialize($job);
        expect($payload)->not->toContain('loaded-user-marker');
        $job = unserialize($payload);
        $restoredImport = (fn (): Import => $this->import)->call($job);
        expect($restoredImport->relationLoaded('user'))->toBeFalse();
    } else {
        $job = new ImportCsv($this->import, $rows, $columnMap, $options);
    }

    auth()->setUser(User::factory()->create());
    $job->handle();

    expect($this->import->refresh()->processed_rows)->toBe(7)
        ->and($this->import->successful_rows)->toBe(4)
        ->and($this->import->getFailedRowsCount())->toBe(3)
        ->and(Post::query()->orderBy('id')->get(['title', 'content', 'author_id'])->toArray())->toBe([
            ['title' => 'First', 'content' => 'secret-first', 'author_id' => $this->import->user_id],
            ['title' => 'Last', 'content' => 'secret-last', 'author_id' => $this->import->user_id],
        ])
        ->and(auth()->guard()->hasUser())->toBeFalse();

    $expectedData = [
        ['Headline' => '', ...($masked ? [] : ['Credential' => 'secret-invalid']), 'Notes' => 'ordinary-invalid'],
        ['Headline' => 'Rejected', ...($masked ? [] : ['Credential' => 'secret-rejected']), 'Notes' => 'ordinary-rejected'],
        ['Headline' => 'Broken', ...($masked ? [] : ['Credential' => 'secret-broken']), 'Notes' => 'ordinary-broken'],
    ];
    $failedRows = $this->import->failedRows()->orderBy('id')->get();
    expect($failedRows->pluck('data')->all())->toBe($expectedData)
        ->and($failedRows->pluck('validation_error')->all())->toBe(['The title field is required.', 'Rejected by importer.', null]);
    Exceptions::assertReported(static fn (RuntimeException $exception): bool => $exception->getMessage() === 'Internal failure: secret-broken');

    $csv = Writer::from(new SplTempFileObject);
    app(CsvImportFailureContentGenerator::class)($this->import, $csv);
    $reader = Reader::fromString($csv->toString());
    $reader->setHeaderOffset(0);
    expect(array_values(iterator_to_array($reader->getRecords())))->toBe([
        [...$expectedData[0], 'error' => 'The title field is required.'],
        [...$expectedData[1], 'error' => 'Rejected by importer.'],
        [...$expectedData[2], 'error' => 'System error, please contact support.'],
    ]);
    if ($masked) {
        expect($csv->toString())->not->toContain('Credential', 'secret-');
    }
})->with(['array rows' => false, 'dispatched and rehydrated rows' => true])->with(['ordinary' => false, 'sensitive' => true]);

it('rolls back rows and counters on a persistence exception before retrying the original serialized import', function (): void {
    $payload = serialize(new ImportCsv($this->import, [
        ['title' => 'First', 'content' => 'secret-first'],
        ['title' => '', 'content' => 'secret-invalid'],
        ['title' => 'Last', 'content' => 'secret-last'],
    ], ['title' => 'title', 'content' => 'content'], ['mask' => true]));
    $event = 'eloquent.creating: ' . FailedImportRow::class;
    Event::listen($event, static fn () => throw new RuntimeException('Failed row storage unavailable.'));

    try {
        expect(fn () => unserialize($payload)->handle())->toThrow(RuntimeException::class, 'Failed row storage unavailable.');
        expect($this->import->refresh()->processed_rows)->toBe(2)
            ->and($this->import->successful_rows)->toBe(2)
            ->and($this->import->failedRows()->count())->toBe(0)
            ->and(Post::query()->count())->toBe(0)
            ->and(auth()->guard()->hasUser())->toBeFalse();
    } finally {
        Event::forget($event);
    }

    unserialize($payload)->handle();
    expect($this->import->refresh()->processed_rows)->toBe(5)
        ->and($this->import->successful_rows)->toBe(4)
        ->and($this->import->failedRows()->sole()->data)->toBe(['title' => ''])
        ->and(Post::query()->orderBy('id')->pluck('title')->all())->toBe(['First', 'Last']);
});

class AccountingPostImporter extends Importer
{
    protected static ?string $model = Post::class;

    public static function getColumns(): array
    {
        return [
            ImportColumn::make('title')->rules(['required']),
            ImportColumn::make('content')->sensitive(static fn (array $options): bool => $options['mask']),
        ];
    }

    public function resolveRecord(): ?Post
    {
        return new Post(['author_id' => auth()->id()]);
    }

    protected function beforeValidate(): void
    {
        if ($this->data['title'] === 'Rejected') {
            throw new RowImportFailedException('Rejected by importer.');
        }
        if ($this->data['title'] === 'Broken') {
            throw new RuntimeException('Internal failure: ' . $this->data['content']);
        }
    }

    public static function getCompletedNotificationBody(Import $import): string
    {
        return '';
    }
}
