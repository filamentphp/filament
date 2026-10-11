<?php

use Filament\Tests\Fixtures\Models\User;
use Filament\Tests\Fixtures\Pages\Settings;
use Filament\Tests\TestCase;
use Illuminate\Support\Facades\Vite;

uses(TestCase::class);

beforeEach(function (): void {
    $this->actingAs(User::factory()->create());
});

describe('CSP nonce', function (): void {
    it('does not render a `nonce` attribute by default', function (): void {
        $this->get(Settings::getUrl())
            ->assertSuccessful()
            ->assertDontSee('nonce=', escape: false);
    });

    it('renders the `nonce` on every inline script tag', function (): void {
        Vite::useCspNonce('abc123');

        $html = $this->get(Settings::getUrl())
            ->assertSuccessful()
            ->getContent();

        $scriptTags = [];
        preg_match_all('/<script\b[^>]*>/', $html, $scriptTags);

        $inlineScriptTags = array_filter(
            $scriptTags[0],
            fn (string $scriptTag): bool => ! str_contains($scriptTag, 'src='),
        );

        expect($inlineScriptTags)
            ->not->toBeEmpty()
            ->each->toContain('nonce="abc123"');
    });
});
