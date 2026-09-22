<?php

use Filament\Actions\Imports\Exceptions\RowImportFailedException;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\Import;
use Filament\Actions\Testing\TestImporter;
use Filament\Tests\Fixtures\Models\User;
use Filament\Tests\TestCase;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\ValidationException;
use PHPUnit\Framework\AssertionFailedError;

uses(TestCase::class);

it('imports a row with identity mapping through casting, validation and save hooks', function (): void {
    $record = TestImporter::make(UserRowTestImporter::class)->import([
        'name' => '  Ada Lovelace  ',
        'email' => 'ada@example.com',
    ])->assertHasNoErrors()->getRecord();

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
    ])->assertHasNoErrors()->getRecord();

    expect($record->is($user))->toBeTrue();
    $this->assertDatabaseCount('users', 1);
    $this->assertDatabaseHas('users', ['id' => $user->getKey(), 'name' => 'Original name']);
});

it('keeps an explicit empty column map instead of applying identity mapping', function (): void {
    TestImporter::make(UserRowTestImporter::class, columnMap: [])->import([
        'name' => 'Ada Lovelace',
        'email' => 'ada@example.com',
    ])->assertHasErrors(['name' => 'required']);

    $this->assertDatabaseCount('users', 0);
});

it('captures validation errors from the importer without saving a record', function (): void {
    $importer = TestImporter::make(UserRowTestImporter::class);
    $importer->import(['name' => '', 'email' => 'x'])->assertHasErrors(['name', 'email']);

    $importer->import([
        'name' => 'Ada Lovelace',
        'email' => 'not-an-email',
    ])->assertHasErrors(['email' => 'email'])
        ->assertHasNoErrors(['name'])
        ->assertHasNoErrors(['email' => 'min']);

    expect($importer->getRecord())->toBeInstanceOf(User::class)
        ->exists->toBeFalse();

    $this->assertDatabaseCount('users', 0);
    $this->assertDatabaseCount('failed_import_rows', 0);
});

it('asserts validation field and rule subsets without rejecting additional errors', function (): void {
    TestImporter::make(UserRowTestImporter::class)->import([
        'name' => '',
        'email' => 'x',
    ])->assertHasErrors()
        ->assertHasErrors(['name', 'email'])
        ->assertHasErrors(['name' => 'required'])
        ->assertHasErrors(['email' => ['email', 'min:6']])
        ->assertHasNoErrors(['missing'])
        ->assertHasNoErrors(['email' => 'required']);
});

it('rejects incorrect validation assertions with useful diagnostics', function (string $method, array $keys, string $message): void {
    $importer = TestImporter::make(UserRowTestImporter::class)->import([
        'name' => 'Ada Lovelace',
        'email' => 'x',
    ]);

    expect(fn () => $importer->{$method}($keys))->toThrow(AssertionFailedError::class, $message);
})->with([
    'wrong rule on invalid field' => ['assertHasErrors', ['email' => 'required'], 'no matching failed rule'],
    'valid field' => ['assertHasErrors', ['name'], 'missing error: name'],
    'missing field' => ['assertHasErrors', ['missing' => 'required'], 'no matching failed rule'],
    'partially matching rules' => ['assertHasErrors', ['email' => ['email', 'required']], 'no matching failed rule'],
    'global no errors' => ['assertHasNoErrors', [], 'Component has errors:'],
    'field no errors' => ['assertHasNoErrors', ['email'], 'Component has error: email'],
    'rule no errors' => ['assertHasNoErrors', ['email' => 'min:6'], 'Component has [min] errors'],
]);

it('clears captured validation errors and failed rules when reused for successful or skipped rows', function (array $data): void {
    $importer = TestImporter::make(UserRowTestImporter::class);
    $importer->import(['name' => '', 'email' => 'x'])->assertHasErrors(['name', 'email']);

    expect($importer->import($data))->toBe($importer);
    $importer->assertHasNoErrors()->assertHasNoErrors(['email' => 'min']);
    expect($importer->failedRules())->toBe([]);
    expect(fn () => $importer->assertHasErrors())->toThrow(AssertionFailedError::class, 'Component has no errors.');
})->with([
    'success' => [['name' => 'Ada Lovelace', 'email' => 'ada@example.com']],
    'skipped' => [['skip' => true]],
]);

