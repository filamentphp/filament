<?php

use Filament\Tests\Fixtures\Models\User;
use Filament\Tests\TestCase;
use Illuminate\Support\Facades\Artisan;

uses(TestCase::class);

beforeEach(function (): void {
    Artisan::call('filament:assets');
});

it('autofocuses a text input', function (): void {
    retry(10, function (): void {
        $this->actingAs(User::factory()->create());

        visit('/autofocus-basic-browser-test')
            ->waitForText('Email')
            ->assertScript('document.activeElement === document.querySelector("[autofocus]")', true);
    });
});

it('autofocuses a text input inside tabs', function (): void {
    retry(10, function (): void {
        $this->actingAs(User::factory()->create());

        visit('/autofocus-browser-test')
            ->waitForText('First Tab')
            ->assertScript('document.activeElement === document.querySelector("[autofocus]")', true);
    });
});

it('autofocuses a text input inside a wizard step', function (): void {
    retry(10, function (): void {
        $this->actingAs(User::factory()->create());

        visit('/autofocus-wizard-browser-test')
            ->waitForText('First Step')
            ->assertScript('document.activeElement === document.querySelector("[autofocus]")', true);
    });
});

it('does not autofocus a text input on an inactive tab', function (): void {
    retry(10, function (): void {
        $this->actingAs(User::factory()->create());

        visit('/autofocus-second-tab-browser-test')
            ->waitForText('First Tab')
            ->assertScript('document.activeElement === document.querySelector("[autofocus]")', false);
    });
});

it('autofocuses a text input when switching to its tab', function (): void {
    retry(10, function (): void {
        $this->actingAs(User::factory()->create());

        visit('/autofocus-second-tab-browser-test')
            ->waitForText('First Tab')
            ->click('.fi-tabs-item >> text=Second Tab')
            ->wait(0.3)
            ->assertScript('document.activeElement === document.querySelector("[autofocus]")', true);
    });
});

it('refocuses an `autofocus()` field after `create another` is clicked on a `CreateRecord` page', function (): void {
    retry(10, function (): void {
        $author = User::factory()->create();
        $this->actingAs($author);

        visit('/posts/create')
            ->waitForText('Title')
            ->assertScript('document.activeElement === document.querySelector("[autofocus]")', true)
            ->fill('input[wire\\:model="data.title"]', 'First post')
            ->fill('input[wire\\:model="data.rating"]', '5')
            ->select('select[wire\\:model="data.author_id"]', (string) $author->getKey())
            ->wait(0.3)
            ->click('input[wire\\:model="data.rating"]')
            ->wait(0.1)
            ->assertScript('document.activeElement === document.querySelector("[autofocus]")', false)
            ->click('button >> text=Create & create another')
            ->wait(0.5)
            ->assertScript('document.activeElement === document.querySelector("[autofocus]")', true);
    });
});
