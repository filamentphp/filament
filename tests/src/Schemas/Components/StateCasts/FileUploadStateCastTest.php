<?php

use Filament\Schemas\Components\StateCasts\FileUploadStateCast;
use Filament\Tests\TestCase;
use Illuminate\Support\Str;

uses(TestCase::class);

it('can transform an array of files into a single file', function (): void {
    $cast = app(FileUploadStateCast::class, ['isMultiple' => false]);

    expect($cast->get([$file = Str::random()]))
        ->toBe($file);
});

it('can transform a single file into an array of files', function (): void {
    $cast = app(FileUploadStateCast::class, ['isMultiple' => true]);

    expect($cast->get($file = Str::random()))
        ->toBe([$file]);
});

it('can reset the keys in an array of files', function (): void {
    $cast = app(FileUploadStateCast::class, ['isMultiple' => true]);

    expect($cast->get([(string) Str::uuid() => $file = Str::random()]))
        ->toBe([$file]);
});

it('can leave a single file alone in the getter', function (): void {
    $cast = app(FileUploadStateCast::class, ['isMultiple' => false]);

    expect($cast->get($file = Str::random()))
        ->toBe($file);
});

it('can wrap a single file in an array in the setter', function (): void {
    $cast = app(FileUploadStateCast::class, ['isMultiple' => false]);

    expect($cast->set($file = Str::random()))
        ->toBeArray()
        ->toHaveCount(1)
        ->toContain($file)
        ->not->toBe([$file]);
});

it('can strip empty items from the final array in the setter', function (): void {
    $cast = app(FileUploadStateCast::class, ['isMultiple' => true]);

    expect($cast->set([null, $file = Str::random(), '']))
        ->toBeArray()
        ->toHaveCount(1)
        ->toContain($file)
        ->not->toBe([$file]);

    expect($cast->set([null, '']))
        ->toBeArray()
        ->toBe([]);

    expect($cast->set(null))
        ->toBeArray()
        ->toBe([]);
});
