<?php

use Filament\Schemas\Components\Icon;
use Filament\Support\Enums\IconSize;
use Filament\Support\Icons\Heroicon;
use Filament\Tests\Fixtures\Models\User;
use Filament\Tests\TestCase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\HtmlString;

uses(TestCase::class);

it('can be constructed with a string icon', function (): void {
    $icon = Icon::make('heroicon-o-check');

    expect($icon->getIcon())->toBe('heroicon-o-check');
});

it('can be constructed with a `BackedEnum` icon', function (): void {
    $icon = Icon::make(Heroicon::Check);

    expect($icon->getIcon())->toBe(Heroicon::Check);
});

it('can be constructed with an `Htmlable` icon', function (): void {
    $html = new HtmlString('<svg data-testid="custom-icon"></svg>');
    $icon = Icon::make($html);

    expect($icon->getIcon())->toBe($html);
});

it('can set `icon()` with a `Closure`', function (): void {
    $icon = Icon::make('initial')
        ->icon(static fn (): string => 'heroicon-o-star');

    expect($icon->getIcon())->toBe('heroicon-o-star');
});

it('can set `icon()` with a `Closure` returning an `Htmlable` icon', function (): void {
    $html = new HtmlString('<svg data-testid="custom-icon"></svg>');
    $icon = Icon::make('initial')
        ->icon(static fn (): HtmlString => $html);

    expect($icon->getIcon())->toBe($html);
});

describe('color', function (): void {
    it('returns `null` for `getColor()` by default', function (): void {
        $icon = Icon::make(Heroicon::Check);

        expect($icon->getColor())->toBeNull();
    });

    it('can set `color()`', function (): void {
        $icon = Icon::make(Heroicon::Check)->color('success');

        expect($icon->getColor())->toBe('success');
    });

    it('can set `color()` with a `Closure`', function (): void {
        $icon = Icon::make(Heroicon::Check)
            ->color(static fn (): string => 'danger');

        expect($icon->getColor())->toBe('danger');
    });
});

describe('size', function (): void {
    it('returns `null` for `getSize()` by default', function (): void {
        $icon = Icon::make(Heroicon::Check);

        expect($icon->getSize())->toBeNull();
    });

    it('can set `size()`', function (): void {
        $icon = Icon::make(Heroicon::Check)->size(IconSize::Large);

        expect($icon->getSize())->toBe(IconSize::Large);
    });

    it('can set `size()` with a `Closure`', function (): void {
        $icon = Icon::make(Heroicon::Check)
            ->size(static fn (): IconSize => IconSize::Large);

        expect($icon->getSize())->toBe(IconSize::Large);
    });

    it('resolves a string `size()` to the matching `IconSize`', function (): void {
        $icon = Icon::make(Heroicon::Check)->size('lg');

        expect($icon->getSize())->toBe(IconSize::Large);
    });

    it('returns `null` for `getSize()` when `size()` is `"base"`', function (): void {
        $icon = Icon::make(Heroicon::Check)->size('base');

        expect($icon->getSize())->toBeNull();
    });
});

describe('tooltip', function (): void {
    it('returns `null` for `getTooltip()` by default', function (): void {
        $icon = Icon::make(Heroicon::Check);

        expect($icon->getTooltip())->toBeNull();
    });

    it('can set `tooltip()`', function (): void {
        $icon = Icon::make(Heroicon::Check)->tooltip('Verified');

        expect($icon->getTooltip())->toBe('Verified');
    });

    it('can set `tooltip()` with a `Closure`', function (): void {
        $icon = Icon::make(Heroicon::Check)
            ->tooltip(static fn (): string => 'Dynamic tip');

        expect($icon->getTooltip())->toBe('Dynamic tip');
    });
});

