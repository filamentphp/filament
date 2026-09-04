<?php

use Filament\Tests\TestCase;

uses(TestCase::class)->group('serial');

beforeEach(function (): void {
    $this->withoutMockingConsoleOutput();

    $this->gitIgnorePath = base_path('.gitignore');
    $this->originalGitIgnoreContents = file_exists($this->gitIgnorePath)
        ? file_get_contents($this->gitIgnorePath)
        : null;

    $this->composerJsonPath = base_path('composer.json');
    $this->originalComposerJsonContents = file_exists($this->composerJsonPath)
        ? file_get_contents($this->composerJsonPath)
        : null;
});

afterEach(function (): void {
    if (filled($this->originalGitIgnoreContents)) {
        file_put_contents($this->gitIgnorePath, $this->originalGitIgnoreContents);
    } elseif (file_exists($this->gitIgnorePath)) {
        unlink($this->gitIgnorePath);
    }

    if (filled($this->originalComposerJsonContents)) {
        file_put_contents($this->composerJsonPath, $this->originalComposerJsonContents);
    }
});

it('adds the published assets to `.gitignore`', function (): void {
    file_put_contents($this->gitIgnorePath, '/vendor' . PHP_EOL);

    $this->artisan('filament:install', ['--no-interaction' => true]);

    expect(file_get_contents($this->gitIgnorePath))
        ->toBe(implode(PHP_EOL, [
            '/vendor',
            '',
            '/public/css/filament',
            '/public/fonts/filament',
            '/public/js/filament',
            '',
        ]));
});

it('does not duplicate `.gitignore` rules with leading or trailing slashes', function (): void {
    file_put_contents($this->gitIgnorePath, implode(PHP_EOL, [
        '/vendor',
        'public/css/filament/',
        '/public/js/filament/',
        '',
    ]));

    $this->artisan('filament:install', ['--no-interaction' => true]);

    expect(file_get_contents($this->gitIgnorePath))
        ->toBe(implode(PHP_EOL, [
            '/vendor',
            'public/css/filament/',
            '/public/fonts/filament',
            '/public/js/filament/',
            '',
        ]));
});

it('inserts `.gitignore` rules after the nearest previous public path', function (): void {
    file_put_contents($this->gitIgnorePath, implode(PHP_EOL, [
        '/node_modules',
        '/public/build',
        '/public/fonts-manifest.dev.json',
        '/public/hot',
        '/public/storage',
        '',
    ]));

    $this->artisan('filament:install', ['--no-interaction' => true]);

    expect(file_get_contents($this->gitIgnorePath))
        ->toBe(implode(PHP_EOL, [
            '/node_modules',
            '/public/build',
            '/public/css/filament',
            '/public/fonts-manifest.dev.json',
            '/public/fonts/filament',
            '/public/hot',
            '/public/js/filament',
            '/public/storage',
            '',
        ]));
});

it('uses the previous inserted `.gitignore` rule when an anchor is missing', function (): void {
    file_put_contents($this->gitIgnorePath, implode(PHP_EOL, [
        '/public/build',
        '/public/storage',
        '',
    ]));

    $this->artisan('filament:install', ['--no-interaction' => true]);

    expect(file_get_contents($this->gitIgnorePath))
        ->toBe(implode(PHP_EOL, [
            '/public/build',
            '/public/css/filament',
            '/public/fonts/filament',
            '/public/js/filament',
            '/public/storage',
            '',
        ]));
});

it('preserves CRLF line endings when adding rules to `.gitignore`', function (): void {
    file_put_contents($this->gitIgnorePath, "/vendor\r\n");

    $this->artisan('filament:install', ['--no-interaction' => true]);

    expect(file_get_contents($this->gitIgnorePath))
        ->toBe(implode("\r\n", [
            '/vendor',
            '',
            '/public/css/filament',
            '/public/fonts/filament',
            '/public/js/filament',
            '',
        ]));
});

it('uses `assets_path` from the configuration when ignoring the published assets', function (): void {
    config()->set('filament.assets_path', 'filament');

    file_put_contents($this->gitIgnorePath, '/vendor' . PHP_EOL);

    $this->artisan('filament:install', ['--no-interaction' => true]);

    expect(file_get_contents($this->gitIgnorePath))
        ->toBe(implode(PHP_EOL, [
            '/vendor',
            '',
            '/public/filament/css/filament',
            '/public/filament/fonts/filament',
            '/public/filament/js/filament',
            '',
        ]));
});

it('does not create a `.gitignore` file if one does not exist', function (): void {
    if (file_exists($this->gitIgnorePath)) {
        unlink($this->gitIgnorePath);
    }

    $this->artisan('filament:install', ['--no-interaction' => true]);

    expect(file_exists($this->gitIgnorePath))
        ->toBeFalse();
});
