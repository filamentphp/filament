<?php

use Filament\Forms\Components\RichEditor;
use Filament\Schemas\Schema;
use Filament\Tests\Fixtures\Livewire\Livewire;
use Filament\Tests\Fixtures\Models\User;
use Filament\Tests\TestCase;
use Illuminate\Support\Facades\Artisan;

uses(TestCase::class);

it('keeps sidebar enhancements opt-in and evaluates their conditions', function (string $setter, string $getter): void {
    $richEditor = RichEditor::make('content')
        ->container(Schema::make(Livewire::make())->statePath('data'));

    expect($richEditor->{$getter}())->toBeFalse();

    $richEditor->{$setter}();

    expect($richEditor->{$getter}())->toBeTrue();

    $richEditor->{$setter}(static fn (): bool => false);

    expect($richEditor->{$getter}())->toBeFalse();

    $richEditor->{$setter}(static fn (): bool => true);

    expect($richEditor->{$getter}())->toBeTrue();

    $richEditor->{$setter}(false);

    expect($richEditor->{$getter}())->toBeFalse();
})->with([
    '`customBlocksGrid()`' => ['customBlocksGrid', 'hasCustomBlocksGrid'],
    '`searchableCustomBlocks()`' => ['searchableCustomBlocks', 'hasSearchableCustomBlocks'],
    '`stickyToolbar()`' => ['stickyToolbar', 'hasStickyToolbar'],
    '`stickyPanels()`' => ['stickyPanels', 'hasStickyPanels'],
]);

it('can evaluate and clear `stickyOffset()` without enabling sticky controls', function (): void {
    $richEditor = RichEditor::make('content')
        ->container(Schema::make(Livewire::make())->statePath('data'));

    expect($richEditor->getStickyOffset())->toBeNull();

    $richEditor->stickyOffset(static fn (): string => 'calc(4rem + 1px)');

    expect($richEditor->getStickyOffset())->toBe('calc(4rem + 1px)')
        ->and($richEditor->hasStickyToolbar())->toBeFalse()
        ->and($richEditor->hasStickyPanels())->toBeFalse();

    $richEditor->stickyOffset(null);

    expect($richEditor->getStickyOffset())->toBeNull();
});

it('can search labels and groups and insert a block at the preserved editor selection', function (bool $hasGrid, bool $isDark): void {
    Artisan::call('filament:assets');
    $this->actingAs(User::factory()->create());

    $page = visit('/rich-editor-sidebar-browser-test?grid=' . (int) $hasGrid);

    if ($isDark) {
        $page = $page->inDarkMode();
    }

    $page
        ->assertPresent('[data-testid="sidebar-rich-editor"] .tiptap')
        ->assertScript(<<<'JS'
            (() => {
                const buttons = [...document.querySelectorAll('.fi-fo-rich-editor-custom-block-btn')]
                const quote = buttons.find(button => button.textContent.trim() === 'Quote')
                const image = buttons.find(button => button.textContent.trim() === 'Image')
                const section = buttons.find(button => button.textContent.trim() === 'Section')

                return !!quote?.querySelector('svg:not(.fi-loading-indicator)') &&
                    !!image?.querySelector('svg:not(.fi-loading-indicator)') &&
                    !!section && !section.querySelector('svg:not(.fi-loading-indicator)')
            })()
            JS)
        ->assertScript('getComputedStyle(document.querySelector(".fi-fo-rich-editor-custom-blocks-list")).gridTemplateColumns.split(" ").length', $hasGrid ? 2 : 1)
        ->fill('.fi-fo-rich-editor-custom-blocks-search-input', '  eDiToRiAl  ')
        ->assertVisible('.fi-fo-rich-editor-custom-block-btn:has-text("Quote")')
        ->assertVisible('.fi-fo-rich-editor-custom-block-btn:has-text("Section")')
        ->assertMissing('.fi-fo-rich-editor-custom-block-btn:has-text("Image")')
        ->fill('.fi-fo-rich-editor-custom-blocks-search-input', 'unknown block')
        ->assertVisible('.fi-fo-rich-editor-custom-blocks-ctn [role="status"]')
        ->assertMissing('.fi-fo-rich-editor-custom-block-btn:has-text("Quote")')
        ->assertNoAccessibilityIssues()
        ->fill('.fi-fo-rich-editor-custom-blocks-search-input', '')
        ->assertVisible('.fi-fo-rich-editor-custom-block-btn:has-text("Image")')
        ->assertScript(<<<'JS'
            (() => {
                const editor = Alpine.$data(document.querySelector('[data-testid="sidebar-rich-editor"] .tiptap')).$getEditor()
                editor.commands.focus()
                editor.commands.setTextSelection(editor.state.doc.firstChild.nodeSize - 1)
                return true
            })()
            JS)
        ->fill('.fi-fo-rich-editor-custom-blocks-search-input', '  QuOtE  ')
        ->assertVisible('.fi-fo-rich-editor-custom-block-btn:has-text("Quote")')
        ->assertMissing('.fi-fo-rich-editor-custom-block-btn:has-text("Section")')
        ->click('.fi-fo-rich-editor-custom-block-btn:has-text("Quote")')
        ->assertPresent('.tiptap [data-type="customBlock"]')
        ->assertScript(<<<'JS'
            (() => {
                const editor = Alpine.$data(document.querySelector('[data-testid="sidebar-rich-editor"] .tiptap')).$getEditor()
                const content = editor.getJSON().content
                const blockPosition = content.findIndex(node => node.type === 'customBlock')
                const lastParagraphPosition = content.findIndex(node => node.content?.[0]?.text === 'Last paragraph.')

                return blockPosition > 0 && blockPosition < lastParagraphPosition &&
                    content[blockPosition].attrs.id === 'quote' &&
                    content[0].content[0].text === 'First paragraph.'
            })()
            JS)
        ->assertNoAccessibilityIssues();
})->with([
    'list in light mode' => [false, false],
    'grid in dark mode' => [true, true],
]);