describe('rendering', function (): void {
    it('renders with a string icon via `toEmbeddedHtml()`', function (): void {
        $html = Icon::make('heroicon-o-check')->toEmbeddedHtml();
        expect($html)->not->toBe('');
    });

    it('renders with `icon()` set via `Closure`', function (): void {
        $html = Icon::make('initial')
            ->icon(static fn (): string => 'heroicon-o-star')
            ->toEmbeddedHtml();
        expect($html)->not->toBe('');
    });

    it('renders with `color()`', function (): void {
        $html = Icon::make(Heroicon::Check)->color('success')->toEmbeddedHtml();
        expect($html)->not->toBe('');
    });

    it('renders with `color()` set via `Closure`', function (): void {
        $html = Icon::make(Heroicon::Check)->color(static fn (): string => 'danger')->toEmbeddedHtml();
        expect($html)->not->toBe('');
    });

    it('renders with `tooltip()`', function (): void {
        $html = Icon::make(Heroicon::Check)->tooltip('Verified')->toEmbeddedHtml();
        expect($html)->not->toBe('');
    });

    it('renders with `tooltip()` set via `Closure`', function (): void {
        $html = Icon::make(Heroicon::Check)->tooltip(static fn (): string => 'Dynamic tip')->toEmbeddedHtml();
        expect($html)->not->toBe('');
    });

    it('renders with `size()`', function (): void {
        $html = Icon::make(Heroicon::Check)->size(IconSize::Large)->toEmbeddedHtml();
        expect($html)->toContain('fi-size-lg');
    });

    it('renders with `size()` set via `Closure`', function (): void {
        $html = Icon::make(Heroicon::Check)->size(static fn (): IconSize => IconSize::Small)->toEmbeddedHtml();
        expect($html)->toContain('fi-size-sm');
    });

    it('renders with default `IconSize::Medium` when no size is set', function (): void {
        $html = Icon::make(Heroicon::Check)->toEmbeddedHtml();
        expect($html)->toContain('fi-size-md');
    });

    it('renders an `Htmlable` icon instead of treating its markup as an image path', function (): void {
        $html = Icon::make(new HtmlString('<svg data-testid="custom-icon"></svg>'))->toEmbeddedHtml();

        expect($html)
            ->toContain('fi-sc-icon-htmlable')
            ->toContain('<svg data-testid="custom-icon"></svg>')
            ->not->toContain('<img src="&lt;svg');
    });

    it('exposes a named built-in icon with an image role while keeping its SVG hidden', function (): void {
        $html = Icon::make(Heroicon::Check)
            ->extraAttributes([
                'aria-label' => 'Verified account',
                'aria-describedby' => 'account-description',
            ])
            ->toEmbeddedHtml();

        expect($html)
            ->toContain('aria-hidden="true"')
            ->toContain('<span aria-describedby="account-description" aria-label="Verified account" role="img" class="fi-sr-only"></span>');
    });

    it('preserves both explicit naming sources and ID references on a named built-in icon', function (): void {
        $html = Icon::make(Heroicon::Check)
            ->extraAttributes([
                'id' => 'verified-icon',
                'aria-label' => 'Verified account',
                'aria-labelledby' => 'verified-icon account-label',
            ])
            ->toEmbeddedHtml();

        expect($html)
            ->toContain('id="verified-icon"')
            ->toContain('aria-label="Verified account"')
            ->toContain('aria-labelledby="verified-icon account-label"')
            ->toContain('<span aria-label="Verified account" aria-labelledby="verified-icon account-label" role="img" class="fi-sr-only"></span>');
    });

    it('uses an `Htmlable` tooltip as the accessible name of a built-in icon', function (): void {
        $html = Icon::make(Heroicon::Check)
            ->tooltip(new HtmlString('<strong>Verified &amp; active</strong>'))
            ->toEmbeddedHtml();

        expect($html)->toContain('aria-label="Verified &amp; active" role="img"');
    });

    it('keeps an unnamed built-in icon decorative', function (): void {
        $html = Icon::make(Heroicon::Check)->toEmbeddedHtml();

        expect($html)
            ->toContain('aria-hidden="true"')
            ->not->toContain('role="img"');
    });

    it('exposes a named `Htmlable` icon once while keeping its SVG wrapper hidden', function (): void {
        $html = Icon::make(new HtmlString('<svg data-testid="custom-icon"></svg>'))
            ->extraAttributes(['aria-label' => 'Custom status'])
            ->toEmbeddedHtml();

        expect($html)
            ->toContain('<span aria-label="Custom status" aria-hidden="true"')
            ->toContain('<span aria-label="Custom status" role="img" class="fi-sr-only"></span>');
    });

    it('keeps an unnamed `Htmlable` icon decorative', function (): void {
        $html = Icon::make(new HtmlString('<svg data-testid="custom-icon"></svg>'))->toEmbeddedHtml();

        expect($html)
            ->toContain('<span aria-hidden="true"')
            ->not->toContain('role="img"');
    });

    it('preserves native image semantics for a named image-path icon', function (): void {
        $html = Icon::make('/icons/status.svg')
            ->extraAttributes(['aria-label' => 'Path status'])
            ->toEmbeddedHtml();

        expect($html)
            ->toContain('<img src="/icons/status.svg"')
            ->toContain('aria-label="Path status"')
            ->not->toContain('fi-sr-only');
    });

    it('preserves `alt` on an image-path icon', function (): void {
        $html = Icon::make('/icons/status.svg')
            ->extraAttributes(['alt' => 'Path status'])
            ->toEmbeddedHtml();

        expect($html)
            ->toContain('alt="Path status"')
            ->not->toContain('fi-sr-only');
    });

    it('uses the tooltip as the accessible name of an otherwise unnamed image-path icon', function (): void {
        $html = Icon::make('/icons/status.svg')
            ->tooltip('Path status')
            ->toEmbeddedHtml();

        expect($html)
            ->toContain('aria-label="Path status"')
            ->not->toContain('fi-sr-only');
    });

    it('keeps an unnamed image-path icon decorative', function (): void {
        $html = Icon::make('/icons/status.svg')->toEmbeddedHtml();

        expect($html)
            ->toContain('alt=""')
            ->not->toContain('fi-sr-only');
    });

    it('escapes accessible names', function (): void {
        $html = Icon::make(Heroicon::Check)
            ->extraAttributes(['aria-label' => 'Verified" autofocus="autofocus'])
            ->toEmbeddedHtml();

        expect($html)
            ->toContain('aria-label="Verified&quot; autofocus=&quot;autofocus"')
            ->not->toContain('aria-label="Verified" autofocus="autofocus"');
    });
});

