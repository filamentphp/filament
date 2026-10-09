<?php

use Filament\Support\View\Concerns\CanGenerateBadgeHtml;
use Filament\Support\View\Concerns\CanGenerateButtonHtml;
use Filament\Support\View\Concerns\CanGenerateDropdownItemHtml;
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
        use CanGenerateBadgeHtml;
        use CanGenerateButtonHtml;
        use CanGenerateDropdownItemHtml;
        use CanGenerateIconButtonHtml;
        use CanGenerateLinkHtml;
    };
}

it('uses the `fi-disabled` class instead of the `disabled` attribute on loading Blade buttons', function (): void {
    $htmlOutputs = [
        Blade::render('<x-filament::badge tag="button" wire:click="save">Save</x-filament::badge>'),
        Blade::render('<x-filament::button wire:click="save">Save</x-filament::button>'),
        Blade::render('<x-filament::dropdown.list.item wire:click="save">Save</x-filament::dropdown.list.item>'),
        Blade::render('<x-filament::icon-button icon="heroicon-o-pencil" label="Edit" wire:click="edit" />'),
        Blade::render('<x-filament::link tag="button" wire:click="view">View</x-filament::link>'),
    ];

    foreach ($htmlOutputs as $html) {
        expect($html)
            ->toContain('wire:loading.attr="aria-disabled"')
            ->toContain('wire:loading.class="fi-disabled"')
            ->not->toContain('wire:loading.attr="disabled"');
    }
});

it('uses the `fi-disabled` class instead of the `disabled` attribute on loading embedded buttons', function (): void {
    $generator = embeddedHtmlGenerator();

    $htmlOutputs = [
        $generator->generateBadgeHtml(
            attributes: new ComponentAttributeBag(['wire:click' => 'save']),
            label: 'Save',
            tag: 'button',
        ),
        $generator->generateButtonHtml(
            attributes: new ComponentAttributeBag(['wire:click' => 'save']),
            label: 'Save',
        ),
        $generator->generateDropdownItemHtml(
            attributes: new ComponentAttributeBag(['wire:click' => 'save']),
            label: 'Save',
        ),
        $generator->generateIconButtonHtml(
            attributes: new ComponentAttributeBag(['wire:click' => 'edit']),
            icon: 'heroicon-o-pencil',
            label: 'Edit',
        ),
        $generator->generateLinkHtml(
            attributes: new ComponentAttributeBag(['wire:click' => 'view']),
            label: 'View',
        ),
    ];

    foreach ($htmlOutputs as $html) {
        expect($html)
            ->toContain('wire:loading.attr="aria-disabled"')
            ->toContain('wire:loading.class="fi-disabled"')
            ->not->toContain('wire:loading.attr="disabled"');
    }
});

it('renders dropdown items with menu item semantics', function (): void {
    $htmlOutputs = [
        Blade::render('<x-filament::dropdown.list.item disabled>Save</x-filament::dropdown.list.item>'),
        embeddedHtmlGenerator()->generateDropdownItemHtml(
            attributes: new ComponentAttributeBag,
            isDisabled: true,
            label: 'Save',
        ),
    ];

    foreach ($htmlOutputs as $html) {
        expect($html)
            ->toContain('aria-disabled="true"')
            ->toContain('role="menuitem"')
            ->toContain('tabindex="-1"')
            ->not->toContain(' disabled');
    }
});

it('preserves specialized dropdown item roles', function (): void {
    $htmlOutputs = [
        Blade::render('<x-filament::dropdown.list.item role="menuitemradio">Light</x-filament::dropdown.list.item>'),
        embeddedHtmlGenerator()->generateDropdownItemHtml(
            attributes: new ComponentAttributeBag(['role' => 'menuitemradio']),
            label: 'Light',
        ),
    ];

    foreach ($htmlOutputs as $html) {
        expect($html)->toContain('role="menuitemradio"');
    }
});

it('preserves custom loading classes on Blade buttons', function (): void {
    $htmlOutputs = [
        Blade::render('<x-filament::badge tag="button" wire:click="save" wire:loading.class="custom-loading">Save</x-filament::badge>'),
        Blade::render('<x-filament::button wire:click="save" wire:loading.class="custom-loading">Save</x-filament::button>'),
        Blade::render('<x-filament::dropdown.list.item wire:click="save" wire:loading.class="custom-loading">Save</x-filament::dropdown.list.item>'),
        Blade::render('<x-filament::icon-button icon="heroicon-o-pencil" label="Edit" wire:click="edit" wire:loading.class="custom-loading" />'),
        Blade::render('<x-filament::link tag="button" wire:click="view" wire:loading.class="custom-loading">View</x-filament::link>'),
    ];

    foreach ($htmlOutputs as $html) {
        expect($html)->toContain('wire:loading.class="fi-disabled custom-loading"');
    }
});

it('preserves custom loading classes on embedded buttons', function (): void {
    $generator = embeddedHtmlGenerator();
    $attributes = new ComponentAttributeBag([
        'wire:click' => 'save',
        'wire:loading.class' => 'custom-loading',
    ]);

    $htmlOutputs = [
        $generator->generateBadgeHtml(attributes: $attributes, label: 'Save', tag: 'button'),
        $generator->generateButtonHtml(attributes: $attributes, label: 'Save'),
        $generator->generateDropdownItemHtml(attributes: $attributes, label: 'Save'),
        $generator->generateIconButtonHtml(attributes: $attributes, icon: 'heroicon-o-pencil', label: 'Edit'),
        $generator->generateLinkHtml(attributes: $attributes, label: 'View'),
    ];

    foreach ($htmlOutputs as $html) {
        expect($html)->toContain('wire:loading.class="fi-disabled custom-loading"');
    }
});

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

it('traps focus on a modal window when it owns scrolling', function (): void {
    $html = Blade::render(<<<'BLADE'
        <x-filament::modal heading="Agreement" sticky-header>Content</x-filament::modal>
        BLADE);

    preg_match('/<div(?=[^>]*\bfi-modal-window-ctn\b)[^>]*>/', $html, $modalWindowContainerMatches);
    preg_match('/<(?:div|form)(?=[^>]*\bfi-modal-window\b)(?![^>]*\bfi-modal-window-ctn\b)[^>]*>/', $html, $modalWindowMatches);

    expect($modalWindowContainerMatches[0])
        ->not->toContain('x-trap')
        ->and($modalWindowMatches[0])
        ->toContain('tabindex="0"')
        ->toContain('x-trap.noreturn');
});

it('preserves explicit modal window focus attributes', function (): void {
    $html = Blade::render(<<<'BLADE'
        <x-filament::modal
            :extra-modal-window-attribute-bag="$modalWindowAttributes"
            heading="Agreement"
            sticky-header
        >
            Content
        </x-filament::modal>
        BLADE, [
        'modalWindowAttributes' => new Filament\Support\View\ComponentAttributeBag([
            'tabindex' => '-1',
        ]),
    ]);

    preg_match('/<div(?=[^>]*\bfi-modal-window-ctn\b)[^>]*>/', $html, $modalWindowContainerMatches);
    preg_match('/<(?:div|form)(?=[^>]*\bfi-modal-window\b)(?![^>]*\bfi-modal-window-ctn\b)[^>]*>/', $html, $modalWindowMatches);

    expect($modalWindowContainerMatches[0])
        ->toContain('x-trap.noreturn')
        ->and($modalWindowMatches[0])
        ->toContain('tabindex="-1"')
        ->not->toContain('x-trap');
});
