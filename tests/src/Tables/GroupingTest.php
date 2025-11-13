<?php

use Filament\Tables;
use Filament\Tests\Fixtures\Livewire\PostsTable;
use Filament\Tests\Fixtures\Models\Post;
use Filament\Tests\Fixtures\Models\Team;
use Filament\Tests\Fixtures\Models\User;
use Filament\Tests\Tables\TestCase;
use Livewire\Features\SupportTesting\Testable;

use function Filament\Tests\livewire;

uses(TestCase::class);

it('can group a table', function (): void {
    $posts = Post::factory()->count(20)->create();

    livewire(PostsTable::class)
        ->tap(function (Testable $testable): void {
            /** @var PostsTable $livewire */
            $livewire = $testable->instance();

            $table = $livewire->getTable();

            expect($table)
                ->getGrouping()->toBeNull();

            $groups = $table->getGroups();

            expect($groups['author.name'])
                ->getLabel()->toBe('Dynamic label');
        })
        ->set('tableGrouping', 'author.name')
        ->tap(function (Testable $testable): void {
            /** @var PostsTable $livewire */
            $livewire = $testable->instance();

            $table = $livewire->getTable();

            expect($table)
                ->getGrouping()->toBeInstanceOf(Tables\Grouping\Group::class)
                ->and($table->getGrouping())
                ->getLabel()->toBe('Dynamic label');
        });
});

it('can group records by column', function (): void {
    // Create posts with different titles to group by
    $posts = collect([
        Post::factory()->create(['title' => 'Apple Post']),
        Post::factory()->create(['title' => 'Banana Post']),
        Post::factory()->create(['title' => 'Apple Post']),
        Post::factory()->create(['title' => 'Cherry Post']),
        Post::factory()->create(['title' => 'Banana Post']),
    ]);

    livewire(PostsTable::class)
        ->set('tableGrouping', 'title')
        ->assertCanSeeTableRecords($posts->sortBy('title'), inOrder: true);
});

it('can group records by relationship', function (): void {
    // Create users with specific names to control order
    $userAlice = User::factory()->create(['name' => 'Alice']);
    $userBob = User::factory()->create(['name' => 'Bob']);
    $userCharlie = User::factory()->create(['name' => 'Charlie']);

    // Create posts with those authors
    $posts = collect([
        Post::factory()->create(['author_id' => $userBob->id]),
        Post::factory()->create(['author_id' => $userAlice->id]),
        Post::factory()->create(['author_id' => $userCharlie->id]),
        Post::factory()->create(['author_id' => $userAlice->id]),
        Post::factory()->create(['author_id' => $userBob->id]),
    ]);

    livewire(PostsTable::class)
        ->set('tableGrouping', 'author.name')
        ->assertCanSeeTableRecords($posts->sortBy('author.name'), inOrder: true);
});

it('can group records by nested relationship', function (): void {
    // Create teams with specific names to control order
    $teamAlpha = Team::factory()->create(['name' => 'Alpha Team']);
    $teamBeta = Team::factory()->create(['name' => 'Beta Team']);
    $teamGamma = Team::factory()->create(['name' => 'Gamma Team']);

    // Create users with teams
    $userWithAlpha = User::factory()->create(['team_id' => $teamAlpha->id]);
    $userWithBeta = User::factory()->create(['team_id' => $teamBeta->id]);
    $userWithGamma = User::factory()->create(['team_id' => $teamGamma->id]);

    // Create posts with those authors
    $posts = collect([
        Post::factory()->create(['author_id' => $userWithBeta->id]),
        Post::factory()->create(['author_id' => $userWithAlpha->id]),
        Post::factory()->create(['author_id' => $userWithGamma->id]),
        Post::factory()->create(['author_id' => $userWithAlpha->id]),
        Post::factory()->create(['author_id' => $userWithBeta->id]),
    ]);

    livewire(PostsTable::class)
        ->set('tableGrouping', 'author.team.name')
        ->assertCanSeeTableRecords($posts->sortBy('author.team.name'), inOrder: true);
});
