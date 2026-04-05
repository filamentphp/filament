<?php

use Filament\Schemas\Components\Group;
use Filament\Tests\TestCase;

uses(TestCase::class);

it('can be constructed with empty schema', function (): void {
    $group = Group::make();

    expect($group)->toBeInstanceOf(Group::class);
});

it('can be constructed with schema array', function (): void {
    $group = Group::make([]);

    expect($group)->toBeInstanceOf(Group::class);
});

it('can be constructed with a `Closure` schema', function (): void {
    $group = Group::make(static fn (): array => []);

    expect($group)->toBeInstanceOf(Group::class);
});
