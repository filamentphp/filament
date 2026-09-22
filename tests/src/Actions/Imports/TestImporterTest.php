<?php

use Filament\Actions\Imports\Exceptions\RowImportFailedException;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\Import;
use Filament\Actions\Testing\TestImporter;
use Filament\Tests\Fixtures\Models\User;
use Filament\Tests\TestCase;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\ValidationException;

uses(TestCase::class);

it('imports a row with identity mapping through casting, validation and save hooks', function (): void {
    $record = TestImporter::make(UserRowTestImporter::class)->import([
        'name' => '  Ada Lovelace  ',
        'email' => 'ada@example.com',
    ]);

    expect($record)->toBeInstanceOf(User::class)
        ->exists->toBeTrue();

    $this->assertDatabaseHas('users', [
        'id' => $record->getKey(),
        'name' => 'Ada Lovelace',
        'email' => 'ada@example.com',
        'password' => 'set-by-before-save',
    ]);
    $this->assertDatabaseCount('imports', 0);
});

it('updates a record using explicit headers without filling or validating an omitted column', function (): void {
    $user = User::factory()->create(['name' => 'Original name']);

    $record = TestImporter::make(UserRowTestImporter::class, columnMap: [
        'email' => 'Email address',
    ], options: ['updateExisting' => true])->import([
        'Email address' => $user->email,
        'name' => '',
    ]);

    expect($record->is($user))->toBeTrue();
    $this->assertDatabaseCount('users', 1);
    $this->assertDatabaseHas('users', ['id' => $user->getKey(), 'name' => 'Original name']);
});

it('keeps an explicit empty column map instead of applying identity mapping', function (): void {
    expect(fn () => TestImporter::make(UserRowTestImporter::class, columnMap: [])->import([
        'name' => 'Ada Lovelace',
        'email' => 'ada@example.com',
    ]))->toThrow(ValidationException::class);

    $this->assertDatabaseCount('users', 0);
});

it('propagates validation errors from the importer without saving a record', function (): void {
    try {
        TestImporter::make(UserRowTestImporter::class)->import([
            'name' => 'Ada Lovelace',
            'email' => 'not-an-email',
        ]);

        $this->fail('Expected a validation exception.');
    } catch (ValidationException $exception) {
        expect($exception->errors())->toHaveKey('email')->not->toHaveKey('name');
    }

    $this->assertDatabaseCount('users', 0);
    $this->assertDatabaseCount('failed_import_rows', 0);
});

it('propagates deliberate row failures and unexpected exceptions unchanged', function (Throwable $exception): void {
    expect(fn () => TestImporter::make(UserRowTestImporter::class, options: [
        'exception' => $exception,
    ])->import(['name' => 'Ada Lovelace', 'email' => 'ada@example.com']))->toThrow($exception);

    $this->assertDatabaseCount('users', 0);
})->with([
    'row failure' => [new RowImportFailedException('Cannot import this row.')],
    'unexpected failure' => [new RuntimeException('Unexpected failure.')],
]);

it('returns `null` for a skipped row without retaining the previous record', function (): void {
    $importer = TestImporter::make(UserRowTestImporter::class);

    expect($importer->import(['name' => 'Ada Lovelace', 'email' => 'ada@example.com']))->toBeInstanceOf(User::class);
    expect($importer->import(['skip' => true]))->toBeNull();

    $this->assertDatabaseCount('users', 1);
});

it('resolves the importer through the container with the supplied import, mapping and options without changing authentication', function (): void {
    $user = User::factory()->create();
    $this->actingAs($user);
    $import = app(Import::class);
    $import->setRelation('user', $user);
    $resolvedImporter = null;

    app()->bind(UserRowTestImporter::class, function ($application, array $parameters) use (&$resolvedImporter): UserRowTestImporter {
        return $resolvedImporter = new UserRowTestImporter(...$parameters);
    });

    TestImporter::make(UserRowTestImporter::class, options: ['updateExisting' => true], import: $import)
        ->import(['name' => 'Updated name', 'email' => $user->email]);

    expect($resolvedImporter->getImport())->toBe($import)
        ->and($import->importer)->toBe(UserRowTestImporter::class)
        ->and($import->getColumnMap())->toBe(['name' => 'name', 'email' => 'email'])
        ->and($import->getOptions())->toBe(['updateExisting' => true])
        ->and($resolvedImporter->getOptions())->toBe(['updateExisting' => true])
        ->and($resolvedImporter->getImport()->user)->toBe($user)
        ->and(auth()->user())->toBe($user);

    $this->assertDatabaseHas('users', ['id' => $user->getKey(), 'name' => 'Updated name']);
});

class UserRowTestImporter extends Importer
{
    public static function getColumns(): array
    {
        return [
            ImportColumn::make('name')
                ->requiredMappingForNewRecordsOnly()
                ->castStateUsing(static fn (?string $state): string => trim($state ?? ''))
                ->rules(['required']),
            ImportColumn::make('email')->rules(['required', 'email']),
        ];
    }

    public function resolveRecord(): ?Model
    {
        if ($this->data['skip'] ?? false) {
            return null;
        }

        if ($this->options['updateExisting'] ?? false) {
            return User::firstOrNew(['email' => $this->data['email']]);
        }

        return app(User::class);
    }

    protected function beforeSave(): void
    {
        if (isset($this->options['exception'])) {
            throw $this->options['exception'];
        }

        $this->record->password = 'set-by-before-save';
    }

    public static function getCompletedNotificationBody(Import $import): string
    {
        return 'Import completed';
    }
}
