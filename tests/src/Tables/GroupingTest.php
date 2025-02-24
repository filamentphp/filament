<?php

use Filament\Tables;
use Filament\Tests\Models\Post;
use Filament\Tests\Tables\Fixtures\PostsTable;
use Filament\Tests\Tables\TestCase;
use Livewire\Features\SupportTesting\Testable;

use function Filament\Tests\livewire;

uses(TestCase::class);

it('can group a table', function () {
    $posts = Post::factory(20)->create();

    livewire(PostsTable::class)
        ->tap(function (Testable $testable) {
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
        ->tap(function (Testable $testable) {
            /** @var PostsTable $livewire */
            $livewire = $testable->instance();

            $table = $livewire->getTable();

            expect($table)
                ->getGrouping()->toBeInstanceOf(Tables\Grouping\Group::class)
                ->and($table->getGrouping())
                ->getLabel()->toBe('Dynamic label');
        });
});
