<?php

use Filament\Tests\Tables\TestCase;

use function PHPUnit\Framework\assertCount;

uses(TestCase::class);

/**
 * The user-entered search string for a table is split into words
 * on any runs of whitespace.
 */

it('can split on whitespace', function () {
    $trait = new class {
        use \Filament\Tables\Concerns\CanSearchRecords {
            extractTableSearchWords as public;
        }
    };

    assertCount(1, $trait->extractTableSearchWords('test'));

    assertCount(2, $trait->extractTableSearchWords('testy test'));
    assertCount(2, $trait->extractTableSearchWords('testy   test'));
    assertCount(2, $trait->extractTableSearchWords("testy \t \n \r  test"));

    // Leading and trailing whitespace are extracted as empty words.
    assertCount(3, $trait->extractTableSearchWords('  test  '));
    assertCount(3, $trait->extractTableSearchWords(" \t test \n "));
    assertCount(4, $trait->extractTableSearchWords('  testy test  '));
    assertCount(4, $trait->extractTableSearchWords(" \t testy test \n "));
    assertCount(5, $trait->extractTableSearchWords("   \t testy  \t  tasty  \n  test \r   "));

    $this->assertSame(['', 'test', ''], $trait->extractTableSearchWords('   test   '));
});

it('can trim a search query', function () {
    $trait = new class {
        use \Filament\Tables\Concerns\CanSearchRecords;
    };

    $trait->tableSearch = 'test';
    $this->assertSame('test', $trait->getTableSearch());

    $trait->tableSearch = '  test  ';
    $this->assertSame('test', $trait->getTableSearch());

    $trait->tableSearch = null;
    $this->assertNull($trait->getTableSearch());
});
