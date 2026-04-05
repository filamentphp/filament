<?php

use Filament\Actions\Action;
use Filament\Forms\Components\RichEditor\RichContentCustomBlock;
use Filament\Tests\TestCase;

uses(TestCase::class);

// Concrete subclass for testing the abstract base class
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
