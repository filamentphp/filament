<?php

use Filament\Tables\Columns\Summarizers\Average;
use Filament\Tables\Columns\Summarizers\Count;
use Filament\Tables\Columns\Summarizers\Range;
use Filament\Tables\Columns\Summarizers\Sum;
use Filament\Tables\Columns\Summarizers\Summarizer;
use Filament\Tables\Columns\Summarizers\Values;
use Filament\Tests\Fixtures\Models\Post;
use Filament\Tests\TestCase;
use Illuminate\Database\Query\Builder;
use Illuminate\Database\SqlServerConnection;

uses(TestCase::class);

describe('`Summarizer` base class', function (): void {
    it('can be constructed with `make()`', function (): void {
        $summarizer = Summarizer::make();

        expect($summarizer)->toBeInstanceOf(Summarizer::class);
    });

    it('returns `null` for `getId()` by default', function (): void {
        $summarizer = Summarizer::make();

        expect($summarizer->getId())->toBeNull();
    });

    it('can set `id()`', function (): void {
        $summarizer = Summarizer::make('custom-id');

        expect($summarizer->getId())->toBe('custom-id');
    });

    it('can set `id()` after construction', function (): void {
        $summarizer = Summarizer::make();
        $summarizer->id('post-hoc');

        expect($summarizer->getId())->toBe('post-hoc');
    });

    it('can set `using()` callback', function (): void {
        $summarizer = Summarizer::make()
            ->using(static fn () => 42);

        expect($summarizer)->toBeInstanceOf(Summarizer::class);
    });

    it('returns `null` from `getSelectedState()` by default', function (): void {
        $summarizer = Summarizer::make();

        expect($summarizer->getSelectedState())->toBeNull();
    });

    it('can set `selectedState()`', function (): void {
        $summarizer = Summarizer::make()
            ->selectedState(['key' => 'value']);

        expect($summarizer)->toBeInstanceOf(Summarizer::class);
    });

    it('returns `null` from base `summarize()`', function (): void {
        $summarizer = Summarizer::make();

        $query = Post::query()->toBase();

        expect($summarizer->summarize($query, 'rating'))->toBeNull();
    });

    it('returns empty array from base `getSelectStatements()`', function (): void {
        $summarizer = Summarizer::make();

        expect($summarizer->getSelectStatements('rating'))->toBe([]);
    });
});

describe('`Average` summarizer', function (): void {
    it('can be constructed with `make()`', function (): void {
        $average = Average::make();

        expect($average)->toBeInstanceOf(Average::class);
        expect($average)->toBeInstanceOf(Summarizer::class);
    });

    it('can set an ID', function (): void {
        $average = Average::make('avg-rating');

        expect($average->getId())->toBe('avg-rating');
    });

    it('rejects SQL Server `distinct()` combined with unions or `having()` in a summary query', function (string $method, bool | array $distinct): void {
        $connection = app(SqlServerConnection::class, [
            'pdo' => static fn () => throw new RuntimeException('No database connection is needed to reject this query.'),
        ]);
        $query = $connection->query()->from('amounts')
            ->beforeQuery(static fn (Builder $query): Builder => $query->distinct($distinct));

        if ($method === 'having') {
            $query->groupBy('amount')->having('amount', '>', 0);
        } else {
            $query->{$method}($connection->query()->from('other_amounts'));
        }

        expect(fn () => Average::make()->summarize($query, 'amount'))
            ->toThrow(LogicException::class, 'SQL Server average summaries do not support combining distinct()');
    })->with(['union', 'unionAll', 'having'])->with([[true], [['amount']]]);
});

describe('`Count` summarizer', function (): void {
    it('can be constructed with `make()`', function (): void {
        $count = Count::make();

        expect($count)->toBeInstanceOf(Count::class);
    });

    it('can set an ID', function (): void {
        $count = Count::make('row-count');

        expect($count->getId())->toBe('row-count');
    });
});

describe('`Range` summarizer', function (): void {
    it('can be constructed with `make()`', function (): void {
        $range = Range::make();

        expect($range)->toBeInstanceOf(Range::class);
    });

    it('defaults `shouldExcludeNull()` to `true`', function (): void {
        $range = Range::make();

        expect($range->shouldExcludeNull())->toBeTrue();
    });

    it('can set `excludeNull()` to `false`', function (): void {
        $range = Range::make()
            ->excludeNull(false);

        expect($range->shouldExcludeNull())->toBeFalse();
    });

    it('can set `excludeNull()` with a `Closure`', function (): void {
        $range = Range::make()
            ->excludeNull(static fn (): bool => false);

        expect($range->shouldExcludeNull())->toBeFalse();
    });
});

describe('`Sum` summarizer', function (): void {
    it('can be constructed with `make()`', function (): void {
        $sum = Sum::make();

        expect($sum)->toBeInstanceOf(Sum::class);
    });

    it('can set an ID', function (): void {
        $sum = Sum::make('total');

        expect($sum->getId())->toBe('total');
    });
});

describe('`Values` summarizer', function (): void {
    it('can be constructed with `make()`', function (): void {
        $values = Values::make();

        expect($values)->toBeInstanceOf(Values::class);
    });

    it('defaults `isBulleted()` to `true`', function (): void {
        $values = Values::make();

        expect($values->isBulleted())->toBeTrue();
    });

    it('can set `bulleted()` to `false`', function (): void {
        $values = Values::make()
            ->bulleted(false);

        expect($values->isBulleted())->toBeFalse();
    });

    it('can set `bulleted()` with a `Closure`', function (): void {
        $values = Values::make()
            ->bulleted(static fn (): bool => false);

        expect($values->isBulleted())->toBeFalse();
    });
});
