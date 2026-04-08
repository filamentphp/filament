<?php

use Filament\Support\TimezoneManager;
use Filament\Tests\TestCase;

uses(TestCase::class);

beforeEach(function (): void {
    $this->manager = new TimezoneManager;
});

it('returns the app timezone by default', function (): void {
    expect($this->manager->get())->toBe(config('app.timezone'));
});

it('can set a timezone', function (): void {
    $this->manager->set('America/New_York');

    expect($this->manager->get())->toBe('America/New_York');
});

it('can set a timezone with a `Closure`', function (): void {
    $this->manager->set(static fn (): string => 'Europe/London');

    expect($this->manager->get())->toBe('Europe/London');
});

it('can clear timezone with `null` to fall back to app timezone', function (): void {
    $this->manager->set('Asia/Tokyo');
    $this->manager->set(null);

    expect($this->manager->get())->toBe(config('app.timezone'));
});
