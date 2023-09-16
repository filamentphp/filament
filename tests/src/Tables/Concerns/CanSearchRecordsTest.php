<?php

use Filament\Tests\Tables\TestCase;

use function PHPUnit\Framework\assertCount;

uses(TestCase::class);

it('can extract the search into words using whitespace', function () {
    $trait = new class
    {
        use \Filament\Tables\Concerns\CanSearchRecords {
            extractTableSearchWords as public;
        }
    };

    assertCount(1, $trait->extractTableSearchWords('test'));

    assertCount(2, $trait->extractTableSearchWords('testy test'));
    assertCount(2, $trait->extractTableSearchWords('testy   test'));
    assertCount(2, $trait->extractTableSearchWords("testy \t \n \r  test"));
    assertCount(3, $trait->extractTableSearchWords('testy   tasty   test'));
});

it('can trim the search query', function () {
    $trait = new class
    {
        use \Filament\Tables\Concerns\CanSearchRecords;
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
});
