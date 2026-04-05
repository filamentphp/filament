<?php

use Filament\Support\Icons\Heroicon;
use Filament\Support\Icons\IconManager;
use Filament\Tests\TestCase;
use Illuminate\Support\HtmlString;

uses(TestCase::class);

beforeEach(function (): void {
    $this->manager = new IconManager;
});

describe('registering icons', function (): void {
    it('can register a string icon alias', function (): void {
        $this->manager->register([
            'panels::sidebar.collapse-button' => 'heroicon-o-chevron-left',
        ]);

        expect($this->manager->resolve('panels::sidebar.collapse-button'))->toBe('heroicon-o-chevron-left');
    });

    it('can register a `BackedEnum` icon alias', function (): void {
        $this->manager->register([
            'panels::sidebar.collapse-button' => Heroicon::ChevronLeft,
        ]);

        expect($this->manager->resolve('panels::sidebar.collapse-button'))->toBe(Heroicon::ChevronLeft);
    });

    it('can register an `Htmlable` icon alias', function (): void {
        $htmlable = new HtmlString('<svg>custom</svg>');

        $this->manager->register([
            'custom-icon' => $htmlable,
        ]);

        expect($this->manager->resolve('custom-icon'))->toBe($htmlable);
    });

    it('can register multiple icons at once', function (): void {
        $this->manager->register([
            'icon-a' => 'heroicon-o-check',
            'icon-b' => 'heroicon-o-x-mark',
        ]);

        expect($this->manager->resolve('icon-a'))->toBe('heroicon-o-check');
        expect($this->manager->resolve('icon-b'))->toBe('heroicon-o-x-mark');
    });

    it('merges icons across multiple `register()` calls', function (): void {
        $this->manager->register(['icon-a' => 'heroicon-o-check']);
        $this->manager->register(['icon-b' => 'heroicon-o-x-mark']);

        expect($this->manager->resolve('icon-a'))->toBe('heroicon-o-check');
        expect($this->manager->resolve('icon-b'))->toBe('heroicon-o-x-mark');
    });

    it('overwrites existing alias on re-registration', function (): void {
        $this->manager->register(['icon' => 'heroicon-o-check']);
        $this->manager->register(['icon' => 'heroicon-o-x-mark']);

        expect($this->manager->resolve('icon'))->toBe('heroicon-o-x-mark');
    });
});

describe('resolving icons', function (): void {
    it('returns `null` for unregistered alias', function (): void {
        expect($this->manager->resolve('nonexistent'))->toBeNull();
    });

    it('can resolve from an array of aliases, returning the first match', function (): void {
        $this->manager->register([
            'icon-b' => 'heroicon-o-star',
        ]);

        $result = $this->manager->resolve(['icon-a', 'icon-b', 'icon-c']);

        expect($result)->toBe('heroicon-o-star');
    });

    it('returns `null` when no alias in the array matches', function (): void {
        $result = $this->manager->resolve(['nonexistent-a', 'nonexistent-b']);

        expect($result)->toBeNull();
    });
});
