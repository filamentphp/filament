<?php

use Filament\SpatieLaravelTagsPlugin\Types\AllTagTypes;
use Filament\Tests\TestCase;

uses(TestCase::class);

it('can be constructed with `make()`', function (): void {
    $instance = AllTagTypes::make();

    expect($instance)->toBeInstanceOf(AllTagTypes::class);
});

it('returns a new instance each time `make()` is called', function (): void {
    $first = AllTagTypes::make();
    $second = AllTagTypes::make();

    expect($first)->not->toBe($second);
});
