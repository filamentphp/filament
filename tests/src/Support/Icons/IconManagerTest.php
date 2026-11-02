<?php

use Filament\Support\Facades\FilamentIcon;
use Filament\Support\Icons\Heroicon;
use Filament\Support\Icons\IconManager;
use Filament\Tests\TestCase;
use Illuminate\Support\Facades\Blade;
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

describe('compatible icon spellings', function (): void {
    it('resolves and renders either spelling with ordered fallbacks', function (string $legacy, string $canonical): void {
        FilamentIcon::swap($this->manager);

        expect(FilamentIcon::resolve([$legacy, $canonical]))->toBeNull();

        foreach ([[$legacy, $canonical], [$canonical, $legacy]] as [$registered, $resolved]) {
            $icon = new HtmlString('<svg data-icon="replacement"></svg>');
            FilamentIcon::register([$registered => $icon, 'custom-fallback' => 'heroicon-o-star']);

            expect(FilamentIcon::resolve($resolved))->toBe($icon)
                ->and(FilamentIcon::resolve(['missing', $resolved, 'custom-fallback']))->toBe($icon)
                ->and(FilamentIcon::resolve(['custom-fallback', $resolved]))->toBe('heroicon-o-star')
                ->and(Blade::render('<x-filament::icon :alias="$alias" />', ['alias' => $resolved]))
                ->toContain('<svg data-icon="replacement"></svg>');
        }
    })->with('compatible icon aliases');

    it('uses the last registration regardless of spelling within and across calls', function (string $legacy, string $canonical): void {
        foreach ([[$legacy, $canonical], [$canonical, $legacy]] as [$first, $second]) {
            $this->manager->register([$first => 'heroicon-o-check', $second => Heroicon::XMark]);

            expect($this->manager->resolve($first))->toBe(Heroicon::XMark)
                ->and($this->manager->resolve($second))->toBe(Heroicon::XMark);

            $this->manager->register([$first => 'heroicon-o-star']);

            expect($this->manager->resolve($first))->toBe('heroicon-o-star')
                ->and($this->manager->resolve($second))->toBe('heroicon-o-star');

            $this->manager->register([$second => '']);

            expect($this->manager->resolve([$first, 'custom-fallback']))->toBe('');
        }
    })->with([
        ['forms:components.rich-editor.toolbar.clear_formatting', 'forms::components.rich-editor.toolbar.clear-formatting'],
        ['schema::components.callout.info', 'schemas::components.callout.info'],
    ]);

    it('leaves unknown spellings independent', function (string $first, string $second): void {
        $this->manager->register([$first => 'heroicon-o-check']);

        expect($this->manager->resolve($second))->toBeNull();

        $this->manager->register([$second => 'heroicon-o-star']);

        expect($this->manager->resolve([$first, $second]))->toBe('heroicon-o-check')
            ->and($this->manager->resolve([$second, $first]))->toBe('heroicon-o-star');
    })->with([
        ['forms:components.rich-editor.toolbar.custom_button', 'forms::components.rich-editor.toolbar.custom-button'],
        ['forms::components.rich-editor.toolbar.clear_formatting', 'forms::components.rich-editor.toolbar.clear-formatting'],
        ['forms:components.rich-editor.toolbar.clear-formatting', 'forms:components.rich-editor.toolbar.clear_formatting'],
        ['schema::components.custom', 'schemas::components.custom'],
    ]);
});

