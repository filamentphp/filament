<?php

use Filament\Support\Assets\Js;
use Filament\Tests\TestCase;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\HtmlString;

uses(TestCase::class);

it('can be constructed with `make()`', function (): void {
    $js = Js::make('custom', '/path/to/custom.js');

    expect($js)->toBeInstanceOf(Js::class);
    expect($js->getId())->toBe('custom');
});

describe('boolean properties', function (): void {
    it('defaults `isAsync()` to `false`', function (): void {
        expect(Js::make('script')->isAsync())->toBeFalse();
    });

    it('can set `async()`', function (): void {
        expect(Js::make('script')->async()->isAsync())->toBeTrue();
    });

    it('can set `async()` to `false`', function (): void {
        expect(Js::make('script')->async()->async(false)->isAsync())->toBeFalse();
    });

    it('defaults `isDeferred()` to `false`', function (): void {
        expect(Js::make('script')->isDeferred())->toBeFalse();
    });

    it('can set `defer()`', function (): void {
        expect(Js::make('script')->defer()->isDeferred())->toBeTrue();
    });

    it('defaults `isCore()` to `false`', function (): void {
        expect(Js::make('script')->isCore())->toBeFalse();
    });

    it('can set `core()`', function (): void {
        expect(Js::make('script')->core()->isCore())->toBeTrue();
    });

    it('defaults `isNavigateOnce()` to `true`', function (): void {
        expect(Js::make('script')->isNavigateOnce())->toBeTrue();
    });

    it('can set `navigateOnce()` to `false`', function (): void {
        expect(Js::make('script')->navigateOnce(false)->isNavigateOnce())->toBeFalse();
    });

    it('defaults `isModule()` to `false`', function (): void {
        expect(Js::make('script')->isModule())->toBeFalse();
    });

    it('can set `module()`', function (): void {
        expect(Js::make('script')->module()->isModule())->toBeTrue();
    });
});

describe('extra attributes', function (): void {
    it('returns empty array for `getExtraAttributes()` by default', function (): void {
        expect(Js::make('script')->getExtraAttributes())->toBe([]);
    });

    it('can set `extraAttributes()`', function (): void {
        $js = Js::make('script')
            ->extraAttributes(['nonce' => 'abc123']);

        expect($js->getExtraAttributes())->toBe(['nonce' => 'abc123']);
    });

    it('renders extra attributes as HTML', function (): void {
        $js = Js::make('script')
            ->extraAttributes(['nonce' => 'abc123', 'crossorigin' => 'anonymous']);

        $html = $js->getExtraAttributesHtml();

        expect($html)->toContain('nonce="abc123"');
        expect($html)->toContain('crossorigin="anonymous"');
    });

    it('returns empty string for `getExtraAttributesHtml()` when no attributes', function (): void {
        expect(Js::make('script')->getExtraAttributesHtml())->toBe('');
    });
});

describe('`getSrc()`', function (): void {
    it('returns the remote URL directly when path is remote', function (): void {
        $js = Js::make('external', 'https://cdn.example.com/script.js');

        expect($js->getSrc())->toBe('https://cdn.example.com/script.js');
    });

    it('returns an asset URL with version for local paths', function (): void {
        $js = Js::make('local')
            ->package('my-package');

        $src = $js->getSrc();

        expect($src)->toContain('local.js');
        expect($src)->toContain('?v=');
    });
});

describe('relative public path', function (): void {
    it('generates a path containing the ID', function (): void {
        $js = Js::make('my-script')
            ->package('my-package');

        expect($js->getRelativePublicPath())->toContain('js/my-package/my-script.js');
    });
});

describe('`getHtml()`', function (): void {
    it('returns an `Htmlable` instance', function (): void {
        $js = Js::make('script')
            ->package('my-package');

        expect($js->getHtml())->toBeInstanceOf(Htmlable::class);
    });

    it('generates a `<script>` tag by default', function (): void {
        $js = Js::make('script')
            ->package('my-package');

        $html = $js->getHtml()->toHtml();

        expect($html)->toContain('<script');
        expect($html)->toContain('src=');
    });

    it('includes `async` attribute when `async()` is set', function (): void {
        $js = Js::make('script')
            ->package('my-package')
            ->async();

        $html = $js->getHtml()->toHtml();

        expect($html)->toContain('async');
    });

    it('includes `defer` attribute when `defer()` is set', function (): void {
        $js = Js::make('script')
            ->package('my-package')
            ->defer();

        $html = $js->getHtml()->toHtml();

        expect($html)->toContain('defer');
    });

    it('includes `type="module"` when `module()` is set', function (): void {
        $js = Js::make('script')
            ->package('my-package')
            ->module();

        $html = $js->getHtml()->toHtml();

        expect($html)->toContain('type="module"');
    });

    it('passes through custom HTML containing `<script`', function (): void {
        $js = Js::make('script')
            ->html('<script src="/custom.js"></script>');

        $html = $js->getHtml()->toHtml();

        expect($html)->toBe('<script src="/custom.js"></script>');
    });

    it('passes through custom `Htmlable` containing `<script`', function (): void {
        $htmlable = new HtmlString('<script src="/custom.js"></script>');
        $js = Js::make('script')
            ->html($htmlable);

        expect($js->getHtml())->toBe($htmlable);
    });
});
