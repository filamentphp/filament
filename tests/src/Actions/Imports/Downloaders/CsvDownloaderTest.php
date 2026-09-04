<?php

use Filament\Actions\Imports\Downloaders\Contracts\Downloader;
use Filament\Actions\Imports\Downloaders\CsvDownloader;
use Filament\Tests\TestCase;

uses(TestCase::class);

it('implements `Downloader` interface', function (): void {
    $downloader = new CsvDownloader;

    expect($downloader)->toBeInstanceOf(Downloader::class);
});

it('is invocable', function (): void {
    $downloader = new CsvDownloader;

    expect(is_callable($downloader))->toBeTrue();
});
