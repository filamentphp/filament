<?php

use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\RichEditor\StateCasts\RichEditorStateCast;
use Filament\Schemas\Schema;
use Filament\Tests\Fixtures\Forms\RichEditor\MinimalControlsCalloutBlock;
use Filament\Tests\Fixtures\Forms\RichEditor\MinimalControlsDividerBlock;
use Filament\Tests\Fixtures\Livewire\Livewire;
use Filament\Tests\Fixtures\Models\User;
use Filament\Tests\Fixtures\Pages\RichEditorMinimalControlsBrowserTest;
use Filament\Tests\TestCase;
use Illuminate\Support\Facades\Artisan;

uses(TestCase::class);

it('keeps `minimalCustomBlockControls()` opt-in and evaluates and resets its condition', function (): void {
    $richEditor = RichEditor::make('content')
        ->container(Schema::make(Livewire::make())->statePath('data'));

    expect($richEditor->hasMinimalCustomBlockControls())->toBeFalse();

    $richEditor->minimalCustomBlockControls();

    expect($richEditor->hasMinimalCustomBlockControls())->toBeTrue();

    $richEditor->minimalCustomBlockControls(static fn (): bool => false);

    expect($richEditor->hasMinimalCustomBlockControls())->toBeFalse();

    $richEditor->minimalCustomBlockControls(static fn (): bool => true);

    expect($richEditor->hasMinimalCustomBlockControls())->toBeTrue();

    $richEditor->minimalCustomBlockControls(false);

    expect($richEditor->hasMinimalCustomBlockControls())->toBeFalse();
});

it('does not change saved content when using `minimalCustomBlockControls()`', function (bool $isJson): void {
    $richEditor = RichEditor::make('content')
        ->json($isJson)
        ->customBlocks([MinimalControlsCalloutBlock::class, MinimalControlsDividerBlock::class])
        ->container(Schema::make(Livewire::make())->statePath('data'));
    $stateCast = new RichEditorStateCast($richEditor);
    $document = RichEditorMinimalControlsBrowserTest::getDocument();
    $defaultContent = $stateCast->get($stateCast->set($document));

    $richEditor->minimalCustomBlockControls();

    $minimalContent = $stateCast->get($stateCast->set($document));

    expect($minimalContent)->toEqual($defaultContent)
        ->and(json_encode($minimalContent))->not->toContain('minimal', 'preview', 'label');
})->with([
    'JSON' => true,
    'HTML' => false,
]);

