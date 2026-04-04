<?php

use Filament\Schemas\Components\Tabs\Tab;
use Filament\Support\Enums\IconPosition;
use Filament\Support\Icons\Heroicon;
use Filament\Tests\TestCase;
use Illuminate\Support\HtmlString;

uses(TestCase::class);

it('can be constructed with a label', function (): void {
    $tab = Tab::make('Settings');

    expect($tab->getLabel())->toBe('Settings');
});

it('returns `null` for `getLabel()` when no label given', function (): void {
    $tab = Tab::make();

    expect($tab->getLabel())->toBeNull();
});

it('can set `label()` with a `Closure`', function (): void {
    $tab = Tab::make()
        ->label(static fn (): string => 'Dynamic');

    expect($tab->getLabel())->toBe('Dynamic');
});

it('can set `label()` with an `Htmlable`', function (): void {
    $htmlable = new HtmlString('<strong>Bold</strong>');
    $tab = Tab::make()->label($htmlable);

    expect($tab->getLabel())->toBe($htmlable);
});

it('returns `true` for `canConcealComponents()`', function (): void {
    $tab = Tab::make('Test');

    expect($tab->canConcealComponents())->toBeTrue();
});

describe('label visibility', function (): void {
    it('reports `hasCustomLabel()` as `false` by default', function (): void {
        $tab = Tab::make();

        expect($tab->hasCustomLabel())->toBeFalse();
    });

    it('reports `hasCustomLabel()` as `true` after `label()` is set', function (): void {
        $tab = Tab::make('Custom');

        expect($tab->hasCustomLabel())->toBeTrue();
    });

    it('defaults `isLabelHidden()` to `false`', function (): void {
        $tab = Tab::make('Test');

        expect($tab->isLabelHidden())->toBeFalse();
    });

    it('can set `hiddenLabel()`', function (): void {
        $tab = Tab::make('Test')->hiddenLabel();

        expect($tab->isLabelHidden())->toBeTrue();
    });

    it('can set `hiddenLabel()` with a `Closure`', function (): void {
        $tab = Tab::make('Test')
            ->hiddenLabel(static fn (): bool => true);

        expect($tab->isLabelHidden())->toBeTrue();
    });

    it('can translate label with `translateLabel()`', function (): void {
        $tab = Tab::make()
            ->label('validation.required')
            ->translateLabel();

        expect($tab->getLabel())->toBe(__('validation.required'));
    });
});

describe('badge', function (): void {
    it('returns `null` for `getBadge()` by default', function (): void {
        $tab = Tab::make('Test');

        expect($tab->getBadge())->toBeNull();
    });

    it('can set `badge()` with a string', function (): void {
        $tab = Tab::make('Test')->badge('New');

        expect($tab->getBadge())->toBe('New');
    });

    it('can set `badge()` with a number', function (): void {
        $tab = Tab::make('Test')->badge(5);

        expect($tab->getBadge())->toBe(5);
    });

    it('can set `badge()` with a `Closure`', function (): void {
        $tab = Tab::make('Test')
            ->badge(static fn (): int => 42);

        expect($tab->getBadge())->toBe(42);
    });

    it('can clear `badge()` with `null`', function (): void {
        $tab = Tab::make('Test')
            ->badge(5)
            ->badge(null);

        expect($tab->getBadge())->toBeNull();
    });

    it('returns `null` for `getBadgeColor()` by default', function (): void {
        $tab = Tab::make('Test');

        expect($tab->getBadgeColor())->toBeNull();
    });

    it('can set `badgeColor()`', function (): void {
        $tab = Tab::make('Test')->badgeColor('danger');

        expect($tab->getBadgeColor())->toBe('danger');
    });

    it('can set `badgeColor()` with a `Closure`', function (): void {
        $tab = Tab::make('Test')
            ->badgeColor(static fn (): string => 'success');

        expect($tab->getBadgeColor())->toBe('success');
    });

    it('can clear `badgeColor()` with `null`', function (): void {
        $tab = Tab::make('Test')
            ->badgeColor('danger')
            ->badgeColor(null);

        expect($tab->getBadgeColor())->toBeNull();
    });
});

