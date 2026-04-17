<?php

use Filament\Support\ChunkIterator;
use Filament\Tests\TestCase;

uses(TestCase::class);

it('chunks an iterator into arrays of the given size', function (): void {
    $iterator = new ArrayIterator([1, 2, 3, 4, 5, 6]);
    $chunker = new ChunkIterator($iterator, 2);

    $chunks = iterator_to_array($chunker->get());

    expect($chunks)->toHaveCount(3);
    expect($chunks[0])->toBe([1, 2]);
    expect($chunks[1])->toBe([3, 4]);
    expect($chunks[2])->toBe([5, 6]);
});

it('yields a remainder chunk when items do not divide evenly', function (): void {
    $iterator = new ArrayIterator([1, 2, 3, 4, 5]);
    $chunker = new ChunkIterator($iterator, 3);

    $chunks = iterator_to_array($chunker->get());

    expect($chunks)->toHaveCount(2);
    expect($chunks[0])->toBe([1, 2, 3]);
    expect($chunks[1])->toBe([4, 5]);
});

it('yields a single chunk when items fit exactly', function (): void {
    $iterator = new ArrayIterator([1, 2, 3]);
    $chunker = new ChunkIterator($iterator, 3);

    $chunks = iterator_to_array($chunker->get());

    expect($chunks)->toHaveCount(1);
    expect($chunks[0])->toBe([1, 2, 3]);
});

it('yields nothing for an empty iterator', function (): void {
    $iterator = new ArrayIterator([]);
    $chunker = new ChunkIterator($iterator, 5);

    $chunks = iterator_to_array($chunker->get());

    expect($chunks)->toBe([]);
});

it('yields one item per chunk when chunk size is 1', function (): void {
    $iterator = new ArrayIterator(['a', 'b', 'c']);
    $chunker = new ChunkIterator($iterator, 1);

    $chunks = iterator_to_array($chunker->get());

    expect($chunks)->toHaveCount(3);
    expect($chunks[0])->toBe(['a']);
    expect($chunks[1])->toBe(['b']);
    expect($chunks[2])->toBe(['c']);
});

it('yields all items in one chunk when chunk size exceeds item count', function (): void {
    $iterator = new ArrayIterator([1, 2]);
    $chunker = new ChunkIterator($iterator, 100);

    $chunks = iterator_to_array($chunker->get());

    expect($chunks)->toHaveCount(1);
    expect($chunks[0])->toBe([1, 2]);
});

it('returns a `Generator` from `get()`', function (): void {
    $iterator = new ArrayIterator([1]);
    $chunker = new ChunkIterator($iterator, 1);

    expect($chunker->get())->toBeInstanceOf(Generator::class);
});
