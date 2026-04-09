<?php

use Filament\Actions\Action;
use Filament\Forms\Components\RichEditor\RichContentCustomBlock;
use Filament\Tests\TestCase;

uses(TestCase::class);

// Concrete subclasses for testing the abstract base class

class TestCalloutBlock extends RichContentCustomBlock
{
    public static function getId(): string
    {
        return 'callout-block';
    }
}

class TestSimpleBlock extends RichContentCustomBlock
{
    public static function getId(): string
    {
        return 'quote';
    }
}

class TestBlockWithPreview extends RichContentCustomBlock
{
    public static function getId(): string
    {
        return 'with-preview';
    }

    public static function toPreviewHtml(array $config): ?string
    {
        return '<p>' . ($config['text'] ?? '') . '</p>';
    }

    public static function getPreviewLabel(array $config): string
    {
        return 'Preview: ' . ($config['text'] ?? 'empty');
    }

    public static function toHtml(array $config, array $data): ?string
    {
        return '<div>' . ($config['text'] ?? '') . '</div>';
    }

    public static function configureEditorAction(Action $action): Action
    {
        return $action;
    }
}

describe('`getLabel()` logic', function (): void {
    it('auto-generates label from kebab-case ID', function (): void {
        expect(TestCalloutBlock::getLabel())->toBe('Callout Block');
    });

    it('auto-generates label from simple ID', function (): void {
        expect(TestSimpleBlock::getLabel())->toBe('Quote');
    });
});

describe('default implementations', function (): void {
    it('returns `null` from `toHtml()` by default', function (): void {
        expect(TestCalloutBlock::toHtml([], []))->toBeNull();
    });

    it('returns label from `getPreviewLabel()` by default', function (): void {
        expect(TestCalloutBlock::getPreviewLabel([]))->toBe('Callout Block');
    });

    it('returns `null` from `toPreviewHtml()` by default', function (): void {
        expect(TestCalloutBlock::toPreviewHtml([]))->toBeNull();
    });

    it('hides modal from `configureEditorAction()` by default', function (): void {
        $action = Action::make('test');
        $result = TestCalloutBlock::configureEditorAction($action);

        expect($result)->toBe($action);
    });
});

describe('`toPreviewHtml()` with config', function (): void {
    it('receives `$config` and can use it in output', function (): void {
        expect(TestBlockWithPreview::toPreviewHtml(['text' => 'Hello']))->toBe('<p>Hello</p>');
    });
});

describe('`getPreviewLabel()` with config', function (): void {
    it('can return a dynamic label based on `$config`', function (): void {
        expect(TestBlockWithPreview::getPreviewLabel(['text' => 'World']))->toBe('Preview: World');
    });

    it('handles missing config keys gracefully', function (): void {
        expect(TestBlockWithPreview::getPreviewLabel([]))->toBe('Preview: empty');
    });
});

describe('`toHtml()` with config and data', function (): void {
    it('receives both `$config` and `$data`', function (): void {
        expect(TestBlockWithPreview::toHtml(['text' => 'Test'], ['extra' => 'value']))->toBe('<div>Test</div>');
    });
});

describe('`configureEditorAction()`', function (): void {
    it('can return the action without hiding the modal', function (): void {
        $action = Action::make('test');
        $result = TestBlockWithPreview::configureEditorAction($action);

        expect($result)->toBe($action);
    });
});
