<?php

use Filament\Tests\Fixtures\Models\Department;
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
        ->assertPresent('input[wire\\:model="data.email"]:focus');
});

it('autofocuses a text input inside tabs', function (): void {
    $this->actingAs(User::factory()->create());

    visit('/autofocus-browser-test')
        ->assertPresent('input[wire\\:model="data.name"]:focus');
});

it('autofocuses a text input inside a wizard step', function (): void {
    $this->actingAs(User::factory()->create());

    visit('/autofocus-wizard-browser-test')
        ->assertPresent('input[wire\\:model="data.name"]:focus');
});

it('only autofocuses a text input after its tab becomes active', function (): void {
    $this->actingAs(User::factory()->create());

    visit('/autofocus-second-tab-browser-test')
        ->assertVisible('input[wire\\:model="data.name"]')
        ->assertNotPresent('[autofocus]:focus')
        ->click('[role="tab"]:nth-child(2)')
        ->assertPresent('[autofocus]:focus');
});

it('resets the form and active tab after `create another` inside a `CreateAction` modal', function (): void {
    $this->actingAs(User::factory()->create());

    $page = visit('/autofocus-after-create-another-tabs-modal-browser-test');

    foreach ([$page, $page->inDarkMode()] as $themedPage) {
        $themedPage
            ->click('[data-testid="open-modal-trigger"]')
            ->assertVisible('input[wire\\:model="mountedActions.0.data.name"]');

        foreach (['Engineering', 'Operations'] as $departmentName) {
            $themedPage
                ->fill('input[wire\\:model="mountedActions.0.data.name"]', $departmentName)
                ->click('[role="tab"]:nth-child(2)')
                ->assertAttribute('[role="tab"]:nth-child(2)', 'aria-selected', 'true')
                ->click('button >> text=Create & create another')
                ->assertAttribute('[role="tab"]:nth-child(1)', 'aria-selected', 'true')
                ->assertVisible('input[wire\\:model="mountedActions.0.data.name"]')
                ->assertValue('input[wire\\:model="mountedActions.0.data.name"]', '');
        }

        $themedPage->assertNoAccessibilityIssues();
    }

    expect(Department::query()->orderBy('id')->pluck('name')->all())->toBe(['Engineering', 'Operations', 'Engineering', 'Operations']);
});

it('refocuses an `autofocus()` field after `create another` is clicked on a `CreateRecord` page', function (): void {
    $author = User::factory()->create();
    $this->actingAs($author);

    visit('/posts/create')
        ->assertPresent('input[wire\\:model="data.title"]:focus')
        ->fill('input[wire\\:model="data.title"]', 'First post')
        ->fill('input[wire\\:model="data.rating"]', '5')
        ->select('select[wire\\:model="data.author_id"]', (string) $author->getKey())
        ->click('input[wire\\:model="data.rating"]')
        ->assertPresent('input[wire\\:model="data.rating"]:focus')
        ->click('button >> text=Create & create another')
        ->assertValue('input[wire\\:model="data.title"]', '')
        ->assertPresent('input[wire\\:model="data.title"]:focus');
});
