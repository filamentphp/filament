<?php

use Filament\Actions\Action;
use Filament\Forms\Components\RichEditor\RichContentCustomBlock;
use Filament\Tests\TestCase;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\HtmlString;

uses(TestCase::class);

describe('label', function (): void {
    it('auto-generates label from kebab-case ID', function (): void {
        expect(TestCalloutBlock::getLabel())->toBe('Callout Block');
    });

    it('auto-generates label from simple ID', function (): void {
        expect(TestSimpleBlock::getLabel())->toBe('Quote');
    });
});

describe('icon', function (): void {
    it('returns `null` from `getIcon()` by default', function (): void {
        expect(TestCalloutBlock::getIcon())->toBeNull();
    });

    it('can return an `Htmlable` icon', function (): void {
        expect(TestBlockWithIcon::getIcon())->toBeInstanceOf(Htmlable::class);
    });
});

describe('preview label', function (): void {
    it('returns label from `getPreviewLabel()` by default', function (): void {
        expect(TestCalloutBlock::getPreviewLabel([]))->toBe('Callout Block');
    });

    it('can return a dynamic label based on `$config`', function (): void {
        expect(TestBlockWithPreview::getPreviewLabel(['text' => 'World']))->toBe('Preview: World');
    });

    it('handles missing config keys gracefully', function (): void {
        expect(TestBlockWithPreview::getPreviewLabel([]))->toBe('Preview: empty');
    });
});

describe('preview rendering', function (): void {
    it('returns `null` from `toPreviewHtml()` by default', function (): void {
        expect(TestCalloutBlock::toPreviewHtml([]))->toBeNull();
    });

    it('receives `$config` and can use it in output', function (): void {
        expect(TestBlockWithPreview::toPreviewHtml(['text' => 'Hello']))->toBe('<p>Hello</p>');
    });
});

describe('HTML rendering', function (): void {
    it('returns `null` from `toHtml()` by default', function (): void {
        expect(TestCalloutBlock::toHtml([], []))->toBeNull();
    });

    it('receives both `$config` and `$data`', function (): void {
        expect(TestBlockWithPreview::toHtml(['text' => 'Test'], ['extra' => 'value']))->toBe('<div>Test: value</div>');
    });
});

describe('editor action', function (): void {
    it('hides modal from `configureEditorAction()` by default', function (): void {
        $action = Action::make('test')->modalHeading('Configure block');

        expect($action->shouldOpenModal())->toBeTrue();

        $result = TestCalloutBlock::configureEditorAction($action);

        expect($result)->toBe($action)
            ->and($result->shouldOpenModal())->toBeFalse();
    });

    it('can return the action without hiding the modal', function (): void {
        $action = Action::make('test')->modalHeading('Configure block');
        $result = TestBlockWithPreview::configureEditorAction($action);

        expect($result)->toBe($action)
            ->and($result->shouldOpenModal())->toBeTrue();
    });
});

describe('preview prose styling', function (): void {
    it('returns `false` by default', function (): void {
        expect(TestCalloutBlock::shouldApplyProseStylingToPreview([]))->toBeFalse();
    });

    it('returns `true` when overridden', function (): void {
        expect(TestBlockWithProsePreview::shouldApplyProseStylingToPreview([]))->toBeTrue();
    });

    it('receives `$config` parameter', function (bool $applyProseStyling): void {
        expect(TestBlockWithProsePreview::shouldApplyProseStylingToPreview(['applyProseStyling' => $applyProseStyling]))->toBe($applyProseStyling);
    })->with([false, true]);
});

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

class TestBlockWithIcon extends RichContentCustomBlock
{
    public static function getId(): string
    {
        return 'with-icon';
    }

    public static function getIcon(): Htmlable
    {
        return new HtmlString('icon');
    }
}

class TestBlockWithPreview extends RichContentCustomBlock
{
    public static function getId(): string
    {
        return 'with-preview';
    }

    public static function toPreviewHtml(array $configuration): ?string
    {
        $config = &$configuration;

        return '<p>' . ($config['text'] ?? '') . '</p>';
    }

    public static function getPreviewLabel(array $configuration): string
    {
        $config = &$configuration;

        return 'Preview: ' . ($config['text'] ?? 'empty');
    }

    public static function toHtml(array $configuration, array $data): ?string
    {
        $config = &$configuration;

        return '<div>' . ($config['text'] ?? '') . ': ' . ($data['extra'] ?? '') . '</div>';
    }

    public static function configureEditorAction(Action $action): Action
    {
        return $action;
    }
}

class TestBlockWithProsePreview extends RichContentCustomBlock
{
    public static function getId(): string
    {
        return 'prose-preview';
    }

    public static function toPreviewHtml(array $configuration): ?string
    {
        $config = &$configuration;

        return '<h2>' . ($config['title'] ?? '') . '</h2>';
    }

    public static function shouldApplyProseStylingToPreview(array $configuration): bool
    {
        $config = &$configuration;

        return $config['applyProseStyling'] ?? true;
    }
}