it('captures a `ValidationException` raised in a lifecycle hook', function (): void {
    $exception = ValidationException::withMessages(['custom' => 'Rejected by the hook.']);

    TestImporter::make(UserRowTestImporter::class, options: ['exception' => $exception])
        ->import(['name' => 'Ada Lovelace', 'email' => 'ada@example.com'])
        ->assertHasErrors(['custom' => 'Rejected by the hook.']);

    $this->assertDatabaseCount('users', 0);
});

it('propagates deliberate row failures and unexpected exceptions unchanged', function (Throwable $exception): void {
    $importer = TestImporter::make(UserRowTestImporter::class, options: [
        'exception' => $exception,
    ]);
    $importer->import(['name' => '', 'email' => 'x'])->assertHasErrors();

    try {
        $importer->import(['name' => 'Ada Lovelace', 'email' => 'ada@example.com']);
        $this->fail('Expected the importer exception.');
    } catch (Throwable $caughtException) {
        expect($caughtException)->toBe($exception);
    }

    $importer->assertHasNoErrors();
    expect($importer->failedRules())->toBe([]);

    $this->assertDatabaseCount('users', 0);
})->with([
    'row failure' => [new RowImportFailedException('Cannot import this row.')],
    'unexpected failure' => [new RuntimeException('Unexpected failure.')],
]);

it('returns `null` from `getRecord()` for a skipped row without retaining the previous record', function (): void {
    $importer = TestImporter::make(UserRowTestImporter::class);

    expect($importer->import(['name' => 'Ada Lovelace', 'email' => 'ada@example.com'])->assertHasNoErrors()->getRecord())->toBeInstanceOf(User::class);
    expect($importer->import(['skip' => true])->assertHasNoErrors()->getRecord())->toBeNull();

    $this->assertDatabaseCount('users', 1);
});

it('resolves the importer through the container with the supplied import, mapping and options without changing authentication', function (): void {
    $authenticatedUser = User::factory()->create();
    $importUser = User::factory()->create();
    $this->actingAs($authenticatedUser);
    $import = app(Import::class);
    $import->setRelation('user', $importUser);
    $resolvedImporter = null;

    app()->bind(UserRowTestImporter::class, function ($application, array $parameters) use (&$resolvedImporter): UserRowTestImporter {
        return $resolvedImporter = new UserRowTestImporter(...$parameters);
    });

    TestImporter::make(UserRowTestImporter::class, options: ['updateExisting' => true], import: $import)
        ->import(['name' => 'Updated name', 'email' => $importUser->email]);

    expect($resolvedImporter->getImport())->toBe($import)
        ->and($import->importer)->toBe(UserRowTestImporter::class)
        ->and($import->getColumnMap())->toBe(['name' => 'name', 'email' => 'email'])
        ->and($import->getOptions())->toBe(['updateExisting' => true])
        ->and($resolvedImporter->getOptions())->toBe(['updateExisting' => true])
        ->and($resolvedImporter->getImport()->user)->toBe($importUser)
        ->and($resolvedImporter->authenticatedUserBeforeSave)->toBe($authenticatedUser)
        ->and(auth()->user())->toBe($authenticatedUser);

    $this->assertDatabaseHas('users', ['id' => $importUser->getKey(), 'name' => 'Updated name']);
});

it('does not associate the authenticated user with the default import context', function (): void {
    $authenticatedUser = User::factory()->create();
    $this->actingAs($authenticatedUser);
    $resolvedImporter = null;

    app()->bind(UserRowTestImporter::class, function ($application, array $parameters) use (&$resolvedImporter): UserRowTestImporter {
        return $resolvedImporter = new UserRowTestImporter(...$parameters);
    });

    TestImporter::make(UserRowTestImporter::class)->import([
        'name' => 'Ada Lovelace',
        'email' => 'ada@example.com',
    ]);

    expect($resolvedImporter->getImport()->exists)->toBeFalse()
        ->and($resolvedImporter->getImport()->user_id)->toBeNull()
        ->and($resolvedImporter->getImport()->user)->toBeNull()
        ->and($resolvedImporter->authenticatedUserBeforeSave)->toBe($authenticatedUser)
        ->and(auth()->user())->toBe($authenticatedUser);
});

class UserRowTestImporter extends Importer
{
    public ?Authenticatable $authenticatedUserBeforeSave = null;

    public static function getColumns(): array
    {
        return [
            ImportColumn::make('name')
                ->requiredMappingForNewRecordsOnly()
                ->castStateUsing(static fn (?string $state): string => trim($state ?? ''))
                ->rules(['required']),
            ImportColumn::make('email')->rules(['required', 'email', 'min:6']),
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
        $this->authenticatedUserBeforeSave = auth()->user();

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
