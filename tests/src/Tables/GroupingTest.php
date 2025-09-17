<?php

use Filament\Tables;
use Filament\Tests\Fixtures\Livewire\PostsTable;
use Filament\Tests\Fixtures\Models\Post;
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
