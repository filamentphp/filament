<?php

use Filament\Forms\Components\RichEditor\RichEditorTool;
use Filament\Tests\TestCase;

uses(TestCase::class);

describe('construction and name', function (): void {
    it('can be constructed with a name', function (): void {
        $tool = RichEditorTool::make('bold');

        expect($tool->getName())->toBe('bold');
    });

    it('hides label by default', function (): void {
        $tool = RichEditorTool::make('bold');

        expect($tool->isLabelHidden())->toBeTrue();
    });
});

describe('`getLabel()` logic', function (): void {
    it('auto-generates label from name', function (): void {
        $tool = RichEditorTool::make('bullet-list');

        expect($tool->getLabel())->toBe('Bullet list');
    });

    it('auto-generates label from underscored name', function (): void {
        $tool = RichEditorTool::make('ordered_list');

        expect($tool->getLabel())->toBe('Ordered list');
    });

    it('uses custom label over auto-generated', function (): void {
        $tool = RichEditorTool::make('bold')
            ->label('Make Bold');

        expect($tool->getLabel())->toBe('Make Bold');
    });
});

describe('`getActiveKey()` logic', function (): void {
    it('defaults to name when no `activeKey()` is set', function (): void {
        $tool = RichEditorTool::make('bold');

        expect($tool->getActiveKey())->toBe('bold');
    });

    it('uses custom `activeKey()` when set', function (): void {
        $tool = RichEditorTool::make('bold')
            ->activeKey('strong');

        expect($tool->getActiveKey())->toBe('strong');
    });

    it('can set `activeKey()` with a `Closure`', function (): void {
        $tool = RichEditorTool::make('bold')
            ->activeKey(static fn (): string => 'dynamic-key');

        expect($tool->getActiveKey())->toBe('dynamic-key');
    });
});

describe('active options', function (): void {
    it('returns empty array for `getActiveOptions()` by default', function (): void {
        $tool = RichEditorTool::make('heading');

        expect($tool->getActiveOptions())->toBe([]);
    });

    it('can set `activeOptions()`', function (): void {
        $tool = RichEditorTool::make('heading')
            ->activeOptions(['level' => 2]);

        expect($tool->getActiveOptions())->toBe(['level' => 2]);
    });

    it('can set `activeOptions()` with a `Closure`', function (): void {
        $tool = RichEditorTool::make('heading')
            ->activeOptions(static fn (): array => ['level' => 3]);

        expect($tool->getActiveOptions())->toBe(['level' => 3]);
    });
});

describe('disabled when not active', function (): void {
    it('defaults `isDisabledWhenNotActive()` to `false`', function (): void {
        $tool = RichEditorTool::make('bold');

        expect($tool->isDisabledWhenNotActive())->toBeFalse();
    });

    it('can set `disabledWhenNotActive()`', function (): void {
        $tool = RichEditorTool::make('bold')
            ->disabledWhenNotActive();

        expect($tool->isDisabledWhenNotActive())->toBeTrue();
    });

    it('can set `disabledWhenNotActive()` with a `Closure`', function (): void {
        $tool = RichEditorTool::make('bold')
            ->disabledWhenNotActive(static fn (): bool => true);

        expect($tool->isDisabledWhenNotActive())->toBeTrue();
    });
});

describe('active styling', function (): void {
    it('defaults `hasActiveStyling()` to `true`', function (): void {
        $tool = RichEditorTool::make('bold');

        expect($tool->hasActiveStyling())->toBeTrue();
    });

    it('can set `activeStyling()` to `false`', function (): void {
        $tool = RichEditorTool::make('bold')
            ->activeStyling(false);

        expect($tool->hasActiveStyling())->toBeFalse();
    });

    it('can set `activeStyling()` with a `Closure`', function (): void {
        $tool = RichEditorTool::make('bold')
            ->activeStyling(static fn (): bool => false);

        expect($tool->hasActiveStyling())->toBeFalse();
    });
});

describe('icon and icon alias', function (): void {
    it('returns `null` for `getIconAlias()` by default', function (): void {
        $tool = RichEditorTool::make('bold');

        expect($tool->getIconAlias())->toBeNull();
    });

    it('can set `iconAlias()`', function (): void {
        $tool = RichEditorTool::make('bold')
            ->iconAlias('filament-bold');

        expect($tool->getIconAlias())->toBe('filament-bold');
    });

    it('can set `iconAlias()` with a `Closure`', function (): void {
        $tool = RichEditorTool::make('bold')
            ->iconAlias(static fn (): string => 'dynamic-alias');

        expect($tool->getIconAlias())->toBe('dynamic-alias');
    });
});

describe('JS handler', function (): void {
    it('returns `null` for `getJsHandler()` by default', function (): void {
        $tool = RichEditorTool::make('bold');

        expect($tool->getJsHandler())->toBeNull();
    });

    it('can set `jsHandler()`', function (): void {
        $tool = RichEditorTool::make('bold')
            ->jsHandler('$getEditor().chain().toggleBold().run()');

        expect($tool->getJsHandler())->toBe('$getEditor().chain().toggleBold().run()');
    });

    it('can set `jsHandler()` with a `Closure`', function (): void {
        $tool = RichEditorTool::make('bold')
            ->jsHandler(static fn (): string => 'dynamic()');

        expect($tool->getJsHandler())->toBe('dynamic()');
    });
});

