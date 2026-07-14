<?php

use Filament\Support\View\Concerns\CanGenerateButtonHtml;
use Filament\Support\View\Concerns\CanGenerateIconButtonHtml;
use Filament\Support\View\Concerns\CanGenerateLinkHtml;
use Filament\Tests\TestCase;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Str;
use Illuminate\View\ComponentAttributeBag;

uses(TestCase::class);

function embeddedHtmlGenerator(): object
{
    return new class
    {
        use CanGenerateButtonHtml;
        use CanGenerateIconButtonHtml;
        use CanGenerateLinkHtml;
    };
}

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

it('reflects the initial state in the `aria-checked` attribute of the toggle Blade component', function (): void {
    expect(Blade::render('<x-filament::toggle :state="true" />'))
        ->toContain('aria-checked="true"');

    expect(Blade::render('<x-filament::toggle :state="false" />'))
        ->toContain('aria-checked="false"');
});

it('escapes the `aria-label` in the embedded icon-button generator against attribute breakout', function (): void {
    $html = embeddedHtmlGenerator()->generateIconButtonHtml(
        attributes: new ComponentAttributeBag,
        icon: 'heroicon-o-pencil',
        label: new HtmlString('x" onmouseover="alert(1)"'),
    );

    expect($html)
        ->toContain('aria-label="x&quot; onmouseover=&quot;alert(1)&quot;"')
        ->not->toContain('onmouseover="alert(1)"');
});

it('does not double-encode entities in the embedded icon-button generator `aria-label`', function (): void {
    $html = embeddedHtmlGenerator()->generateIconButtonHtml(
        attributes: new ComponentAttributeBag,
        icon: 'heroicon-o-pencil',
        label: new HtmlString('Terms &amp; conditions'),
    );

    expect($html)
        ->toContain('aria-label="Terms &amp; conditions"')
        ->not->toContain('&amp;amp;');
});

it('escapes the sr-only `aria-label` in the embedded button generator against attribute breakout', function (): void {
    $html = embeddedHtmlGenerator()->generateButtonHtml(
        attributes: new ComponentAttributeBag,
        isLabelSrOnly: true,
        label: new HtmlString('x" onmouseover="alert(1)"'),
    );

    expect($html)
        ->toContain('aria-label="x&quot; onmouseover=&quot;alert(1)&quot;"')
        ->not->toContain('onmouseover="alert(1)"');
});

it('escapes the sr-only `aria-label` in the embedded link generator against attribute breakout', function (): void {
    $html = embeddedHtmlGenerator()->generateLinkHtml(
        attributes: new ComponentAttributeBag,
        href: 'https://example.com',
        isLabelSrOnly: true,
        label: new HtmlString('x" onmouseover="alert(1)"'),
        tag: 'a',
    );

    expect($html)
        ->toContain('aria-label="x&quot; onmouseover=&quot;alert(1)&quot;"')
        ->not->toContain('onmouseover="alert(1)"');
});

it('does not double-encode entities in the embedded link generator sr-only `aria-label`', function (): void {
    $html = embeddedHtmlGenerator()->generateLinkHtml(
        attributes: new ComponentAttributeBag,
        href: 'https://example.com',
        isLabelSrOnly: true,
        label: new HtmlString('Terms &amp; conditions'),
        tag: 'a',
    );

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
