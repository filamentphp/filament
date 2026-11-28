<?php

use Filament\Tables\Concerns\CanSearchRecords;
use Filament\Tests\Tables\TestCase;

use function PHPUnit\Framework\assertCount;

uses(TestCase::class);

it('can use `extractTableSearchWords()` for whitespace and quoted phrases', function (): void {
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
    assertCount(2, $trait->extractTableSearchWords("testy 　 \u{3164} \u{1160}  test"));
    assertCount(3, $trait->extractTableSearchWords('testy   tasty   test'));

    assertCount(1, $trait->extractTableSearchWords('"phrase one"'));
    assertCount(3, $trait->extractTableSearchWords('"phrase one" test "number 2"'));

    $this->assertSame(
        ['test', 'phrase one'],
        $trait->extractTableSearchWords('test   "phrase    one"')
    );

    $this->assertSame(
        ['phrase one', 'test', 'number 2'],
        $trait->extractTableSearchWords('"phrase one"   test  "number   2"')
    );

    $this->assertSame([], $trait->extractTableSearchWords(''));
});

it('bounds `extractTableSearchWords()` before splitting without breaking multibyte characters', function (string $search, array $expectedWords): void {
    $trait = new class
    {
        use CanSearchRecords {
            extractTableSearchWords as public;
        }
    };

    expect($trait->extractTableSearchWords($search))->toBe($expectedWords);
})->with([
    'below the character limit' => [str_repeat('猫', 999), [str_repeat('猫', 999)]],
    'at the character limit' => [str_repeat('猫', 1000), [str_repeat('猫', 1000)]],
    'above the character limit' => [str_repeat('猫', 1000) . '犬', [str_repeat('猫', 1000)]],
    '50 words' => [str_repeat('a ', 49) . '0', [...array_fill(0, 49, 'a'), '0']],
    '51 words' => [str_repeat('a ', 50) . '0', [str_repeat('a ', 50) . '0']],
    '50 quoted phrases' => [str_repeat('"猫 犬" ', 50), array_fill(0, 50, '猫 犬')],
    '51 quoted phrases' => [str_repeat('"猫 犬" ', 51), [str_repeat('"猫 犬" ', 51)]],
    'empty terms do not count' => [str_repeat('"" ', 50) . str_repeat('a ', 50), array_fill(50, 50, 'a')],
    'many words' => [str_repeat('a ', 2000), [str_repeat('a ', 500)]],
    'multibyte separators' => [str_repeat("猫\u{3164}", 2000), [str_repeat("猫\u{3164}", 500)]],
    'quoted phrase at the boundary' => [str_repeat('猫', 994) . ' "犬 鳥" extra', [str_repeat('猫', 994), '犬 鳥']],
]);

it('can use `getTableSearch()` to trim whitespace and normalize blank searches', function (): void {
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

    $trait->tableSearch = '　　　test　　　';
    $this->assertSame('test', $trait->getTableSearch());

    $trait->tableSearch = "\u{3164}\u{3164}test\u{3164}\u{3164}";
    $this->assertSame('test', $trait->getTableSearch());

    $trait->tableSearch = "\u{1160}\u{1160}test\u{1160}\u{1160}";
    $this->assertSame('test', $trait->getTableSearch());
});
