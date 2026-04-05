<?php

use Filament\Forms\Components\RichEditor\RichEditorTool;
use Filament\Forms\Components\RichEditor\ToolbarButtonGroup;
use Filament\Tests\TestCase;

uses(TestCase::class);

describe('construction', function (): void {
    it('can be constructed with a label', function (): void {
        $group = ToolbarButtonGroup::make('Headings');

        expect($group->getName())->toBe('Headings');
    });

    it('can be constructed with buttons', function (): void {
        $group = ToolbarButtonGroup::make('Format', ['bold', 'italic']);

        expect($group->getButtons())->toBe(['bold', 'italic']);
    });
});

describe('buttons', function (): void {
    it('returns empty array for `getButtons()` by default', function (): void {
        $group = ToolbarButtonGroup::make('Format');

        expect($group->getButtons())->toBe([]);
    });

    it('can set `buttons()`', function (): void {
        $group = ToolbarButtonGroup::make('Format')
            ->buttons(['bold', 'italic', 'strike']);

        expect($group->getButtons())->toBe(['bold', 'italic', 'strike']);
    });

    it('can set `buttons()` with a `Closure`', function (): void {
        $group = ToolbarButtonGroup::make('Format')
            ->buttons(static fn (): array => ['bold', 'underline']);

        expect($group->getButtons())->toBe(['bold', 'underline']);
    });
});

describe('textual buttons', function (): void {
    it('defaults `hasTextualButtons()` to `false`', function (): void {
        $group = ToolbarButtonGroup::make('Format');

        expect($group->hasTextualButtons())->toBeFalse();
    });

    it('can set `textualButtons()`', function (): void {
        $group = ToolbarButtonGroup::make('Format')
            ->textualButtons();

        expect($group->hasTextualButtons())->toBeTrue();
    });

    it('can set `textualButtons()` with a `Closure`', function (): void {
        $group = ToolbarButtonGroup::make('Format')
            ->textualButtons(static fn (): bool => true);

        expect($group->hasTextualButtons())->toBeTrue();
    });
});

describe('`resolve()` logic', function (): void {
    it('resolves button names to tool objects', function (): void {
        $boldTool = RichEditorTool::make('bold')->icon('heroicon-o-bold');
        $italicTool = RichEditorTool::make('italic')->icon('heroicon-o-italic');

        $tools = ['bold' => $boldTool, 'italic' => $italicTool];

        $group = ToolbarButtonGroup::make('Format', ['bold', 'italic'])
            ->resolve($tools);

        $resolved = $group->getResolvedButtons();

        expect($resolved)->toHaveCount(2);
        expect($resolved[0])->toBe($boldTool);
        expect($resolved[1])->toBe($italicTool);
    });

    it('filters out non-existent button names', function (): void {
        $boldTool = RichEditorTool::make('bold')->icon('heroicon-o-bold');

        $tools = ['bold' => $boldTool];

        $group = ToolbarButtonGroup::make('Format', ['bold', 'nonexistent'])
            ->resolve($tools);

        $resolved = $group->getResolvedButtons();

        expect($resolved)->toHaveCount(1);
        expect($resolved[0])->toBe($boldTool);
    });

    it('returns empty array when no buttons match', function (): void {
        $group = ToolbarButtonGroup::make('Format', ['missing1', 'missing2'])
            ->resolve([]);

        expect($group->getResolvedButtons())->toBe([]);
    });

    it('returns fluent `$this`', function (): void {
        $group = ToolbarButtonGroup::make('Format');

        expect($group->resolve([]))->toBe($group);
    });
});

describe('`toEmbeddedHtml()` output', function (): void {
    it('returns empty string when no resolved buttons', function (): void {
        $group = ToolbarButtonGroup::make('Format')
            ->resolve([]);

        expect($group->toEmbeddedHtml())->toBe('');
    });

    it('renders dropdown with resolved buttons', function (): void {
        $boldTool = RichEditorTool::make('bold')
            ->icon('heroicon-o-bold')
            ->jsHandler('$getEditor().chain().toggleBold().run()');

        $group = ToolbarButtonGroup::make('Format', ['bold'])
            ->resolve(['bold' => $boldTool]);

        $html = $group->toEmbeddedHtml();

        expect($html)->toContain('fi-fo-rich-editor-dropdown-tool');
        expect($html)->toContain('fi-fo-rich-editor-dropdown-tool-menu');
        expect($html)->toContain('aria-haspopup="menu"');
        expect($html)->toContain('Format');
    });

    it('adds textual class when `textualButtons()` is set', function (): void {
        $boldTool = RichEditorTool::make('bold')
            ->icon('heroicon-o-bold')
            ->jsHandler('toggle()');

        $group = ToolbarButtonGroup::make('Format', ['bold'])
            ->textualButtons()
            ->resolve(['bold' => $boldTool]);

        $html = $group->toEmbeddedHtml();

        expect($html)->toContain('fi-fo-rich-editor-dropdown-tool-textual');
    });

    it('does not add textual class when `textualButtons()` is not set', function (): void {
        $boldTool = RichEditorTool::make('bold')
            ->icon('heroicon-o-bold')
            ->jsHandler('toggle()');

        $group = ToolbarButtonGroup::make('Format', ['bold'])
            ->resolve(['bold' => $boldTool]);

        $html = $group->toEmbeddedHtml();

        expect($html)->not->toContain('fi-fo-rich-editor-dropdown-tool-textual');
    });

    it('renders button labels in textual mode', function (): void {
        $boldTool = RichEditorTool::make('bold')
            ->icon('heroicon-o-bold')
            ->label('Bold')
            ->jsHandler('toggle()');

        $group = ToolbarButtonGroup::make('Format', ['bold'])
            ->textualButtons()
            ->resolve(['bold' => $boldTool]);

        $html = $group->toEmbeddedHtml();

        expect($html)->toContain('<span>Bold</span>');
    });

    it('renders with `buttons()` set via `Closure`', function (): void {
        $boldTool = RichEditorTool::make('bold')
            ->icon('heroicon-o-bold')
            ->jsHandler('toggle()');

        $group = ToolbarButtonGroup::make('Format')
            ->buttons(static fn (): array => ['bold'])
            ->resolve(['bold' => $boldTool]);

        $html = $group->toEmbeddedHtml();

        expect($html)->not->toBe('');
        expect($html)->toContain('Format');
    });

    it('renders with `textualButtons()` set via `Closure`', function (): void {
        $boldTool = RichEditorTool::make('bold')
            ->icon('heroicon-o-bold')
            ->jsHandler('toggle()');

        $group = ToolbarButtonGroup::make('Format', ['bold'])
            ->textualButtons(static fn (): bool => true)
            ->resolve(['bold' => $boldTool]);

        $html = $group->toEmbeddedHtml();

        expect($html)->not->toBe('');
    });
});
