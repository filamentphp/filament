<?php

use Filament\Tests\TestCase;
use Illuminate\Support\Facades\Blade;

uses(TestCase::class);

/*
/ Default behavior tests
*/
it('renders script tags without extra attributes', function (): void {
    $html = Blade::render('@filamentScripts');

    preg_match_all('/<script[\s\S]*?>/m', $html, $matches);

    expect($matches[0])->not->toBeEmpty()
        ->and($html)->not->toContain('data-cfasync');
});

/*
/ Inline attributes tests
*/

it('applies inline attributes to every rendered script tag', function (): void {
    $html         = Blade::render("@filamentScripts(attributes: ['data-cfasync' => 'false'])");
    $htmlWithout  = Blade::render('@filamentScripts');

    preg_match_all('/<script[\s\S]*?src=[\s\S]*?>/m', $html, $withMatches);
    preg_match_all('/<script[\s\S]*?src=[\s\S]*?>/m', $htmlWithout, $withoutMatches);

    expect($withMatches[0])->toHaveCount(count($withoutMatches[0]));

    foreach ($withMatches[0] as $tag) {
        expect($tag)->toContain('data-cfasync="false"');
    }
});

/*
/ Keyed vs non-keyed attributes tests
*/

it('renders integer-keyed attributes as normal attributes, not integer keys like 0="async"', function (): void {
    $html = Blade::render("@filamentScripts(attributes: ['data-cfasync' => 'false', 'async'])");

    expect($html)
        ->toContain('data-cfasync="false"')
        ->toContain(' async')
        ->not->toContain('0="async"')
        ->not->toContain('="async"');
});

/*
/ FilamentData script block tests
*/

it('applies inline attributes to the filamentData script block', function (): void {
    $html = Blade::render("@filamentScripts(attributes: ['data-cfasync' => 'false'])");

    expect($html)
        ->toMatch('/<script[^>]*data-cfasync="false"[^>]*>\s*window\.filamentData/');
});

it('applies global config attributes to the filamentData script block', function (): void {
    config(['filament.scripts.attributes' => ['data-cfasync' => 'false']]);

    $html = Blade::render('@filamentScripts');

    expect($html)
        ->toMatch('/<script[^>]*data-cfasync="false"[^>]*>\s*window\.filamentData/');
});

it('applies inline attributes to both the filamentData block and all src script tags', function (): void {
    $html = Blade::render("@filamentScripts(attributes: ['data-cfasync' => 'false'])");

    preg_match_all('/<script[\s\S]*?data-cfasync="false"[\s\S]*?>/m', $html, $matches);

    expect(count($matches[0]))->toBeGreaterThanOrEqual(2);
});

/*
/ Global config attributes tests
*/

it('global config attributes apply to every script tag', function (): void {
    config(['filament.scripts.attributes' => ['data-cfasync' => 'false']]);

    $html = Blade::render('@filamentScripts');

    preg_match_all('/<script[\s\S]*?src=[\s\S]*?>/m', $html, $matches);

    expect($matches[0])->not->toBeEmpty();

    foreach ($matches[0] as $tag) {
        expect($tag)->toContain('data-cfasync="false"');
    }
});

/*
/ Overriding and merging attributes tests
*/

it('inline attributes override global config attributes for the same key', function (): void {
    config(['filament.scripts.attributes' => ['data-cfasync' => 'false']]);

    $html = Blade::render("@filamentScripts(attributes: ['data-cfasync' => 'true'])");

    expect($html)
        ->toContain('data-cfasync="true"')
        ->not->toContain('data-cfasync="false"');
});

it('merges global config attributes with inline attributes', function (): void {
    config(['filament.scripts.attributes' => ['data-cfasync' => 'false']]);

    $html = Blade::render("@filamentScripts(attributes: ['crossorigin' => 'anonymous'])");

    expect($html)
        ->toContain('data-cfasync="false"')
        ->toContain('crossorigin="anonymous"');
});