it('can edit, delete and undo minimal custom blocks while preserving accessible fallbacks', function (bool $isMobile, bool $isDark): void {
    Artisan::call('filament:assets');
    $this->actingAs(User::factory()->create());

    $page = visit('/rich-editor-minimal-controls-browser-test');

    if ($isMobile) {
        $page = $page->on()->mobile();
    }

    if ($isDark) {
        $page = $page->inDarkMode();
    }

    $minimalEditor = '[data-testid="minimal-controls-editor"]';
    $firstCallout = $minimalEditor . ' [data-type="customBlock"]:has(.fi-fo-rich-editor-custom-block-preview:has-text("First callout."))';
    $secondCallout = $minimalEditor . ' [data-type="customBlock"]:has(.fi-fo-rich-editor-custom-block-preview:has-text("Second callout."))';

    $page
        ->assertPresent($minimalEditor . ' .tiptap')
        ->assertPresent($firstCallout . '.fi-fo-rich-editor-custom-block-minimal')
        ->assertVisible($firstCallout . ' button[aria-label="Edit block"]')
        ->assertVisible($firstCallout . ' button[aria-label="Delete block"]')
        ->assertNotPresent($minimalEditor . ' [data-id="divider"].fi-fo-rich-editor-custom-block-minimal')
        ->assertVisible($minimalEditor . ' [data-id="divider"] .fi-fo-rich-editor-custom-block-heading')
        ->assertNotPresent('[data-testid="default-controls-editor"] .fi-fo-rich-editor-custom-block-minimal')
        ->assertVisible('[data-testid="default-controls-editor"] [data-id="callout"] .fi-fo-rich-editor-custom-block-heading >> nth=0')
        ->assertNotPresent('[data-testid="disabled-controls-editor"] [data-type="customBlock"] button')
        ->assertSeeIn('[data-testid="disabled-controls-editor"]', 'First callout.')
        ->assertScript(<<<'JS'
            (() => {
                const block = document.querySelector('[data-testid="minimal-controls-editor"] .fi-fo-rich-editor-custom-block-minimal')
                const heading = block.querySelector('.fi-fo-rich-editor-custom-block-heading')
                const button = block.querySelector('button')
                const buttonBounds = button.getBoundingClientRect()
                const deleteButtonBounds = block.querySelector('button[aria-label="Delete block"]').getBoundingClientRect()
                const blockBounds = block.getBoundingClientRect()
                const headerBounds = block.querySelector('.fi-fo-rich-editor-custom-block-header').getBoundingClientRect()
                const previewBounds = block.querySelector('.fi-fo-rich-editor-custom-block-preview').getBoundingClientRect()
                const disabledBlock = document.querySelector('[data-testid="disabled-controls-editor"] .fi-fo-rich-editor-custom-block-minimal')
                const disabledPreviewBounds = disabledBlock.querySelector('.fi-fo-rich-editor-custom-block-preview').getBoundingClientRect()

                return heading.getBoundingClientRect().width <= 1 &&
                    button.tabIndex === 0 &&
                    buttonBounds.top >= blockBounds.top &&
                    buttonBounds.top < blockBounds.bottom &&
                    buttonBounds.right <= deleteButtonBounds.left &&
                    Math.abs(buttonBounds.top - deleteButtonBounds.top) < 1 &&
                    headerBounds.width <= 92 &&
                    Math.abs(previewBounds.top - blockBounds.top) < 1 &&
                    previewBounds.right <= headerBounds.left &&
                    Math.abs(disabledPreviewBounds.width - disabledBlock.getBoundingClientRect().width) < 1
            })()
            JS)
        ->assertNoAccessibilityIssues()
        ->assertScript(<<<'JS'
            (() => {
                const editor = Alpine.$data(document.querySelector('[data-testid="minimal-controls-editor"] .tiptap')).$getEditor()
                editor.commands.focus()
                editor.commands.setTextSelection(editor.state.doc.content.size - 1)
                return true
            })()
            JS)
        ->keys($secondCallout . ' button[aria-label="Edit block"]', 'Enter')
        ->assertVisible('.fi-modal-window input')
        ->fill('.fi-modal-window input', 'Updated second callout.')
        ->click('.fi-modal-footer-actions button:has-text("Save")')
        ->assertSeeIn($minimalEditor, 'Updated second callout.')
        ->assertSeeIn($minimalEditor, 'First callout.')
        ->assertScript(<<<'JS'
            (() => {
                const editor = Alpine.$data(document.querySelector('[data-testid="minimal-controls-editor"] .tiptap')).$getEditor()
                return editor.getJSON().content.filter(node => node.type === 'customBlock' && node.attrs.id === 'callout').map(node => node.attrs.config.message)
            })()
            JS, ['First callout.', 'Updated second callout.'])
        // Keep the deletion outside TipTap's history grouping interval for the edit.
        ->wait(0.6)
        ->click($firstCallout . ' button[aria-label="Delete block"]')
        ->assertDontSeeIn($minimalEditor, 'First callout.')
        ->assertSeeIn($minimalEditor, 'Updated second callout.')
        ->click($minimalEditor . ' button[aria-label="Undo"]')
        ->assertSeeIn($minimalEditor, 'First callout.')
        ->assertSeeIn($minimalEditor, 'Updated second callout.')
        ->assertNoAccessibilityIssues();
})->with([
    'desktop in light mode' => [false, false],
    'mobile in dark mode' => [true, true],
]);
