<?php

use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;
use Filament\Actions\Testing\TestExporter;
use Filament\Tests\Fixtures\Models\User;
use Filament\Tests\TestCase;
use Illuminate\Database\Eloquent\Model;

uses(TestCase::class);

it('derives a default map from visible and default-enabled columns without binding an exporter', function (): void {
    $export = app(Export::class);
    $record = new User(['name' => 'Ada', 'email' => 'ada@example.com']);

    $row = TestExporter::make(RowTestExporter::class, export: $export)->export($record);

    expect($export->getColumnMap())->toBe(['name' => 'Identity', 'email' => 'Identity'])
        ->and($row)->toBe(['Ada', 'ada@example.com'])
        ->and($export->exists)->toBeFalse()
        ->and($record->exists)->toBeFalse();
});

it('honors an overridden `getVisibleColumns()`', function (): void {
    $exporter = new class(app(Export::class), [], []) extends RowTestExporter
    {
        public static function getVisibleColumns(): array
        {
            return [ExportColumn::make('email')];
        }
    };

    expect(TestExporter::make($exporter::class)->export(new User([
        'name' => 'Ada',
        'email' => 'ada@example.com',
    ])))->toBe(['ada@example.com']);
});

it('preserves explicit map order and duplicate labels including hidden and default-disabled columns', function (): void {
    $export = app(Export::class);
    $columnMap = ['password' => 'Value', 'email' => 'Value', 'name' => 'Renamed', 'id' => 'Identifier'];
    $record = new User(['name' => 'Ada', 'email' => 'ada@example.com']);
    $record->setRawAttributes([...$record->getAttributes(), 'password' => 'secret', 'id' => 42]);

    expect(TestExporter::make(RowTestExporter::class, $columnMap, export: $export)->export($record))
        ->toBe(['secret', 'ada@example.com', 'Ada', '42'])
        ->and($export->getColumnMap())->toBe($columnMap);
});

it('does not evaluate selection callbacks for an explicit map including `[]`', function (array $columnMap, array $expected): void {
    ExportColumn::configureUsing(static function (ExportColumn $column): void {
        $column
            ->visible(static fn (): bool => throw new LogicException('Visibility must not be evaluated'));
    }, during: function () use ($columnMap, $expected): void {
        $exporter = new class(app(Export::class), [], []) extends RowTestExporter
        {
            public static function getColumns(): array
            {
                return [ExportColumn::make('name')
                    ->enabledByDefault(static fn (): bool => throw new LogicException('Defaults must not be evaluated'))
                    ->label(static fn (): string => throw new LogicException('Labels must not be evaluated'))];
            }
        };

        expect(TestExporter::make($exporter::class, $columnMap)->export(new User(['name' => 'Ada'])))
            ->toBe($expected);
    });
})->with([
    'empty map' => [[], []],
    'selected column' => [['name' => 'Custom name'], ['Ada']],
]);

it('reuses the real exporter with each record and passes options to formatting callbacks', function (): void {
    $helper = TestExporter::make(RowTestExporter::class, ['name' => 'Name'], ['prefix' => 'Author: ']);

    expect($helper->export(new User(['name' => 'Ada'])))->toBe(['Author: Ada'])
        ->and($helper->export(new User(['name' => 'Grace'])))->toBe(['Author: Grace']);
});

it('resolves the default unsaved `Export` and wrapper from the container', function (): void {
    $export = app(Export::class);
    app()->bind(Export::class, static fn (): Export => $export);
    app()->bind(TestExporter::class, CustomRowTestHelper::class);

    $helper = TestExporter::make(RowTestExporter::class);

    expect($helper)->toBeInstanceOf(CustomRowTestHelper::class)
        ->and($export->exporter)->toBe(RowTestExporter::class)
        ->and($export->getColumnMap())->toBe(['name' => 'Identity', 'email' => 'Identity'])
        ->and($export->getOptions())->toBe([])
        ->and($export->exists)->toBeFalse()
        ->and($export->getKey())->toBeNull();
});

