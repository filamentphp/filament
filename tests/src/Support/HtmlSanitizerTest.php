<?php

use Filament\Tests\TestCase;
use Illuminate\Support\Str;

uses(TestCase::class);

it('strips `<script>` tags', function (): void {
    expect(Str::sanitizeHtml('<p>Hello</p><script>alert("xss")</script>'))
        ->toBe('<p>Hello</p>');
});

it('strips `<script>` tags with attributes', function (): void {
    expect(Str::sanitizeHtml('<p>Hello</p><script src="evil.js"></script>'))
        ->toBe('<p>Hello</p>');
});

it('strips `javascript:` links', function (): void {
    expect(Str::sanitizeHtml('<a href="javascript:alert(1)">click</a>'))
        ->toBe('<a>click</a>');
});

it('strips `javascript:` links with whitespace obfuscation', function (): void {
    expect(Str::sanitizeHtml('<a href="  javascript:alert(1)">click</a>'))
        ->toBe('<a>click</a>');
});

it('strips `onclick` attributes', function (): void {
    expect(Str::sanitizeHtml('<p onclick="alert(1)">Hello</p>'))
        ->toBe('<p>Hello</p>');
});

it('strips `onerror` attributes', function (): void {
    expect(Str::sanitizeHtml('<img src="x" onerror="alert(1)">'))
        ->toBe('<img src="x" />');
});

it('strips `onload` attributes', function (): void {
    expect(Str::sanitizeHtml('<img src="x" onload="alert(1)">'))
        ->toBe('<img src="x" />');
});

it('strips `onmouseover` attributes', function (): void {
    expect(Str::sanitizeHtml('<p onmouseover="alert(1)">Hello</p>'))
        ->toBe('<p>Hello</p>');
});

it('strips `onfocus` attributes with `autofocus`', function (): void {
    expect(Str::sanitizeHtml('<input onfocus="alert(1)" autofocus>'))
        ->not->toContain('onfocus');
});

it('strips `<iframe>` tags', function (): void {
    expect(Str::sanitizeHtml('<p>Hello</p><iframe src="evil.html"></iframe>'))
        ->toBe('<p>Hello</p>');
});

it('strips `<object>` tags', function (): void {
    expect(Str::sanitizeHtml('<p>Hello</p><object data="evil.swf"></object>'))
        ->toBe('<p>Hello</p>');
});

it('strips `<embed>` tags', function (): void {
    expect(Str::sanitizeHtml('<p>Hello</p><embed src="evil.swf">'))
        ->toBe('<p>Hello</p>');
});

it('strips `<form>` tags', function (): void {
    expect(Str::sanitizeHtml('<form action="evil.php"><input></form>'))
        ->not->toContain('<form');
});

it('strips `<svg>` with inline script', function (): void {
    $result = Str::sanitizeHtml('<svg onload="alert(1)"><circle r="10"></circle></svg>');

    expect($result)
        ->not->toContain('onload')
        ->not->toContain('alert');
});

it('strips `<math>` with embedded `<script>`', function (): void {
    expect(Str::sanitizeHtml('<math><mtext><script>alert(1)</script></mtext></math>'))
        ->not->toContain('<script');
});

it('strips `data:` URIs in links', function (): void {
    expect(Str::sanitizeHtml('<a href="data:text/html,<script>alert(1)</script>">click</a>'))
        ->toBe('<a>click</a>');
});

it('strips `vbscript:` links', function (): void {
    expect(Str::sanitizeHtml('<a href="vbscript:alert(1)">click</a>'))
        ->toBe('<a>click</a>');
});

it('preserves safe HTML elements', function (): void {
    $html = '<p>Hello <strong>world</strong>, <em>this</em> is <a href="https://example.com">safe</a>.</p>';

    expect(Str::sanitizeHtml($html))
        ->toBe($html);
});

it('preserves allowed `data-*` attributes', function (): void {
    $html = '<span data-type="mergeTag" data-id="user_name" data-color="red">Tag</span>';

    expect(Str::sanitizeHtml($html))
        ->toContain('data-type="mergeTag"')
        ->toContain('data-id="user_name"')
        ->toContain('data-color="red"');
});

it('preserves `style` attributes', function (): void {
    $html = '<span style="color: red;">Hello</span>';

    expect(Str::sanitizeHtml($html))
        ->toBe($html);
});

it('preserves `class` attributes', function (): void {
    $html = '<div class="fi-prose">Hello</div>';

    expect(Str::sanitizeHtml($html))
        ->toBe($html);
});

it('preserves `width` and `height` on images', function (): void {
    $html = '<img src="photo.jpg" width="100" height="50" />';

    expect(Str::sanitizeHtml($html))
        ->toContain('width="100"')
        ->toContain('height="50"');
});

it('preserves rich editor grid attributes', function (): void {
    $html = '<div data-cols="3" data-col-span="2" data-from-breakpoint="md">Content</div>';

    expect(Str::sanitizeHtml($html))
        ->toContain('data-cols="3"')
        ->toContain('data-col-span="2"')
        ->toContain('data-from-breakpoint="md"');
});