it('exposes meaningful icons and hides decorative icons in light and dark modes', function (): void {
    $this->actingAs(User::factory()->create());
    Artisan::call('filament:assets');

    $browser = visit('/icon-browser-test')
        ->assertNoSmoke()
        ->assertPresent('[data-testid="built-in-named"][aria-hidden="true"] + [role="img"][aria-label="Verified account"]')
        ->assertPresent('[data-testid="custom-named"][aria-hidden="true"] + [role="img"][aria-label="Custom status"]')
        ->assertPresent('[data-testid="built-in-decorative"][aria-hidden="true"]')
        ->assertPresent('[data-testid="custom-decorative"][aria-hidden="true"]')
        ->assertPresent('[data-testid="custom-tooltip"].fi-sc-icon-htmlable[aria-hidden="true"] + [role="img"][aria-label="Custom warning"]')
        ->assertPresent('img[data-testid="path-named"][aria-label="Path status"]:not([aria-hidden])')
        ->assertPresent('img[data-testid="path-decorative"][alt=""]:not([aria-hidden])')
        ->assertScript('document.querySelectorAll(\'[data-testid="built-in-decorative"] + [role="img"], [data-testid="custom-decorative"] + [role="img"]\').length', 0)
        ->assertScript('document.querySelector(\'[data-testid="custom-tooltip"]\').getBoundingClientRect().width === document.querySelector(\'[data-testid="custom-tooltip"] > svg\').getBoundingClientRect().width', true)
        ->assertNoAccessibilityIssues();

    expect($browser->script('document.querySelectorAll(\'[role="img"][aria-label="Verified account"], [role="img"][aria-label="Custom status"]\').length'))->toBe(2);

    visit('/icon-browser-test')
        ->inDarkMode()
        ->assertNoAccessibilityIssues();
});
