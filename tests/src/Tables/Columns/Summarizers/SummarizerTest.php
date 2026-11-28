<?php

use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\Summarizers\Average;
use Filament\Tables\Columns\Summarizers\Count;
use Filament\Tables\Columns\Summarizers\Range;
use Filament\Tables\Columns\Summarizers\Sum;
use Filament\Tables\Columns\Summarizers\Summarizer;
use Filament\Tables\Columns\Summarizers\Values;
use Filament\Tables\Columns\TextColumn;
use Filament\Tests\Fixtures\Livewire\PostsTable;
use Filament\Tests\Fixtures\Models\Post;
use Filament\Tests\TestCase;
use Illuminate\Database\Query\Builder;
use Illuminate\Database\SqlServerConnection;
use Illuminate\Support\Facades\DB;

use function Filament\Tests\livewire;

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

    it('preserves the selected rows in ordered queries with limits and offsets', function (): void {
        foreach ([2, 7, 19] as $rating) {
            Post::factory()->create(['rating' => $rating]);
        }

        $summarizer = Sum::make();
        TextColumn::make('rating')->summarize($summarizer);

        expect($summarizer->query(Post::query()->orderByDesc('rating'))->getState())->toEqual(28)
            ->and($summarizer->query(Post::query()->orderByDesc('rating')->limit(1))->getState())->toEqual(19)
            ->and($summarizer->query(Post::query()->orderByDesc('rating')->offset(1)->limit(1))->getState())->toEqual(7)
            ->and($summarizer->query(Post::query()->withGlobalScope('top-rating', static fn ($query) => $query->orderByDesc('rating')->limit(1)))->getState())->toEqual(19);
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

    it('omits iconless entries from `icons()` without changing valid counts and colors', function (string $iconType): void {
        Post::factory()->count(2)->create(['rating' => 1]);
        Post::factory()->count(3)->create(['rating' => 2]);
        Post::factory()->count(4)->create(['rating' => 3]);

        $icon = match ($iconType) {
            'string' => 'heroicon-o-check-circle',
            'enum' => Heroicon::OutlinedCheckCircle,
            'svg' => svg('heroicon-o-check-circle'),
        };
        $column = IconColumn::make('rating')
            ->table(livewire(PostsTable::class)->instance()->getTable())
            ->icon(static fn (int $state) => ($state === 1) ? null : $icon)
            ->color(static fn (int $state): string => ($state === 3) ? 'warning' : 'success');
        $count = Count::make()
            ->icons()
            ->column($column)
            ->query(Post::query()->orderBy('rating'));

        expect($count->getState())->toBe([
            '"success"' => [serialize(null) => 2, serialize($icon) => 3],
            '"warning"' => [serialize($icon) => 4],
        ]);

        $document = new DOMDocument;
        $document->loadHTML($count->toEmbeddedHtml(), LIBXML_NOERROR | LIBXML_NOWARNING);
        $xpath = new DOMXPath($document);
        $items = $xpath->query('//li');

        expect($items)->toHaveCount(2)
            ->and(trim($items->item(0)->textContent))->toBe('3')
            ->and(trim($items->item(1)->textContent))->toBe('4')
            ->and($xpath->query('.//svg', $items->item(0)))->toHaveCount(1)
            ->and($xpath->query('.//svg', $items->item(1)))->toHaveCount(1)
            ->and($xpath->query('.//*[contains(@class, "fi-color-success")]', $items->item(0)))->toHaveCount(1)
            ->and($xpath->query('.//*[contains(@class, "fi-color-warning")]', $items->item(1)))->toHaveCount(1);
    })->with(['string', 'enum', 'svg']);

    it('can render `icons()` when all entries are iconless', function (string | bool | null $icon): void {
        Post::factory()->count(2)->create(['rating' => 1]);
        Post::factory()->count(3)->create(['rating' => 2]);

        $column = IconColumn::make('rating')
            ->table(livewire(PostsTable::class)->instance()->getTable())
            ->icon(static fn () => $icon);
        $count = Count::make()
            ->icons()
            ->column($column)
            ->query(Post::query());

        expect($count->getState())->toBe(['null' => [serialize(null) => 5]]);

        $document = new DOMDocument;
        $document->loadHTML($count->toEmbeddedHtml());

        expect($document->getElementsByTagName('li'))->toHaveCount(0)
            ->and($document->getElementsByTagName('svg'))->toHaveCount(0)
            ->and(trim($document->textContent))->toBe('');
    })->with([null, false, '', ' ']);
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

    it('can use `minimalTextualDifference()` to shorten the range', function (): void {
        $range = Range::make()
            ->minimalTextualDifference();

        expect($range->formatState(['apple', 'apricot']))->toBe(['app', 'apr'])
            ->and($range->formatState(['app', 'apple']))->toBe(['app', 'appl']);
    });

    it('can use `minimalTextualDifference()` with multibyte characters', function (): void {
        $range = Range::make()
            ->minimalTextualDifference();

        expect($range->formatState(['Jose', 'José']))->toBe(['Jose', 'José'])
            ->and($range->formatState(['ab', 'abé']))->toBe(['ab', 'abé'])
            ->and($range->formatState(['Аня', 'Ася']))->toBe(['Ан', 'Ас']);
    });

    it('formats the minimal textual difference between values', function (array $state, ?array $expected): void {
        expect(Range::make()->minimalTextualDifference()->formatState($state))->toBe($expected);
    })->with([
        'different initials' => [['apple', 'banana'], ['a', 'b']],
        'common prefix' => [['abcd', 'abef'], ['abc', 'abe']],
        'shorter first' => [['abc', 'abcdef'], ['abc', 'abcd']],
        'longer first' => [['abcdef', 'abc'], ['abcd', 'abc']],
        'case only' => [['AbC', 'aBc'], ['AbC', 'aBc']],
        'case before difference' => [['ABcx', 'abcy'], ['ABcx', 'abcy']],
        'trimmed comparison' => [[" abc \n", 'abd '], ['abc', 'abd']],
        'identical values' => [['abc', 'abc'], ['abc']],
        'identical trimmed values' => [[' abc ', 'abc'], [' abc ']],
        'empty first' => [['', 'value'], [1 => 'value']],
        'empty second' => [['value', ''], ['value']],
        'both blank' => [[' ', null], null],
        'multibyte difference' => [['éx', 'êy'], ['é', 'ê']],
        'multibyte case' => [['Éx', 'éy'], ['Éx', 'éy']],
        'emoji difference' => [['😀x', '😁y'], ['😀', '😁']],
        'multibyte extension' => [['a', 'aé'], ['a', 'aé']],
        'multibyte extension of first value' => [['aé', 'a'], ['aé', 'a']],
    ]);

    it('formats long common prefixes with `minimalTextualDifference()`', function (string $character): void {
        $prefix = str_repeat($character, 4096);

        expect(Range::make()->minimalTextualDifference()->formatState([$prefix . 'x', $prefix . 'y']))
            ->toBe([$prefix . 'x', $prefix . 'y']);
    })->with([
        'ASCII' => 'a',
        'Unicode' => 'é',
    ]);

    it('keeps `limit()` and `minimalTextualDifference()` as replacement formatters', function (): void {
        expect(Range::make()->minimalTextualDifference()->limit(3)->formatState(['abcdef', 'abcxyz']))
            ->toBe(['abc...', 'abc...'])
            ->and(Range::make()->limit(3)->minimalTextualDifference()->formatState(['abcdef', 'abcxyz']))
            ->toBe(['abcd', 'abcx'])
            ->and(Range::make()->limit(2, null)->formatState(['ééé', '界界']))
            ->toBe(['éé', '界']);
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

    it('preserves existing ordered `Values` on SQLite', function (): void {
        if (DB::connection()->getDriverName() !== 'sqlite') {
            $this->markTestSkipped('This preserves existing SQLite ordering, not a portable derived-table ordering guarantee.');
        }

        foreach ([2, 7, 19] as $rating) {
            Post::factory()->create(['rating' => $rating]);
        }

        $summarizer = Values::make();
        TextColumn::make('rating')->summarize($summarizer);

        expect($summarizer->query(Post::query()->orderByDesc('rating'))->getState())->toBe([19, 7, 2]);
    });
});
