<?php

use Filament\Infolists\Components\TextEntry;
use Filament\Tests\TestCase;

uses(TestCase::class);

// Testing Entry via TextEntry (concrete subclass)

describe('construction', function (): void {
    it('can be constructed with a name', function (): void {
        $entry = TextEntry::make('title');

        expect($entry->getName())->toBe('title');
    });

    it('throws `LogicException` when name is blank', function (): void {
        TextEntry::make('');
    })->throws(LogicException::class);
});

describe('`getLabel()` logic', function (): void {
    it('auto-generates label from kebab name', function (): void {
        $entry = TextEntry::make('first-name');

        expect($entry->getLabel())->toBe('First name');
    });

    it('auto-generates label from underscored name', function (): void {
        $entry = TextEntry::make('last_name');

        expect($entry->getLabel())->toBe('Last name');
    });

    it('auto-generates label from dotted name using last segment', function (): void {
        $entry = TextEntry::make('author.full-name');

        expect($entry->getLabel())->toBe('Full name');
    });

    it('uses custom label over auto-generated', function (): void {
        $entry = TextEntry::make('title')
            ->label('Post Title');

        expect($entry->getLabel())->toBe('Post Title');
    });

    it('can set label with a `Closure`', function (): void {
        $entry = TextEntry::make('title')
            ->label(static fn (): string => 'Dynamic');

        expect($entry->getLabel())->toBe('Dynamic');
    });
});

describe('hint', function (): void {
    it('returns `false` for `hasHint()` by default', function (): void {
        $entry = TextEntry::make('title');

        expect($entry->hasHint())->toBeFalse();
    });

    it('can set `hint()`', function (): void {
        $entry = TextEntry::make('title')
            ->hint('Some hint');

        expect($entry->hasHint())->toBeTrue();
        expect($entry->getHint())->toBe('Some hint');
    });

    it('can set `hintIcon()`', function (): void {
        $entry = TextEntry::make('title')
            ->hintIcon('heroicon-o-information-circle');

        expect($entry->hasHintIcon())->toBeTrue();
        expect($entry->getHintIcon())->toBe('heroicon-o-information-circle');
    });

    it('can set `hintColor()`', function (): void {
        $entry = TextEntry::make('title')
            ->hintColor('danger');

        expect($entry->getHintColor())->toBe('danger');
    });
});

describe('label visibility', function (): void {
    it('defaults `isLabelHidden()` to `false`', function (): void {
        $entry = TextEntry::make('title');

        expect($entry->isLabelHidden())->toBeFalse();
    });

    it('can set `hiddenLabel()`', function (): void {
        $entry = TextEntry::make('title')->hiddenLabel();

        expect($entry->isLabelHidden())->toBeTrue();
    });
});

describe('slot methods return fluent `$this`', function (): void {
    it('returns fluent `$this` from all slot methods', function (): void {
        $entry = TextEntry::make('title');

        expect($entry->aboveLabel([]))->toBe($entry);
        expect($entry->belowLabel([]))->toBe($entry);
        expect($entry->beforeLabel([]))->toBe($entry);
        expect($entry->afterLabel([]))->toBe($entry);
        expect($entry->aboveContent([]))->toBe($entry);
        expect($entry->belowContent([]))->toBe($entry);
        expect($entry->beforeContent([]))->toBe($entry);
        expect($entry->afterContent([]))->toBe($entry);
    });
});