it('keeps the toolbar visible while scrolling with responsive sticky panels', function (bool $isMobile, bool $isDark): void {
    Artisan::call('filament:assets');
    $this->actingAs(User::factory()->create());

    $page = visit('/rich-editor-sidebar-browser-test?grid=1');

    if ($isMobile) {
        $page = $page->on()->mobile();
    }

    if ($isDark) {
        $page = $page->inDarkMode();
    }

    $page
        ->assertPresent('[data-testid="sidebar-rich-editor"] .tiptap')
        ->assertScript(<<<'JS'
            (() => {
                const wrapper = document.querySelector('[data-testid="sidebar-rich-editor"]')
                const editor = Alpine.$data(wrapper.querySelector('.tiptap')).$getEditor()
                editor.commands.setContent('<p>A paragraph in a long document.</p>'.repeat(60))
                window.scrollTo(0, wrapper.getBoundingClientRect().top + window.scrollY + 300)
                return true
            })()
            JS)
        ->assertScript(<<<'JS'
            (() => {
                const toolbar = document.querySelector('[data-testid="sidebar-rich-editor"] .fi-fo-rich-editor-toolbar')
                return getComputedStyle(toolbar).position === 'sticky' &&
                    Math.abs(toolbar.getBoundingClientRect().top - 64) < 2
            })()
            JS)
        ->assertScript('getComputedStyle(document.querySelector("[data-testid=sidebar-rich-editor] .fi-fo-rich-editor-panels")).position', $isMobile ? 'static' : 'sticky')
        ->assertNoAccessibilityIssues();
})->with([
    'desktop in light mode' => [false, false],
    'mobile in dark mode' => [true, true],
]);

it('can keep the toolbar visible inside a scrolling action modal', function (bool $isDark): void {
    Artisan::call('filament:assets');
    $this->actingAs(User::factory()->create());

    $page = visit('/rich-editor-sidebar-browser-test');

    if ($isDark) {
        $page = $page->inDarkMode();
    }

    $page
        ->click('Edit in modal')
        ->assertPresent('[data-testid="modal-rich-editor"] .tiptap')
        // Allow the modal's entrance autofocus to finish before scrolling.
        ->wait(0.5)
        ->assertScript(<<<'JS'
            (() => {
                const wrapper = document.querySelector('[data-testid="modal-rich-editor"]')
                const modal = wrapper.closest('.fi-modal-window-ctn')
                modal.scrollTop = 300
                return modal.scrollTop === 300
            })()
            JS)
        ->assertScript(<<<'JS'
            (() => {
                const wrapper = document.querySelector('[data-testid="modal-rich-editor"]')
                const toolbar = wrapper.querySelector('.fi-fo-rich-editor-toolbar')
                const modal = wrapper.closest('.fi-modal-window-ctn')

                const scrollViewportTop = modal.getBoundingClientRect().top + parseFloat(getComputedStyle(modal).paddingTop)

                return getComputedStyle(toolbar).position === 'sticky' &&
                    modal.scrollTop === 300 &&
                    Math.abs(toolbar.getBoundingClientRect().top - scrollViewportTop) < 3
            })()
            JS)
        ->assertNoAccessibilityIssues();
})->with([
    'light mode' => false,
    'dark mode' => true,
]);
