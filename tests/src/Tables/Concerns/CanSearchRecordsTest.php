<?php

use Filament\Tables\Concerns\CanSearchRecords;
use Filament\Tests\Tables\TestCase;

use function PHPUnit\Framework\assertCount;

uses(TestCase::class);

it('can extract the search into words using whitespace', function (): void {
    $trait = new class
    {
        use CanSearchRecords {
            extractTableSearchWords as public;
        }
    };

    assertCount(1, $trait->extractTableSearchWords('test'));
    assertCount(2, $trait->extractTableSearchWords('testy test'));
    assertCount(2, $trait->extractTableSearchWords('testy   test'));
    assertCount(2, $trait->extractTableSearchWords("testy \t \n \r  test"));
    assertCount(3, $trait->extractTableSearchWords('testy   tasty   test'));

    // test count when string contains phrases
    assertCount(1, $trait->extractTableSearchWords('"phrase one"'));
    assertCount(3, $trait->extractTableSearchWords('"phrase one" test "number 2"'));
    // test word/phrase split content, *within double quotes multiple adjacent spaces are compressed to single space.
    $this->assertSame(
        ['test', 'phrase one'],
        $trait->extractTableSearchWords('test   "phrase    one"')
    );

    $this->assertSame(
        ['phrase one', 'test', 'number 2'],
        $trait->extractTableSearchWords('"phrase one"   test  "number   2"')
    );

    // an empty search string should return an empty array
    $this->assertSame([], $trait->extractTableSearchWords(''));
});

it('can trim the search query', function (): void {
    $trait = new class
    {
        use CanSearchRecords;
    };

    $trait->tableSearch = 'test';
    $this->assertSame('test', $trait->getTableSearch());

    $trait->tableSearch = '  test  ';
    $this->assertSame('test', $trait->getTableSearch());

    $trait->tableSearch = '';
    $this->assertSame(null, $trait->getTableSearch());

    $trait->tableSearch = '      ';
    $this->assertSame(null, $trait->getTableSearch());

    $trait->tableSearch = null;
    $this->assertNull($trait->getTableSearch());

    $trait->tableSearch = '  testy "test phrase" ';
    $this->assertSame('testy "test phrase"', $trait->getTableSearch());
});