dataset('compatible icon aliases', [
    ['forms:components.rich-editor.toolbar.align-center', 'forms::components.rich-editor.toolbar.align-center'],
    ['forms:components.rich-editor.toolbar.align-end', 'forms::components.rich-editor.toolbar.align-end'],
    ['forms:components.rich-editor.toolbar.align-justify', 'forms::components.rich-editor.toolbar.align-justify'],
    ['forms:components.rich-editor.toolbar.align-start', 'forms::components.rich-editor.toolbar.align-start'],
    ['forms:components.rich-editor.toolbar.attach-files', 'forms::components.rich-editor.toolbar.attach-files'],
    ['forms:components.rich-editor.toolbar.blockquote', 'forms::components.rich-editor.toolbar.blockquote'],
    ['forms:components.rich-editor.toolbar.bold', 'forms::components.rich-editor.toolbar.bold'],
    ['forms:components.rich-editor.toolbar.bullet-list', 'forms::components.rich-editor.toolbar.bullet-list'],
    ['forms:components.rich-editor.toolbar.clear_formatting', 'forms::components.rich-editor.toolbar.clear-formatting'],
    ['forms:components.rich-editor.toolbar.code', 'forms::components.rich-editor.toolbar.code'],
    ['forms:components.rich-editor.toolbar.code-block', 'forms::components.rich-editor.toolbar.code-block'],
    ['forms:components.rich-editor.toolbar.custom-blocks', 'forms::components.rich-editor.toolbar.custom-blocks'],
    ['forms:components.rich-editor.toolbar.details', 'forms::components.rich-editor.toolbar.details'],
    ['forms:components.rich-editor.toolbar.grid', 'forms::components.rich-editor.toolbar.grid'],
    ['forms:components.rich-editor.toolbar.grid_add_column_after', 'forms::components.rich-editor.toolbar.grid-add-column-after'],
    ['forms:components.rich-editor.toolbar.grid_add_column_before', 'forms::components.rich-editor.toolbar.grid-add-column-before'],
    ['forms:components.rich-editor.toolbar.grid_delete', 'forms::components.rich-editor.toolbar.grid-delete'],
    ['forms:components.rich-editor.toolbar.grid_delete_column', 'forms::components.rich-editor.toolbar.grid-delete-column'],
    ['forms:components.rich-editor.toolbar.h1', 'forms::components.rich-editor.toolbar.h1'],
    ['forms:components.rich-editor.toolbar.h2', 'forms::components.rich-editor.toolbar.h2'],
    ['forms:components.rich-editor.toolbar.h3', 'forms::components.rich-editor.toolbar.h3'],
    ['forms:components.rich-editor.toolbar.h4', 'forms::components.rich-editor.toolbar.h4'],
    ['forms:components.rich-editor.toolbar.h5', 'forms::components.rich-editor.toolbar.h5'],
    ['forms:components.rich-editor.toolbar.h6', 'forms::components.rich-editor.toolbar.h6'],
    ['forms:components.rich-editor.toolbar.highlight', 'forms::components.rich-editor.toolbar.highlight'],
    ['forms:components.rich-editor.toolbar.horizontal-rule', 'forms::components.rich-editor.toolbar.horizontal-rule'],
    ['forms:components.rich-editor.toolbar.italic', 'forms::components.rich-editor.toolbar.italic'],
    ['forms:components.rich-editor.toolbar.lead', 'forms::components.rich-editor.toolbar.lead'],
    ['forms:components.rich-editor.toolbar.link', 'forms::components.rich-editor.toolbar.link'],
    ['forms:components.rich-editor.toolbar.merge-tags', 'forms::components.rich-editor.toolbar.merge-tags'],
    ['forms:components.rich-editor.toolbar.ordered-list', 'forms::components.rich-editor.toolbar.ordered-list'],
    ['forms:components.rich-editor.toolbar.paragraph', 'forms::components.rich-editor.toolbar.paragraph'],
    ['forms:components.rich-editor.toolbar.redo', 'forms::components.rich-editor.toolbar.redo'],
    ['forms:components.rich-editor.toolbar.small', 'forms::components.rich-editor.toolbar.small'],
    ['forms:components.rich-editor.toolbar.strike', 'forms::components.rich-editor.toolbar.strike'],
    ['forms:components.rich-editor.toolbar.subscript', 'forms::components.rich-editor.toolbar.subscript'],
    ['forms:components.rich-editor.toolbar.superscript', 'forms::components.rich-editor.toolbar.superscript'],
    ['forms:components.rich-editor.toolbar.table', 'forms::components.rich-editor.toolbar.table'],
    ['forms:components.rich-editor.toolbar.table_add_column_after', 'forms::components.rich-editor.toolbar.table-add-column-after'],
    ['forms:components.rich-editor.toolbar.table_add_column_before', 'forms::components.rich-editor.toolbar.table-add-column-before'],
    ['forms:components.rich-editor.toolbar.table_add_row_after', 'forms::components.rich-editor.toolbar.table-add-row-after'],
    ['forms:components.rich-editor.toolbar.table_add_row_before', 'forms::components.rich-editor.toolbar.table-add-row-before'],
    ['forms:components.rich-editor.toolbar.table_delete', 'forms::components.rich-editor.toolbar.table-delete'],
    ['forms:components.rich-editor.toolbar.table_delete_column', 'forms::components.rich-editor.toolbar.table-delete-column'],
    ['forms:components.rich-editor.toolbar.table_delete_row', 'forms::components.rich-editor.toolbar.table-delete-row'],
    ['forms:components.rich-editor.toolbar.table_merge_cells', 'forms::components.rich-editor.toolbar.table-merge-cells'],
    ['forms:components.rich-editor.toolbar.table_split_cell', 'forms::components.rich-editor.toolbar.table-split-cell'],
    ['forms:components.rich-editor.toolbar.table_toggle_header_cell', 'forms::components.rich-editor.toolbar.table-toggle-header-cell'],
    ['forms:components.rich-editor.toolbar.table_toggle_header_row', 'forms::components.rich-editor.toolbar.table-toggle-header-row'],
    ['forms:components.rich-editor.toolbar.text-color', 'forms::components.rich-editor.toolbar.text-color'],
    ['forms:components.rich-editor.toolbar.underline', 'forms::components.rich-editor.toolbar.underline'],
    ['forms:components.rich-editor.toolbar.undo', 'forms::components.rich-editor.toolbar.undo'],
    ['schema::components.callout.danger', 'schemas::components.callout.danger'],
    ['schema::components.callout.info', 'schemas::components.callout.info'],
    ['schema::components.callout.success', 'schemas::components.callout.success'],
    ['schema::components.callout.warning', 'schemas::components.callout.warning'],
    ['schema::components.tabs.dropdown-trigger-button', 'schemas::components.tabs.dropdown-trigger-button'],
    ['schema::components.tabs.more-tabs-button', 'schemas::components.tabs.more-tabs-button'],
    ['schema::components.wizard.completed-step', 'schemas::components.wizard.completed-step'],
]);