describe('badge tooltip', function (): void {
    it('returns `null` for `getBadgeTooltip()` by default', function (): void {
        $tab = Tab::make('Test');

        expect($tab->getBadgeTooltip())->toBeNull();
    });

    it('can set `badgeTooltip()`', function (): void {
        $tab = Tab::make('Test')->badgeTooltip('Unread items');

        expect($tab->getBadgeTooltip())->toBe('Unread items');
    });

    it('can set `badgeTooltip()` with a `Closure`', function (): void {
        $tab = Tab::make('Test')
            ->badgeTooltip(static fn (): string => 'Dynamic tip');

        expect($tab->getBadgeTooltip())->toBe('Dynamic tip');
    });

    it('can clear `badgeTooltip()` with `null`', function (): void {
        $tab = Tab::make('Test')
            ->badgeTooltip('Tip')
            ->badgeTooltip(null);

        expect($tab->getBadgeTooltip())->toBeNull();
    });
});

describe('badge icon', function (): void {
    it('returns `null` for `getBadgeIcon()` by default', function (): void {
        $tab = Tab::make('Test');

        expect($tab->getBadgeIcon())->toBeNull();
    });

    it('can set `badgeIcon()` with a string', function (): void {
        $tab = Tab::make('Test')->badgeIcon('heroicon-o-check');

        expect($tab->getBadgeIcon())->toBe('heroicon-o-check');
    });

    it('can set `badgeIcon()` with a `BackedEnum`', function (): void {
        $tab = Tab::make('Test')->badgeIcon(Heroicon::Check);

        expect($tab->getBadgeIcon())->toBe(Heroicon::Check);
    });

    it('can set `badgeIcon()` with a `Closure`', function (): void {
        $tab = Tab::make('Test')
            ->badgeIcon(static fn (): string => 'heroicon-o-star');

        expect($tab->getBadgeIcon())->toBe('heroicon-o-star');
    });

    it('can clear `badgeIcon()` with `null`', function (): void {
        $tab = Tab::make('Test')
            ->badgeIcon('heroicon-o-check')
            ->badgeIcon(null);

        expect($tab->getBadgeIcon())->toBeNull();
    });

    it('defaults `getBadgeIconPosition()` to `Before`', function (): void {
        $tab = Tab::make('Test');

        expect($tab->getBadgeIconPosition())->toBe(IconPosition::Before);
    });

    it('can set `badgeIconPosition()`', function (): void {
        $tab = Tab::make('Test')->badgeIconPosition(IconPosition::After);

        expect($tab->getBadgeIconPosition())->toBe(IconPosition::After);
    });

    it('can set `badgeIconPosition()` with a `Closure`', function (): void {
        $tab = Tab::make('Test')
            ->badgeIconPosition(static fn (): IconPosition => IconPosition::After);

        expect($tab->getBadgeIconPosition())->toBe(IconPosition::After);
    });
});

describe('icon', function (): void {
    it('returns `null` for `getIcon()` by default', function (): void {
        $tab = Tab::make('Test');

        expect($tab->getIcon())->toBeNull();
    });

    it('can set `icon()` with a string', function (): void {
        $tab = Tab::make('Test')->icon('heroicon-o-cog');

        expect($tab->getIcon())->toBe('heroicon-o-cog');
    });

    it('can set `icon()` with a `BackedEnum`', function (): void {
        $tab = Tab::make('Test')->icon(Heroicon::Cog6Tooth);

        expect($tab->getIcon())->toBe(Heroicon::Cog6Tooth);
    });

    it('can set `icon()` with a `Closure`', function (): void {
        $tab = Tab::make('Test')
            ->icon(static fn (): string => 'heroicon-o-star');

        expect($tab->getIcon())->toBe('heroicon-o-star');
    });

    it('can clear `icon()` with `null`', function (): void {
        $tab = Tab::make('Test')
            ->icon('heroicon-o-cog')
            ->icon(null);

        expect($tab->getIcon())->toBeNull();
    });

    it('can use `getIcon()` with a default', function (): void {
        $tab = Tab::make('Test');

        expect($tab->getIcon('heroicon-o-fallback'))->toBe('heroicon-o-fallback');
    });
});

