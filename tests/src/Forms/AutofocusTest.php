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
            ->assertVisible('input[wire\\:model="data.email"]')
            ->assertScript('document.activeElement === document.querySelector("[autofocus]")', true);
    });
});

it('autofocuses a text input inside tabs', function (): void {
    retry(10, function (): void {
        $this->actingAs(User::factory()->create());

        visit('/autofocus-browser-test')
            ->assertVisible('input[wire\\:model="data.name"]')
            ->assertScript('document.activeElement === document.querySelector("[autofocus]")', true);
    });
});

it('autofocuses a text input inside a wizard step', function (): void {
    retry(10, function (): void {
        $this->actingAs(User::factory()->create());

        visit('/autofocus-wizard-browser-test')
            ->assertVisible('input[wire\\:model="data.name"]')
            ->assertScript('document.activeElement === document.querySelector("[autofocus]")', true);
    });
});

it('only autofocuses a text input after its tab becomes active', function (): void {
    retry(10, function (): void {
        $this->actingAs(User::factory()->create());

        visit('/autofocus-second-tab-browser-test')
            ->assertVisible('input[wire\\:model="data.name"]')
            ->assertScript('document.activeElement === document.querySelector("[autofocus]")', false)
            ->click('.fi-tabs-item >> text=Second Tab')
            ->wait(0.3)
            ->assertScript('document.activeElement === document.querySelector("[autofocus]")', true);
    });
});

it('resets to the first tab and refocuses an `autofocus()` field after `create another`', function (): void {
    retry(10, function (): void {
        $this->actingAs(User::factory()->create());

        visit('/autofocus-after-create-another-tabs-browser-test')
            ->assertVisible('input[wire\\:model="data.name"]')
            ->assertScript('document.activeElement === document.querySelector("[autofocus]")', true)
            ->click('input[wire\\:model="data.email"]')
            ->wait(0.1)
            ->assertScript('document.activeElement === document.querySelector("[autofocus]")', false)
            ->click('[data-testid="simulate-create-another"]')
            ->wait(0.5)
            ->assertScript('document.activeElement === document.querySelector("[autofocus]")', true)
            ->click('.fi-tabs-item >> text=Second Tab')
            ->wait(0.3)
            ->assertScript('document.activeElement === document.querySelector("[autofocus]")', false)
            ->click('[data-testid="simulate-create-another"]')
            ->wait(0.5)
            ->assertScript('document.activeElement === document.querySelector("[autofocus]")', true);
    });
});

it('resets to the first wizard step and refocuses an `autofocus()` field after `create another`', function (): void {
    retry(10, function (): void {
        $this->actingAs(User::factory()->create());

        visit('/autofocus-after-create-another-wizard-browser-test')
            ->assertVisible('input[wire\\:model="data.name"]')
            ->assertScript('document.activeElement === document.querySelector("[autofocus]")', true)
            ->click('input[wire\\:model="data.email"]')
            ->wait(0.1)
            ->assertScript('document.activeElement === document.querySelector("[autofocus]")', false)
            ->click('[data-testid="simulate-create-another"]')
            ->wait(0.5)
            ->assertScript('document.activeElement === document.querySelector("[autofocus]")', true)
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
            ->assertVisible('input[wire\\:model="mountedActions.0.data.name"]')
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

it('refocuses an `autofocus()` field after `create another` is clicked on a `CreateRecord` page', function (): void {
    retry(10, function (): void {
        $author = User::factory()->create();
        $this->actingAs($author);

        visit('/posts/create')
            ->assertVisible('input[wire\\:model="data.title"]')
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
