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

it('resets tabs and form data after `create another` inside a `CreateAction` modal', function (): void {
    $this->actingAs(User::factory()->create());

    visit('/autofocus-after-create-another-tabs-modal-browser-test')
        ->click('[data-testid="open-modal-trigger"]')
        ->assertVisible('input[wire\\:model="mountedActions.0.data.name"]')
        ->assertScript('document.activeElement === document.querySelector("[autofocus]")', true)
        ->fill('input[wire\\:model="mountedActions.0.data.name"]', 'Department')
        ->click('[role="tab"]:has-text("Second Tab")')
        ->assertAttribute('[role="tab"]:has-text("Second Tab")', 'aria-selected', 'true')
        ->assertScript('document.activeElement === document.querySelector("[autofocus]")', false)
        ->click('button >> text=Create & create another')
        ->assertAttribute('[role="tab"]:has-text("First Tab")', 'aria-selected', 'true')
        ->assertVisible('input[wire\\:model="mountedActions.0.data.name"]')
        ->assertValue('input[wire\\:model="mountedActions.0.data.name"]', '');

    $this->assertDatabaseHas('departments', ['name' => 'Department']);
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
