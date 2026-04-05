<?php

use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Support\Icons\Heroicon;
use Filament\Tests\Actions\TestCase;
use Illuminate\View\ComponentAttributeBag;

uses(TestCase::class);

describe('label', function (): void {
    it('returns a default translated label when not set', function (): void {
        $group = ActionGroup::make([
            Action::make('edit'),
        ]);

        expect($group->getLabel())->toBeString()->not->toBeEmpty();
    });

    it('can set custom label', function (): void {
        $group = ActionGroup::make([
            Action::make('edit'),
        ])->label('My Actions');

        expect($group->getLabel())->toBe('My Actions');
    });

    it('can set label with a `Closure`', function (): void {
        $group = ActionGroup::make([
            Action::make('edit'),
        ])->label(static fn (): string => 'Dynamic');

        expect($group->getLabel())->toBe('Dynamic');
    });
});

describe('trigger view', function (): void {
    it('returns icon-button view for `getDefaultTriggerView()` by default', function (): void {
        $group = ActionGroup::make([
            Action::make('edit'),
        ]);

        expect($group->getDefaultTriggerView())->toBe(Action::ICON_BUTTON_VIEW);
    });

    it('can set `defaultTriggerView()`', function (): void {
        $group = ActionGroup::make([
            Action::make('edit'),
        ])->defaultTriggerView(Action::BUTTON_VIEW);

        expect($group->getDefaultTriggerView())->toBe(Action::BUTTON_VIEW);
    });

    it('can set `defaultTriggerView()` with a `Closure`', function (): void {
        $group = ActionGroup::make([
            Action::make('edit'),
        ])->defaultTriggerView(static fn (): string => Action::LINK_VIEW);

        expect($group->getDefaultTriggerView())->toBe(Action::LINK_VIEW);
    });

    it('returns a view name from `getTriggerView()`', function (): void {
        $group = ActionGroup::make([
            Action::make('edit'),
        ]);

        expect($group->getTriggerView())->toBeString()->not->toBeEmpty();
    });

    it('can set explicit `triggerView()`', function (): void {
        $group = ActionGroup::make([
            Action::make('edit'),
        ])->triggerView(Action::ICON_BUTTON_VIEW);

        expect($group->getTriggerView())->toBe(Action::ICON_BUTTON_VIEW);
    });
});

describe('serialization', function (): void {
    it('can convert to array with `toArray()`', function (): void {
        $group = ActionGroup::make([
            Action::make('edit')->label('Edit')->color('primary'),
            Action::make('delete')->label('Delete')->color('danger'),
        ]);

        $array = $group->toArray();

        expect($array)->toBeArray();
        expect($array)->toHaveKey('actions');
        expect($array)->toHaveKey('label');
        expect($array)->toHaveKey('icon');
        expect($array)->toHaveKey('color');
        expect($array)->toHaveKey('triggerView');
    });

    it('can restore from array with `fromArray()`', function (): void {
        $group = ActionGroup::make([
            Action::make('edit')->label('Edit'),
        ])->color('primary');

        $array = $group->toArray();
        $restored = ActionGroup::fromArray($array);

        expect($restored)->toBeInstanceOf(ActionGroup::class);
        expect($restored->getColor())->toBe('primary');
    });
});

describe('actions management', function (): void {
    it('returns actions from `getActions()`', function (): void {
        $group = ActionGroup::make([
            Action::make('edit'),
            Action::make('delete'),
        ]);

        expect($group->getActions())->toHaveCount(2);
    });

    it('returns flat actions keyed by name from `getFlatActions()`', function (): void {
        $group = ActionGroup::make([
            Action::make('edit'),
            Action::make('delete'),
        ]);

        $flat = $group->getFlatActions();

        expect($flat)->toHaveCount(2);
        expect(array_keys($flat))->toBe(['edit', 'delete']);
    });

    it('returns `true` for `hasNonBulkAction()` when group has non-bulk actions', function (): void {
        $group = ActionGroup::make([
            Action::make('edit'),
        ]);

        expect($group->hasNonBulkAction())->toBeTrue();
    });

    it('returns `false` for `hasNonBulkAction()` when all actions are bulk', function (): void {
        $group = ActionGroup::make([
            Action::make('delete')->bulk(),
        ]);

        expect($group->hasNonBulkAction())->toBeFalse();
    });
});

describe('dropdown attributes', function (): void {
    it('can set `extraDropdownAttributes()`', function (): void {
        $group = ActionGroup::make([
            Action::make('edit'),
        ])->extraDropdownAttributes(['data-test' => 'value']);

        expect($group->getExtraDropdownAttributes())->toBe(['data-test' => 'value']);
    });

    it('can set `extraDropdownAttributes()` with a `Closure`', function (): void {
        $group = ActionGroup::make([
            Action::make('edit'),
        ])->extraDropdownAttributes(static fn (): array => ['data-test' => 'dynamic']);

        expect($group->getExtraDropdownAttributes())->toBe(['data-test' => 'dynamic']);
    });

    it('can merge `extraDropdownAttributes()`', function (): void {
        $group = ActionGroup::make([
            Action::make('edit'),
        ])
            ->extraDropdownAttributes(['data-a' => '1'])
            ->extraDropdownAttributes(['data-b' => '2'], merge: true);

        $attributes = $group->getExtraDropdownAttributes();

        expect($attributes)->toHaveKey('data-a', '1');
        expect($attributes)->toHaveKey('data-b', '2');
    });

    it('returns `ComponentAttributeBag` from `getExtraDropdownAttributeBag()`', function (): void {
        $group = ActionGroup::make([
            Action::make('edit'),
        ])->extraDropdownAttributes(['data-test' => 'bag']);

        $bag = $group->getExtraDropdownAttributeBag();

        expect($bag)->toBeInstanceOf(ComponentAttributeBag::class);
        expect($bag->get('data-test'))->toBe('bag');
    });
});

describe('icon', function (): void {
    it('has a default icon', function (): void {
        $group = ActionGroup::make([
            Action::make('edit'),
        ]);

        expect($group->getIcon())->not->toBeNull();
    });

    it('can set custom icon', function (): void {
        $group = ActionGroup::make([
            Action::make('edit'),
        ])->icon(Heroicon::PencilSquare);

        expect($group->getIcon())->toBe(Heroicon::PencilSquare);
    });
});

describe('view modes', function (): void {
    it('defaults `isButton()` to `false`', function (): void {
        $group = ActionGroup::make([Action::make('edit')]);

        expect($group->isButton())->toBeFalse();
    });

    it('can set `button()` mode', function (): void {
        $group = ActionGroup::make([Action::make('edit')])->button();

        expect($group->isButton())->toBeTrue();
    });

    it('can set `iconButton()` mode', function (): void {
        $group = ActionGroup::make([Action::make('edit')])->iconButton();

        expect($group->isIconButton())->toBeTrue();
    });

    it('can set `link()` mode', function (): void {
        $group = ActionGroup::make([Action::make('edit')])->link();

        expect($group->isLink())->toBeTrue();
    });

    it('can set `buttonGroup()` mode', function (): void {
        $group = ActionGroup::make([Action::make('edit')])->buttonGroup();

        expect($group->isButtonGroup())->toBeTrue();
    });
});
