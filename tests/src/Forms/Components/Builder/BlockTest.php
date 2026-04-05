<?php

use Filament\Forms\Components\Builder\Block;
use Filament\Tests\TestCase;

uses(TestCase::class);

describe('construction', function (): void {
    it('can be constructed with a name', function (): void {
        $block = Block::make('paragraph');

        expect($block->getName())->toBe('paragraph');
    });

    it('throws `InvalidArgumentException` when name is blank', function (): void {
        Block::make('');
    })->throws(InvalidArgumentException::class);
});

describe('`getLabel()` logic', function (): void {
    it('auto-generates label from name when no label is set', function (): void {
        $block = Block::make('hero-section');

        expect($block->getLabel())->toBe('Hero section');
    });

    it('auto-generates label from dotted name using last segment', function (): void {
        $block = Block::make('blocks.featured-post');

        expect($block->getLabel())->toBe('Featured post');
    });

    it('auto-generates label from underscored name', function (): void {
        $block = Block::make('call_to_action');

        expect($block->getLabel())->toBe('Call to action');
    });

    it('uses custom label when set', function (): void {
        $block = Block::make('paragraph')
            ->label('Custom Paragraph');

        expect($block->getLabel())->toBe('Custom Paragraph');
    });

    it('uses `Closure` label with `$state` and `$key` parameters', function (): void {
        $block = Block::make('paragraph')
            ->label(static fn (?array $state, ?string $key): string => "Item {$key}: {$state['title']}");

        expect($block->getLabel(
            state: ['title' => 'Hello'],
            key: '42',
        ))->toBe('Item 42: Hello');
    });

    it('falls back to auto-generated label when `Closure` label returns blank', function (): void {
        $block = Block::make('hero-section')
            ->label(static fn (): ?string => null);

        expect($block->getLabel())->toBe('Hero section');
    });
});

describe('icon', function (): void {
    it('returns `null` for `getIcon()` by default', function (): void {
        $block = Block::make('paragraph');

        expect($block->getIcon())->toBeNull();
    });

    it('can set `icon()`', function (): void {
        $block = Block::make('paragraph')
            ->icon('heroicon-o-document-text');

        expect($block->getIcon())->toBe('heroicon-o-document-text');
    });

    it('can set `icon()` with a `Closure`', function (): void {
        $block = Block::make('paragraph')
            ->icon(static fn (): string => 'heroicon-o-photo');

        expect($block->getIcon())->toBe('heroicon-o-photo');
    });

    it('can clear `icon()` with `null`', function (): void {
        $block = Block::make('paragraph')
            ->icon('heroicon-o-document-text')
            ->icon(null);

        expect($block->getIcon())->toBeNull();
    });
});

describe('max items', function (): void {
    it('returns `null` for `getMaxItems()` by default', function (): void {
        $block = Block::make('paragraph');

        expect($block->getMaxItems())->toBeNull();
    });

    it('can set `maxItems()`', function (): void {
        $block = Block::make('paragraph')
            ->maxItems(3);

        expect($block->getMaxItems())->toBe(3);
    });

    it('can set `maxItems()` with a `Closure`', function (): void {
        $block = Block::make('paragraph')
            ->maxItems(static fn (): int => 5);

        expect($block->getMaxItems())->toBe(5);
    });

    it('can clear `maxItems()` with `null`', function (): void {
        $block = Block::make('paragraph')
            ->maxItems(3)
            ->maxItems(null);

        expect($block->getMaxItems())->toBeNull();
    });
});

describe('preview', function (): void {
    it('returns `false` for `hasPreview()` by default', function (): void {
        $block = Block::make('paragraph');

        expect($block->hasPreview())->toBeFalse();
    });

    it('returns `true` for `hasPreview()` when preview is set', function (): void {
        $block = Block::make('paragraph')
            ->preview('blocks.paragraph-preview');

        expect($block->hasPreview())->toBeTrue();
    });

    it('can set `preview()` with a `Closure`', function (): void {
        $block = Block::make('paragraph')
            ->preview(static fn (): string => 'blocks.paragraph-preview');

        expect($block->hasPreview())->toBeTrue();
    });

    it('returns `false` for `hasPreview()` when cleared with `null`', function (): void {
        $block = Block::make('paragraph')
            ->preview('blocks.paragraph-preview')
            ->preview(null);

        expect($block->hasPreview())->toBeFalse();
    });
});

describe('label visibility', function (): void {
    it('returns `false` for `isLabelHidden()` by default', function (): void {
        $block = Block::make('paragraph');

        expect($block->isLabelHidden())->toBeFalse();
    });

    it('can set `hiddenLabel()`', function (): void {
        $block = Block::make('paragraph')
            ->hiddenLabel();

        expect($block->isLabelHidden())->toBeTrue();
    });

    it('can set `hiddenLabel()` with a `Closure`', function (): void {
        $block = Block::make('paragraph')
            ->hiddenLabel(static fn (): bool => true);

        expect($block->isLabelHidden())->toBeTrue();
    });

    it('reports `hasCustomLabel()` as `false` by default', function (): void {
        $block = Block::make('paragraph');

        expect($block->hasCustomLabel())->toBeFalse();
    });

    it('reports `hasCustomLabel()` as `true` when label is set', function (): void {
        $block = Block::make('paragraph')
            ->label('Custom');

        expect($block->hasCustomLabel())->toBeTrue();
    });
});