describe('active JS expression', function (): void {
    it('returns `null` for `getActiveJsExpression()` by default', function (): void {
        $tool = RichEditorTool::make('bold');

        expect($tool->getActiveJsExpression())->toBeNull();
    });

    it('can set `activeJsExpression()`', function (): void {
        $tool = RichEditorTool::make('bold')
            ->activeJsExpression('$getEditor()?.isActive("bold")');

        expect($tool->getActiveJsExpression())->toBe('$getEditor()?.isActive("bold")');
    });
});

describe('`toEmbeddedHtml()` output', function (): void {
    it('renders a button element', function (): void {
        $tool = RichEditorTool::make('bold')
            ->icon('heroicon-o-bold');

        $html = $tool->toEmbeddedHtml();

        expect($html)->toContain('<button');
        expect($html)->toContain('fi-fo-rich-editor-tool');
    });

    it('includes `aria-label` from the tool label', function (): void {
        $tool = RichEditorTool::make('bold')
            ->icon('heroicon-o-bold');

        $html = $tool->toEmbeddedHtml();

        expect($html)->toContain('aria-label');
        expect($html)->toContain('Bold');
    });

    it('uses custom `activeJsExpression()` when set', function (): void {
        $tool = RichEditorTool::make('bold')
            ->icon('heroicon-o-bold')
            ->activeJsExpression('customActive()');

        $html = $tool->toEmbeddedHtml();

        expect($html)->toContain('customActive()');
    });

    it('renders `fi-active` class binding with active styling disabled as `false`', function (): void {
        $tool = RichEditorTool::make('undo')
            ->icon('heroicon-o-arrow-uturn-left')
            ->activeStyling(false);

        $html = $tool->toEmbeddedHtml();

        expect($html)->toContain("'fi-active': false");
    });

    it('shows label text when `hiddenLabel()` is not set', function (): void {
        $tool = RichEditorTool::make('bold')
            ->icon('heroicon-o-bold')
            ->hiddenLabel(false);

        $html = $tool->toEmbeddedHtml();

        expect($html)->toContain('fi-fo-rich-editor-tool-with-label');
        expect($html)->toContain('fi-fo-rich-editor-tool-label');
        expect($html)->toContain('Bold');
    });

    it('renders with custom `activeKey()`', function (): void {
        $html = RichEditorTool::make('bold')
            ->icon('heroicon-o-bold')
            ->activeKey('strong')
            ->toEmbeddedHtml();

        expect($html)->not->toBe('');
    });

    it('renders with `activeKey()` set via `Closure`', function (): void {
        $html = RichEditorTool::make('bold')
            ->icon('heroicon-o-bold')
            ->activeKey(static fn (): string => 'dynamic-key')
            ->toEmbeddedHtml();

        expect($html)->not->toBe('');
    });

    it('renders with `activeOptions()`', function (): void {
        $html = RichEditorTool::make('heading')
            ->icon('heroicon-o-hashtag')
            ->activeOptions(['level' => 2])
            ->toEmbeddedHtml();

        expect($html)->not->toBe('');
    });

    it('renders with `activeOptions()` set via `Closure`', function (): void {
        $html = RichEditorTool::make('heading')
            ->icon('heroicon-o-hashtag')
            ->activeOptions(static fn (): array => ['level' => 3])
            ->toEmbeddedHtml();

        expect($html)->not->toBe('');
    });

    it('renders with `disabledWhenNotActive()`', function (): void {
        $html = RichEditorTool::make('bold')
            ->icon('heroicon-o-bold')
            ->disabledWhenNotActive()
            ->toEmbeddedHtml();

        expect($html)->not->toBe('');
    });

    it('renders with `disabledWhenNotActive()` set via `Closure`', function (): void {
        $html = RichEditorTool::make('bold')
            ->icon('heroicon-o-bold')
            ->disabledWhenNotActive(static fn (): bool => true)
            ->toEmbeddedHtml();

        expect($html)->not->toBe('');
    });

    it('renders with `activeStyling()` set via `Closure`', function (): void {
        $html = RichEditorTool::make('bold')
            ->icon('heroicon-o-bold')
            ->activeStyling(static fn (): bool => false)
            ->toEmbeddedHtml();

        expect($html)->not->toBe('');
    });

    it('renders with `jsHandler()`', function (): void {
        $html = RichEditorTool::make('bold')
            ->icon('heroicon-o-bold')
            ->jsHandler('$getEditor().chain().toggleBold().run()')
            ->toEmbeddedHtml();

        expect($html)->not->toBe('');
    });

    it('renders with `jsHandler()` set via `Closure`', function (): void {
        $html = RichEditorTool::make('bold')
            ->icon('heroicon-o-bold')
            ->jsHandler(static fn (): string => 'dynamic()')
            ->toEmbeddedHtml();

        expect($html)->not->toBe('');
    });

    it('renders with custom `label()`', function (): void {
        $html = RichEditorTool::make('bold')
            ->icon('heroicon-o-bold')
            ->label('Make Bold')
            ->toEmbeddedHtml();

        expect($html)->toContain('Make Bold');
    });
});
