<?php

use Filament\Tests\Fixtures\Livewire\PostsTableWithEmptyRelationshipFilter;
use Filament\Tests\Fixtures\Livewire\PostsTableWithMultipleEmptyRelationshipFilter;
use Filament\Tests\Fixtures\Models\Post;
use Filament\Tests\Fixtures\Models\User;
use Filament\Tests\Tables\TestCase;

use function Filament\Tests\livewire;

uses(TestCase::class);

it('can filter records with no relationship using `hasEmptyRelationshipOption`', function (): void {
    $author = User::factory()->create();

    $postsWithAuthor = Post::factory()->count(3)->create(['author_id' => $author->getKey()]);
    $postsWithoutAuthor = Post::factory()->count(2)->create(['author_id' => null]);

    livewire(PostsTableWithEmptyRelationshipFilter::class)
        ->assertCanSeeTableRecords($postsWithAuthor->merge($postsWithoutAuthor))
        ->filterTable('author', '__empty')
        ->assertCanSeeTableRecords($postsWithoutAuthor)
        ->assertCanNotSeeTableRecords($postsWithAuthor);
});

it('can filter records by specific relationship value using `hasEmptyRelationshipOption`', function (): void {
    $author1 = User::factory()->create();
    $author2 = User::factory()->create();

    $postsWithAuthor1 = Post::factory()->count(3)->create(['author_id' => $author1->getKey()]);
    $postsWithAuthor2 = Post::factory()->count(2)->create(['author_id' => $author2->getKey()]);
    $postsWithoutAuthor = Post::factory()->count(2)->create(['author_id' => null]);

    livewire(PostsTableWithEmptyRelationshipFilter::class)
        ->assertCanSeeTableRecords($postsWithAuthor1->merge($postsWithAuthor2)->merge($postsWithoutAuthor))
        ->filterTable('author', $author1->getKey())
        ->assertCanSeeTableRecords($postsWithAuthor1)
        ->assertCanNotSeeTableRecords($postsWithAuthor2)
        ->assertCanNotSeeTableRecords($postsWithoutAuthor);
});

it('can filter records with no relationship using `hasEmptyRelationshipOption` with `multiple()`', function (): void {
    $author = User::factory()->create();

    $postsWithAuthor = Post::factory()->count(3)->create(['author_id' => $author->getKey()]);
    $postsWithoutAuthor = Post::factory()->count(2)->create(['author_id' => null]);

    livewire(PostsTableWithMultipleEmptyRelationshipFilter::class)
        ->assertCanSeeTableRecords($postsWithAuthor->merge($postsWithoutAuthor))
        ->filterTable('author', ['__empty'])
        ->assertCanSeeTableRecords($postsWithoutAuthor)
        ->assertCanNotSeeTableRecords($postsWithAuthor);
});

it('can filter records by specific relationship values using `hasEmptyRelationshipOption` with `multiple()`', function (): void {
    $author1 = User::factory()->create();
    $author2 = User::factory()->create();
    $author3 = User::factory()->create();

    $postsWithAuthor1 = Post::factory()->count(2)->create(['author_id' => $author1->getKey()]);
    $postsWithAuthor2 = Post::factory()->count(2)->create(['author_id' => $author2->getKey()]);
    $postsWithAuthor3 = Post::factory()->count(2)->create(['author_id' => $author3->getKey()]);
    $postsWithoutAuthor = Post::factory()->count(2)->create(['author_id' => null]);

    livewire(PostsTableWithMultipleEmptyRelationshipFilter::class)
        ->assertCanSeeTableRecords($postsWithAuthor1->merge($postsWithAuthor2)->merge($postsWithAuthor3)->merge($postsWithoutAuthor))
        ->filterTable('author', [$author1->getKey(), $author2->getKey()])
        ->assertCanSeeTableRecords($postsWithAuthor1->merge($postsWithAuthor2))
        ->assertCanNotSeeTableRecords($postsWithAuthor3)
        ->assertCanNotSeeTableRecords($postsWithoutAuthor);
});

it('can filter records by relationship values combined with empty option using `hasEmptyRelationshipOption` with `multiple()`', function (): void {
    $author1 = User::factory()->create();
    $author2 = User::factory()->create();

    $postsWithAuthor1 = Post::factory()->count(2)->create(['author_id' => $author1->getKey()]);
    $postsWithAuthor2 = Post::factory()->count(2)->create(['author_id' => $author2->getKey()]);
    $postsWithoutAuthor = Post::factory()->count(2)->create(['author_id' => null]);

    livewire(PostsTableWithMultipleEmptyRelationshipFilter::class)
        ->assertCanSeeTableRecords($postsWithAuthor1->merge($postsWithAuthor2)->merge($postsWithoutAuthor))
        ->filterTable('author', ['__empty', $author1->getKey()])
        ->assertCanSeeTableRecords($postsWithAuthor1->merge($postsWithoutAuthor))
        ->assertCanNotSeeTableRecords($postsWithAuthor2);
});
