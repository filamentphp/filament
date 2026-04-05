<?php

use Filament\Support\Assets\Css;
use Filament\Tests\TestCase;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\HtmlString;

uses(TestCase::class);

it('can be constructed with `make()`', function (): void {
    $css = Css::make('custom', '/path/to/custom.css');

    expect($css)->toBeInstanceOf(Css::class);
    expect($css->getId())->toBe('custom');
    expect($css->getPath())->toBe('/path/to/custom.css');
});

describe('relative public path', function (): void {
    it('generates a default relative public path from package and ID', function (): void {
        $css = Css::make('styles')
            ->package('my-package');

        $path = $css->getRelativePublicPath();

        expect($path)->toContain('css/my-package/styles.css');
    });

    it('can set a custom `relativePublicPath()`', function (): void {
        $css = Css::make('styles')
            ->relativePublicPath('custom/path/styles.css');

        expect($css->getRelativePublicPath())->toBe('custom/path/styles.css');
    });

    it('uses custom relative path over generated one', function (): void {
        $css = Css::make('styles')
            ->package('my-package')
            ->relativePublicPath('override/styles.css');

        expect($css->getRelativePublicPath())->toBe('override/styles.css');
    });
});

describe('`getHref()`', function (): void {
    it('returns the remote URL directly when path is remote', function (): void {
        $css = Css::make('external', 'https://cdn.example.com/styles.css');

        expect($css->getHref())->toBe('https://cdn.example.com/styles.css');
    });

    it('returns an asset URL with version for local paths', function (): void {
        $css = Css::make('local')
            ->package('my-package');

        $href = $css->getHref();

        expect($href)->toContain('local.css');
        expect($href)->toContain('?v=');
    });
});

describe('`getHtml()`', function (): void {
    it('returns an `Htmlable` instance', function (): void {
        $css = Css::make('styles')
            ->package('my-package');

        expect($css->getHtml())->toBeInstanceOf(Htmlable::class);
    });

    it('generates a `<link>` tag by default', function (): void {
        $css = Css::make('styles')
            ->package('my-package');

        $html = $css->getHtml()->toHtml();

        expect($html)->toContain('<link');
        expect($html)->toContain('rel="stylesheet"');
        expect($html)->toContain('data-navigate-track');
    });

    it('passes through custom HTML containing `<link`', function (): void {
        $css = Css::make('styles')
            ->html('<link href="/custom.css" rel="stylesheet">');

        $html = $css->getHtml()->toHtml();

        expect($html)->toBe('<link href="/custom.css" rel="stylesheet">');
    });

    it('passes through custom `Htmlable` containing `<link`', function (): void {
        $htmlable = new HtmlString('<link href="/custom.css" rel="stylesheet">');
        $css = Css::make('styles')
            ->html($htmlable);

        expect($css->getHtml())->toBe($htmlable);
    });

    it('wraps custom URL string in a `<link>` tag', function (): void {
        $css = Css::make('styles')
            ->html('/custom/path.css');

        $html = $css->getHtml()->toHtml();

        expect($html)->toContain('<link');
        expect($html)->toContain('/custom/path.css');
    });
});
