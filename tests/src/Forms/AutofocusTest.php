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

it('refocuses an `autofocus()` field on the active tab after `create another`', function (): void {
    retry(10, function (): void {
        $this->actingAs(User::factory()->create());

        visit('/autofocus-after-create-another-tabs-browser-test')
            ->waitForText('First Tab')
            ->assertScript('document.activeElement === document.querySelector("[autofocus]")', true)
            ->click('input[wire\\:model="data.email"]')
            ->wait(0.1)
            ->assertScript('document.activeElement === document.querySelector("[autofocus]")', false)
            ->click('[data-testid="simulate-create-another"]')
            ->wait(0.5)
            ->assertScript('document.activeElement === document.querySelector("[autofocus]")', true);
    });
});

it('refocuses an `autofocus()` field on the active wizard step after `create another`', function (): void {
    retry(10, function (): void {
        $this->actingAs(User::factory()->create());

        visit('/autofocus-after-create-another-wizard-browser-test')
            ->waitForText('First Step')
            ->assertScript('document.activeElement === document.querySelector("[autofocus]")', true)
            ->click('input[wire\\:model="data.email"]')
            ->wait(0.1)
            ->assertScript('document.activeElement === document.querySelector("[autofocus]")', false)
            ->click('[data-testid="simulate-create-another"]')
            ->wait(0.5)
            ->assertScript('document.activeElement === document.querySelector("[autofocus]")', true);
    });
});

it('resets to the first wizard step and refocuses an `autofocus()` field after `create another` when user navigated to a later step', function (): void {
    retry(10, function (): void {
        $this->actingAs(User::factory()->create());

        visit('/autofocus-after-create-another-wizard-browser-test')
            ->waitForText('First Step')
            ->click('button >> text=Next')
            ->wait(0.3)
            ->assertScript('document.activeElement === document.querySelector("[autofocus]")', false)
            ->click('[data-testid="simulate-create-another"]')
            ->wait(0.5)
            ->assertScript('document.activeElement === document.querySelector("[autofocus]")', true);
    });
});

it('refocuses an `autofocus()` field after `create another` inside a `CreateAction` modal that contains tabs', function (): void {
    retry(10, function (): void {
        $this->actingAs(User::factory()->create());

        visit('/autofocus-after-create-another-tabs-modal-browser-test')
            ->click('[data-testid="open-modal-trigger"]')
            ->waitForText('First Tab')
            ->wait(0.3)
            ->assertScript('document.activeElement === document.querySelector("[autofocus]")', true)
            ->fill('input[wire\\:model="mountedActions.0.data.name"]', 'Department')
            ->click('.fi-tabs-item >> text=Second Tab')
            ->wait(0.3)
            ->assertScript('document.activeElement === document.querySelector("[autofocus]")', false)
            ->click('button >> text=Create & create another')
            ->wait(1.0)
            ->assertScript('document.activeElement === document.querySelector("[autofocus]")', true);
    });
});

it('resets to the first tab and refocuses an `autofocus()` field after `create another` when user navigated to a different tab', function (): void {
    retry(10, function (): void {
        $this->actingAs(User::factory()->create());

        visit('/autofocus-after-create-another-tabs-browser-test')
            ->waitForText('First Tab')
            ->click('.fi-tabs-item >> text=Second Tab')
            ->wait(0.3)
            ->assertScript('document.activeElement === document.querySelector("[autofocus]")', false)
            ->click('[data-testid="simulate-create-another"]')
            ->wait(0.5)
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
