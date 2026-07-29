<?php

use Filament\Support\Csp\CspManager;
use Filament\Tests\TestCase;

use function Filament\Support\csp_nonce;
use function Filament\Support\csp_nonce_html;

uses(TestCase::class);

beforeEach(function (): void {
    $this->manager = new CspManager;
});

describe('nonce', function (): void {
    it('returns `null` for `getNonce()` by default', function (): void {
        expect($this->manager->getNonce())->toBeNull();
    });

    it('can set `useNonce()` with a string', function (): void {
        $this->manager->useNonce('abc123');

        expect($this->manager->getNonce())->toBe('abc123');
    });

    it('can set `useNonce()` with a `Closure`', function (): void {
        $this->manager->useNonce(fn (): string => 'abc123');

        expect($this->manager->getNonce())->toBe('abc123');
    });

    it('can clear `useNonce()` with `null`', function (): void {
        $this->manager->useNonce('abc123');
        $this->manager->useNonce(null);

        expect($this->manager->getNonce())->toBeNull();
    });

    it('resolves the `Closure` only once, so every element receives the same nonce', function (): void {
        $callCount = 0;

        $this->manager->useNonce(function () use (&$callCount): string {
            $callCount++;

            return "nonce-{$callCount}";
        });

        expect($this->manager->getNonce())->toBe('nonce-1');
        expect($this->manager->getNonce())->toBe('nonce-1');
        expect($callCount)->toBe(1);
    });

    it('resolves the `Closure` again after `useNonce()` is called', function (): void {
        $this->manager->useNonce(fn (): string => 'abc123');

        expect($this->manager->getNonce())->toBe('abc123');

        $this->manager->useNonce(fn (): string => 'def456');

        expect($this->manager->getNonce())->toBe('def456');
    });

    it('memoizes a `Closure` that returns `null`', function (): void {
        $callCount = 0;

        $this->manager->useNonce(function () use (&$callCount): ?string {
            $callCount++;

            return null;
        });

        expect($this->manager->getNonce())->toBeNull();
        expect($this->manager->getNonce())->toBeNull();
        expect($callCount)->toBe(1);
    });
});

describe('`hasNonce()`', function (): void {
    it('returns `false` by default', function (): void {
        expect($this->manager->hasNonce())->toBeFalse();
    });

    it('returns `true` when a nonce is configured', function (): void {
        $this->manager->useNonce('abc123');

        expect($this->manager->hasNonce())->toBeTrue();
    });

    it('returns `false` when the `Closure` resolves to an empty string', function (): void {
        $this->manager->useNonce(fn (): string => '');

        expect($this->manager->hasNonce())->toBeFalse();
    });
});

describe('`getNonceHtml()`', function (): void {
    it('returns an empty string when no nonce is configured', function (): void {
        expect((string) $this->manager->getNonceHtml())->toBe('');
    });

    it('returns a `nonce` attribute with a leading space', function (): void {
        $this->manager->useNonce('abc123');

        expect((string) $this->manager->getNonceHtml())->toBe(' nonce="abc123"');
    });

    it('escapes the nonce', function (): void {
        $this->manager->useNonce('abc"><script>alert(1)</script>');

        expect((string) $this->manager->getNonceHtml())
            ->not->toContain('<script>')
            ->toContain('&quot;');
    });
});

describe('helpers', function (): void {
    it('returns `null` from `csp_nonce()` by default', function (): void {
        expect(csp_nonce())->toBeNull();
    });

    it('returns the configured nonce from `csp_nonce()`', function (): void {
        app(CspManager::class)->useNonce('abc123');

        expect(csp_nonce())->toBe('abc123');
    });

    it('returns an empty string from `csp_nonce_html()` by default', function (): void {
        expect((string) csp_nonce_html())->toBe('');
    });

    it('returns the attribute from `csp_nonce_html()`', function (): void {
        app(CspManager::class)->useNonce('abc123');

        expect((string) csp_nonce_html())->toBe(' nonce="abc123"');
    });
});
