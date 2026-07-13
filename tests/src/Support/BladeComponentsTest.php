<?php

use Filament\Tests\TestCase;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Str;

uses(TestCase::class);

it('escapes the sr-only `aria-label` on the link Blade component', function (): void {
    $html = Blade::render(<<<'BLADE'
        <x-filament::link label-sr-only>{!! 'x" onmouseover="alert(1)"' !!}</x-filament::link>
        BLADE);

    $openingTag = Str::of($html)->after('<a')->before('>');

    expect((string) $openingTag)
        ->toContain('aria-label="x&quot; onmouseover=&quot;alert(1)&quot;"')
        ->not->toContain('onmouseover="alert(1)"');
});

it('does not double-encode entities in the sr-only `aria-label` on the link Blade component', function (): void {
    $html = Blade::render(<<<'BLADE'
        <x-filament::link label-sr-only>{{ 'Terms & conditions' }}</x-filament::link>
        BLADE);

    expect($html)
        ->toContain('aria-label="Terms &amp; conditions"')
        ->not->toContain('&amp;amp;');
});

it('escapes the sr-only `aria-label` on the button Blade component', function (): void {
    $html = Blade::render(<<<'BLADE'
        <x-filament::button label-sr-only>{!! 'x" onmouseover="alert(1)"' !!}</x-filament::button>
        BLADE);

    $openingTag = Str::of($html)->after('<button')->before('>');

    expect((string) $openingTag)
        ->toContain('aria-label="x&quot; onmouseover=&quot;alert(1)&quot;"')
        ->not->toContain('onmouseover="alert(1)"');
});

it('does not double-encode entities in the sr-only `aria-label` on the button Blade component', function (): void {
    $html = Blade::render(<<<'BLADE'
        <x-filament::button label-sr-only>{{ 'Terms & conditions' }}</x-filament::button>
        BLADE);

    expect($html)
        ->toContain('aria-label="Terms &amp; conditions"')
        ->not->toContain('&amp;amp;');
});

it('omits `aria-controls` from the collapse button when a collapsible section has no content or footer', function (): void {
    $html = Blade::render(<<<'BLADE'
        <x-filament::section collapsible heading="Empty section"></x-filament::section>
        BLADE);

    expect($html)
        ->not->toContain('aria-controls');
});

it('binds `aria-controls` on the collapse button when a collapsible section has content', function (): void {
    $html = Blade::render(<<<'BLADE'
        <x-filament::section collapsible heading="Filled section">Content</x-filament::section>
        BLADE);

    expect($html)
        ->toContain('x-bind:aria-controls');
});
