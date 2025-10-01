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

it('can detect custom search queries', function (): void {
    $trait = new class
    {
        use CanSearchRecords;
    };

    $this->assertFalse(property_exists($trait, 'searchQuery') && $trait->searchQuery !== null);
    
    $trait->searchQuery = fn($query, $search) => $query->where('custom', 'like', "%{$search}%");
    $this->assertTrue($trait->searchQuery !== null);
});

it('can handle empty search terms', function (): void {
    $trait = new class
    {
        use CanSearchRecords {
            extractTableSearchWords as public;
        }
    };

    $this->assertSame([], $trait->extractTableSearchWords(''));
    $this->assertSame([], $trait->extractTableSearchWords('   '));
    $this->assertSame([], $trait->extractTableSearchWords("\t\n\r"));
});

// Integration tests using Livewire components
use Filament\Tests\Fixtures\Models\Post;
use Filament\Tests\Fixtures\Livewire\PostsTable;

use function Filament\Tests\livewire;

it('can search records with custom search queries', function () {
    $posts = Post::factory()->count(5)->create();
    
    $searchablePost = $posts->first();
    
    livewire(PostsTable::class)
        ->searchTable($searchablePost->title)
        ->assertCanSeeTableRecords($posts->where('title', $searchablePost->title))
        ->assertCanNotSeeTableRecords($posts->where('title', '!=', $searchablePost->title));
});

it('can search individual columns', function () {
    $posts = Post::factory()->count(5)->create();
    
    $searchablePost = $posts->first();
    
    livewire(PostsTable::class)
        ->searchTableColumns(['content' => $searchablePost->content])
        ->assertCanSeeTableRecords($posts->where('content', $searchablePost->content))
        ->assertCanNotSeeTableRecords($posts->where('content', '!=', $searchablePost->content));
});

it('can search multiple individual columns', function () {
    $posts = Post::factory()->count(5)->create();
    
    $searchablePost = $posts->first();
    
    livewire(PostsTable::class)
        ->searchTableColumns([
            'content' => $searchablePost->content,
            'author.email' => $searchablePost->author->email,
        ])
        ->assertCanSeeTableRecords($posts->where('author.email', $searchablePost->author->email))
        ->assertCanNotSeeTableRecords($posts->where('author.email', '!=', $searchablePost->author->email));
});

it('can search with relationships', function () {
    $posts = Post::factory()->count(5)->create();
    
    $searchablePost = $posts->first();
    
    livewire(PostsTable::class)
        ->searchTable($searchablePost->author->name)
        ->assertCanSeeTableRecords($posts->where('author.name', $searchablePost->author->name))
        ->assertCanNotSeeTableRecords($posts->where('author.name', '!=', $searchablePost->author->name));
});

it('can handle multi-word searches', function () {
    $posts = Post::factory()->count(5)->create([
        'title' => 'Multi Word Title',
        'content' => 'Some content with multiple words',
    ]);
    
    livewire(PostsTable::class)
        ->searchTable('Multi Word')
        ->assertCanSeeTableRecords($posts->where('title', 'Multi Word Title'));
});