it('uses supplied export context through `getExporter()` and container bindings without switching authentication', function (): void {
    $authenticatedUser = new User(['name' => 'Authenticated']);
    $exportOwner = new User(['name' => 'Owner']);
    $this->actingAs($authenticatedUser);

    $export = new class extends Export
    {
        public int $resolutions = 0;

        public function getExporter(array $columnMap, array $options): Exporter
        {
            $this->resolutions++;

            return parent::getExporter($columnMap, $options);
        }
    };
    $export->exporter = 'AnOldExporter';
    $export->setRelation('user', $exportOwner);
    app()->bind(Export::class, static fn (): Export => throw new LogicException('A context was supplied'));
    app()->bind(RowTestExporter::class, ContextRowTestExporter::class);

    $record = new User(['name' => 'Ada']);
    $options = ['prefix' => 'Writer: ', 'nested' => ['enabled' => false]];
    $helper = ExportColumn::configureUsing(
        static function (ExportColumn $column) use ($authenticatedUser): void {
            $column->visible(static fn (): bool => auth()->user() === $authenticatedUser);
        },
        during: static fn (): TestExporter => TestExporter::make(RowTestExporter::class, null, $options, $export),
    );

    expect($helper->export($record))->toBe([$export, $options, $authenticatedUser, $record])
        ->and($export->resolutions)->toBe(1)
        ->and($export->exporter)->toBe(RowTestExporter::class)
        ->and($export->getColumnMap())->toBe(['name' => 'Identity', 'email' => 'Identity'])
        ->and($export->getOptions())->toBe($options)
        ->and($export->user)->toBe($exportOwner)
        ->and(auth()->user())->toBe($authenticatedUser)
        ->and($export->exists)->toBeFalse();
});

it('invokes an overridden `__invoke()` exactly once per record and returns its array unchanged', function (): void {
    $exporter = new class(app(Export::class), [], []) extends RowTestExporter
    {
        public int $calls = 0;

        public function __invoke(Model $record): array
        {
            $this->calls++;

            return [7 => $record, 'custom' => [false, null, 0, '0']];
        }
    };
    app()->bind(RowTestExporter::class, static fn (): Exporter => $exporter);
    $helper = TestExporter::make(RowTestExporter::class, []);
    $record = new User(['name' => 'Ada']);

    expect($helper->export($record))->toBe([7 => $record, 'custom' => [false, null, 0, '0']])
        ->and($exporter->calls)->toBe(1);
});

it('propagates the original throwable from row formatting', function (Throwable $exception): void {
    ExportColumn::configureUsing(static function (ExportColumn $column) use ($exception): void {
        $column->state(static fn () => throw $exception);
    }, during: function () use ($exception): void {
        $helper = TestExporter::make(RowTestExporter::class, ['email' => 'Email']);

        try {
            $helper->export(new User);
        } catch (Throwable $caughtException) {
            expect($caughtException)->toBe($exception);

            return;
        }

        $this->fail('The exporter throwable was swallowed.');
    });
})->with([
    'exception' => [new RuntimeException('Formatting failed')],
    'error' => [new TypeError('Invalid state')],
]);

it('propagates the original exception while resolving the exporter', function (): void {
    $exception = new RuntimeException('Exporter construction failed');
    app()->bind(RowTestExporter::class, static fn () => throw $exception);

    try {
        TestExporter::make(RowTestExporter::class);
    } catch (Throwable $caughtException) {
        expect($caughtException)->toBe($exception);

        return;
    }

    $this->fail('The exporter exception was swallowed.');
});

class RowTestExporter extends Exporter
{
    public static function getColumns(): array
    {
        return [
            ExportColumn::make('name')
                ->visible(static function (ExportColumn $column): bool {
                    expect($column->getExporter())->toBeNull();

                    return true;
                })
                ->enabledByDefault(static function (ExportColumn $column): bool {
                    expect($column->getExporter())->toBeNull();

                    return true;
                })
                ->label(static function (ExportColumn $column): string {
                    expect($column->getExporter())->toBeNull();

                    return 'Identity';
                })
                ->formatStateUsing(static fn (string $state, array $options): string => ($options['prefix'] ?? '') . $state),
            ExportColumn::make('password')->hidden()
                ->enabledByDefault(static fn (): bool => throw new LogicException('Hidden default evaluated')),
            ExportColumn::make('id')->enabledByDefault(false)
                ->label(static fn (): string => throw new LogicException('Disabled label evaluated')),
            ExportColumn::make('email')->label('Identity'),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        return '';
    }
}

class CustomRowTestHelper extends TestExporter {}

class ContextRowTestExporter extends RowTestExporter
{
    public function __invoke(Model $record): array
    {
        return [$this->export, $this->options, auth()->user(), $record];
    }
}
