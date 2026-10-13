<?php

use Filament\SpatieLaravelMediaLibraryPlugin\Collections\AllMediaCollections;
use Filament\Tests\TestCase;

uses(TestCase::class);

it('can be constructed with `make()`', function (): void {
    $instance = AllMediaCollections::make();

    expect($instance)->toBeInstanceOf(AllMediaCollections::class);
});

it('returns a new instance each time `make()` is called', function (): void {
    $first = AllMediaCollections::make();
    $second = AllMediaCollections::make();

    expect($first)->not->toBe($second);
});
