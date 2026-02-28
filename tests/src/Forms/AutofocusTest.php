<?php

use Filament\Tests\Fixtures\Models\User;
use Filament\Tests\TestCase;
use Illuminate\Support\Facades\Artisan;

uses(TestCase::class);

beforeEach(function (): void {
    Artisan::call('filament:assets');
});

it('autofocuses a text input', function (): void {
    $this->actingAs(User::factory()->create());

    visit('/autofocus-basic-browser-test')
        ->waitForText('Email')
        ->assertScript('document.activeElement === document.querySelector("[autofocus]")', true);
});

it('autofocuses a text input inside tabs', function (): void {
    $this->actingAs(User::factory()->create());

    visit('/autofocus-browser-test')
        ->waitForText('First Tab')
        ->assertScript('document.activeElement === document.querySelector("[autofocus]")', true);
});

it('does not autofocus a text input on an inactive tab', function (): void {
    $this->actingAs(User::factory()->create());

    visit('/autofocus-second-tab-browser-test')
        ->waitForText('First Tab')
        ->assertScript('document.activeElement === document.querySelector("[autofocus]")', false);
});

it('autofocuses a text input when switching to its tab', function (): void {
    $this->actingAs(User::factory()->create());

    visit('/autofocus-second-tab-browser-test')
        ->waitForText('First Tab')
        ->click('.fi-tabs-item >> text=Second Tab')
        ->wait(0.3)
        ->assertScript('document.activeElement === document.querySelector("[autofocus]")', true);
});

it('autofocuses a text input inside a wizard step', function (): void {
    $this->actingAs(User::factory()->create());

    visit('/autofocus-wizard-browser-test')
        ->waitForText('First Step')
        ->assertScript('document.activeElement === document.querySelector("[autofocus]")', true);
});
