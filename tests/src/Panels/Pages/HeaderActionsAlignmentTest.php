<?php

use Filament\Pages\Page;
use Filament\Support\Enums\Alignment;
use Filament\Tests\Fixtures\Pages\Actions;
use Filament\Tests\Fixtures\Pages\Settings;
use Filament\Tests\Panels\Pages\TestCase;

use function Filament\Tests\livewire;

uses(TestCase::class);

afterEach(function (): void {
    Page::$defaultHeaderActionsAlignment = Alignment::Start;
});

it('has `Alignment::Start` as the default `getHeaderActionsAlignment()` value', function (): void {
    expect(livewire(Settings::class)->instance()->getHeaderActionsAlignment())
        ->toBe(Alignment::Start);
});

it('can use `alignHeaderActionsStart()`, `alignHeaderActionsCenter()` and `alignHeaderActionsEnd()` to set a global default header actions alignment', function (): void {
    Settings::alignHeaderActionsEnd();

    expect(livewire(Settings::class)->instance()->getHeaderActionsAlignment())
        ->toBe(Alignment::End);

    Settings::alignHeaderActionsCenter();

    expect(livewire(Settings::class)->instance()->getHeaderActionsAlignment())
        ->toBe(Alignment::Center);

    Settings::alignHeaderActionsStart();

    expect(livewire(Settings::class)->instance()->getHeaderActionsAlignment())
        ->toBe(Alignment::Start);
});

it('can use `defaultHeaderActionsAlignment()` to set a global default header actions alignment using a raw string', function (): void {
    Settings::defaultHeaderActionsAlignment('center');

    expect(livewire(Settings::class)->instance()->getHeaderActionsAlignment())
        ->toBe('center');
});

it('shares the global default header actions alignment across every page that uses `InteractsWithHeaderActions`', function (): void {
    Actions::alignHeaderActionsEnd();

    expect(livewire(Settings::class)->instance()->getHeaderActionsAlignment())
        ->toBe(Alignment::End);
});

it('can use `headerActionsAlignment()` to override the global default header actions alignment for a single page instance', function (): void {
    $page = livewire(Actions::class)->instance();

    expect($page->headerActionsAlignment(Alignment::Center))
        ->toBe($page);

    expect($page->getHeaderActionsAlignment())
        ->toBe(Alignment::Center);

    Actions::alignHeaderActionsEnd();

    expect($page->getHeaderActionsAlignment())
        ->toBe(Alignment::Center);
});

it('can use `headerActionsAlignment(null)` to unset a page instance override and fall back to the global default', function (): void {
    $page = livewire(Actions::class)->instance();

    $page->headerActionsAlignment(Alignment::Center);
    $page->headerActionsAlignment(null);

    expect($page->getHeaderActionsAlignment())
        ->toBe(Alignment::Start);
});