describe('icon position', function (): void {
    it('defaults `getIconPosition()` to `Before`', function (): void {
        $tab = Tab::make('Test');

        expect($tab->getIconPosition())->toBe(IconPosition::Before);
    });

    it('can set `iconPosition()`', function (): void {
        $tab = Tab::make('Test')->iconPosition(IconPosition::After);

        expect($tab->getIconPosition())->toBe(IconPosition::After);
    });

    it('can set `iconPosition()` with a `Closure`', function (): void {
        $tab = Tab::make('Test')
            ->iconPosition(static fn (): IconPosition => IconPosition::After);

        expect($tab->getIconPosition())->toBe(IconPosition::After);
    });
});

describe('deferred badge', function (): void {
    it('defaults `isBadgeDeferred()` to `false`', function (): void {
        $tab = Tab::make('Test');

        expect($tab->isBadgeDeferred())->toBeFalse();
    });

    it('can set `deferBadge()`', function (): void {
        $tab = Tab::make('Test')->deferBadge();

        expect($tab->isBadgeDeferred())->toBeTrue();
    });

    it('can unset `deferBadge()`', function (): void {
        $tab = Tab::make('Test')->deferBadge()->deferBadge(false);

        expect($tab->isBadgeDeferred())->toBeFalse();
    });

    it('can set `deferBadge()` with a `Closure`', function (): void {
        $tab = Tab::make('Test')
            ->deferBadge(static fn (): bool => true);

        expect($tab->isBadgeDeferred())->toBeTrue();
    });
});

describe('query modification', function (): void {
    it('can set `modifyQueryUsing()`', function (): void {
        $tab = Tab::make('Test')
            ->modifyQueryUsing(static fn ($query) => $query);

        expect($tab)->toBeInstanceOf(Tab::class);
    });

    it('can set `query()` as an alias for `modifyQueryUsing()`', function (): void {
        $tab = Tab::make('Test')
            ->query(static fn ($query) => $query);

        expect($tab)->toBeInstanceOf(Tab::class);
    });

    it('can clear `modifyQueryUsing()` with `null`', function (): void {
        $tab = Tab::make('Test')
            ->modifyQueryUsing(static fn ($query) => $query)
            ->modifyQueryUsing(null);

        expect($tab)->toBeInstanceOf(Tab::class);
    });
});

describe('exclude query when resolving record', function (): void {
    it('defaults `shouldExcludeQueryWhenResolvingRecord()` to `false`', function (): void {
        $tab = Tab::make('Test');

        expect($tab->shouldExcludeQueryWhenResolvingRecord())->toBeFalse();
    });

    it('can set `excludeQueryWhenResolvingRecord()`', function (): void {
        $tab = Tab::make('Test')->excludeQueryWhenResolvingRecord();

        expect($tab->shouldExcludeQueryWhenResolvingRecord())->toBeTrue();
    });

    it('can set `excludeQueryWhenResolvingRecord()` to `false`', function (): void {
        $tab = Tab::make('Test')
            ->excludeQueryWhenResolvingRecord()
            ->excludeQueryWhenResolvingRecord(false);

        expect($tab->shouldExcludeQueryWhenResolvingRecord())->toBeFalse();
    });

    it('can set `excludeQueryWhenResolvingRecord()` with a `Closure`', function (): void {
        $tab = Tab::make('Test')
            ->excludeQueryWhenResolvingRecord(static fn (): bool => true);

        expect($tab->shouldExcludeQueryWhenResolvingRecord())->toBeTrue();
    });
});
