<?php

use Filament\Tests\Fixtures\Livewire\PostsTable;
use Filament\Tests\Tables\TestCase;

use function Filament\Tests\livewire;

uses(TestCase::class);

it('returns `false` for `isCompact()` by default', function (): void {
    $table = livewire(PostsTable::class)->instance()->getTable();

    expect($table->isCompact())->toBeFalse();
});

it('can use `compact()` to enable compact mode', function (): void {
    $table = livewire(PostsTable::class)->instance()->getTable();
    $table->compact();

    expect($table->isCompact())->toBeTrue();
});

it('can use `compact(false)` to disable compact mode', function (): void {
    $table = livewire(PostsTable::class)->instance()->getTable();
    $table->compact();
    $table->compact(false);

    expect($table->isCompact())->toBeFalse();
});

it('can use `compact()` with a `Closure`', function (): void {
    $table = livewire(PostsTable::class)->instance()->getTable();
    $table->compact(fn (): bool => true);

    expect($table->isCompact())->toBeTrue();
});

it('can use `compact()` with a `Closure` that returns `false`', function (): void {
    $table = livewire(PostsTable::class)->instance()->getTable();
    $table->compact(fn (): bool => false);

    expect($table->isCompact())->toBeFalse();
});
