<?php

use Filament\Support\View\Concerns\CanGenerateBadgeHtml;
use Filament\Support\View\Concerns\CanGenerateButtonHtml;
use Filament\Support\View\Concerns\CanGenerateDropdownItemHtml;
use Filament\Support\View\Concerns\CanGenerateIconButtonHtml;
use Filament\Support\View\Concerns\CanGenerateLinkHtml;
use Filament\Tests\Fixtures\Models\User;
use Filament\Tests\TestCase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Str;
use Illuminate\View\ComponentAttributeBag;

uses(TestCase::class);

function embeddedHtmlGenerator(): object
{
    return new class
    {
        use CanGenerateBadgeHtml;
        use CanGenerateButtonHtml;
        use CanGenerateDropdownItemHtml;
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

it('preserves loading targets in shared embedded component rendering', function (string $method): void {
    $generator = embeddedHtmlGenerator();
    $attributes = new ComponentAttributeBag(['wire:target' => ' ', 'wire:click.prevent' => 'save', 'title' => 'Save']);
    $html = $generator->{$method}($attributes, label: 'Save');

    expect($html)->toContain('wire:target="save"')
        ->toContain('fi-loading-indicator');

    $withoutIndicator = $generator->{$method}($attributes, label: 'Save', hasLoadingIndicator: false);
    expect($withoutIndicator)->not->toContain('fi-loading-indicator');
})->with([
    'button' => 'generateButtonHtml',
    'badge' => 'generateBadgeHtml',
    'icon button' => 'generateIconButtonHtml',
    'link' => 'generateLinkHtml',
    'dropdown item' => 'generateDropdownItemHtml',
]);

it('preserves submit-form loading fallback in shared embedded component rendering', function (string $method): void {
    $html = embeddedHtmlGenerator()->{$method}(new ComponentAttributeBag, label: 'Save', type: 'submit', form: 'save');

    expect($html)->toContain('fi-loading-indicator');
})->with(['generateButtonHtml', 'generateBadgeHtml', 'generateIconButtonHtml', 'generateLinkHtml']);

it('keeps form submission and loading controls accessible in the browser', function (): void {
    $this->actingAs(User::factory()->create());
    Artisan::call('filament:assets');

    visit('/text-input-test')
        ->type('[data-testid="text-input"] input', 'Ada Lovelace')
        ->click('Save')
        ->assertSee('Name')
        ->assertNoSmoke()
        ->assertNoAccessibilityIssues();

    visit('/text-input-test')
        ->inDarkMode()
        ->type('[data-testid="text-input"] input', 'Ada Lovelace')
        ->click('Save')
        ->assertSee('Name')
        ->assertNoSmoke()
        ->assertNoAccessibilityIssues();
});
