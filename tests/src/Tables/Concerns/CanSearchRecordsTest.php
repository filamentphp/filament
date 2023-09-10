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
            searchWords as public;
        }
    };

    assertCount(1, $trait->searchWords('test'));
    assertCount(1, $trait->searchWords('  test  '));
    assertCount(1, $trait->searchWords(" \t test \n "));

    assertCount(2, $trait->searchWords('testy test'));
    assertCount(2, $trait->searchWords('  testy test  '));
    assertCount(2, $trait->searchWords('testy   test'));
    assertCount(2, $trait->searchWords("testy \t \n \r  test"));
    assertCount(2, $trait->searchWords(" \t testy test \n "));

    assertCount(3, $trait->searchWords("   \t testy  \t  tasty  \n  test \r   "));
});
