<?php

use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Support\Enums\Width;
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

    it('has a default label on `ActionGroup`', function (): void {
        $group = ActionGroup::make([Action::make('edit')]);
        expect($group->getLabel())->toBeString();
        expect($group->getLabel())->not->toBeEmpty();
    });

    it('can set custom label on `ActionGroup`', function (): void {
        $group = ActionGroup::make([Action::make('edit')])->label('More Actions');
        expect($group->getLabel())->toBe('More Actions');
    });

    it('can set `labeledFrom()` breakpoint on `ActionGroup`', function (): void {
        $group = ActionGroup::make([Action::make('edit')]);
        expect($group->getLabeledFromBreakpoint())->toBeNull();
        $group->labeledFrom('md');
        expect($group->getLabeledFromBreakpoint())->toBe('md');
    });

    it('can set `hiddenLabel()` on `ActionGroup`', function (): void {
        $group = ActionGroup::make([Action::make('edit')]);
        expect($group->isLabelHidden())->toBeFalse();
        $group->hiddenLabel();
        expect($group->isLabelHidden())->toBeTrue();
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

    it('has a default icon on `ActionGroup`', function (): void {
        $group = ActionGroup::make([Action::make('edit')]);
        expect($group->getIcon())->not->toBeNull();
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

    it('can set a custom `triggerView()`', function (): void {
        $group = ActionGroup::make([
            Action::make('edit'),
        ])->triggerView('custom-view');

        expect($group->getTriggerView())->toBe('custom-view');
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
        expect($group->isButton())->toBeFalse();
    });

    it('can set `button()` trigger view on `ActionGroup`', function (): void {
        $group = ActionGroup::make([Action::make('edit')])->button();
        expect($group->isButton())->toBeTrue();
        expect($group->isIconButton())->toBeFalse();
        expect($group->isLink())->toBeFalse();
        expect($group->isButtonGroup())->toBeFalse();
    });

    it('can set `iconButton()` trigger view on `ActionGroup`', function (): void {
        $group = ActionGroup::make([Action::make('edit')])->iconButton();
        expect($group->isIconButton())->toBeTrue();
        expect($group->isButton())->toBeFalse();
    });

    it('can set `link()` trigger view on `ActionGroup`', function (): void {
        $group = ActionGroup::make([Action::make('edit')])->link();
        expect($group->isLink())->toBeTrue();
        expect($group->isButton())->toBeFalse();
    });

    it('can set `grouped()` trigger view on `ActionGroup`', function (): void {
        $group = ActionGroup::make([Action::make('edit')])->grouped();
        expect($group->getTriggerView())->toBe(ActionGroup::GROUPED_VIEW);
    });

    it('defaults to `iconButton` trigger view on `ActionGroup`', function (): void {
        $group = ActionGroup::make([Action::make('edit')]);
        expect($group->isIconButton())->toBeTrue();
    });

    it('can set `badge()` trigger view on `ActionGroup`', function (): void {
        $group = ActionGroup::make([Action::make('edit')])->badge();
        expect($group->isBadge())->toBeTrue();
    });

    it('returns `false` for `isBadge()` by default on `ActionGroup`', function (): void {
        $group = ActionGroup::make([Action::make('edit')]);
        expect($group->isBadge())->toBeFalse();
    });

    it('can set `badge()` with a value on `ActionGroup`', function (): void {
        $group = ActionGroup::make([Action::make('edit')])->badge(3);
        expect($group->getBadge())->toBe('3');
        expect($group->isBadge())->toBeFalse();
    });

    it('can set `outlined()` on `ActionGroup`', function (): void {
        $group = ActionGroup::make([Action::make('edit')]);
        expect($group->isOutlined())->toBeFalse();
        $group->outlined();
        expect($group->isOutlined())->toBeTrue();
    });
});

describe('dropdown configuration', function (): void {
    it('defaults `hasDropdown()` to `true`', function (): void {
        $group = ActionGroup::make([Action::make('edit')]);
        expect($group->hasDropdown())->toBeTrue();
    });

    it('can disable dropdown via `dropdown(false)`', function (): void {
        $group = ActionGroup::make([Action::make('edit')])->dropdown(false);
        expect($group->hasDropdown())->toBeFalse();
    });

    it('can set `dropdownPlacement()`', function (): void {
        $group = ActionGroup::make([Action::make('edit')]);
        expect($group->getDropdownPlacement())->toBeNull();
        $group->dropdownPlacement('bottom-end');
        expect($group->getDropdownPlacement())->toBe('bottom-end');
    });

    it('can set `dropdownMaxHeight()`', function (): void {
        $group = ActionGroup::make([Action::make('edit')]);
        expect($group->getDropdownMaxHeight())->toBeNull();
        $group->dropdownMaxHeight('300px');
        expect($group->getDropdownMaxHeight())->toBe('300px');
    });

    it('can set `dropdownOffset()`', function (): void {
        $group = ActionGroup::make([Action::make('edit')]);
        expect($group->getDropdownOffset())->toBeNull();
        $group->dropdownOffset(12);
        expect($group->getDropdownOffset())->toBe(12);
    });

    it('can set `dropdownWidth()` with a `Width` enum', function (): void {
        $group = ActionGroup::make([Action::make('edit')])->dropdownWidth(Width::Large);
        expect($group->getDropdownWidth())->toBe(Width::Large);
    });

    it('can set `dropdownWidth()` with a string that maps to enum', function (): void {
        $group = ActionGroup::make([Action::make('edit')])->dropdownWidth('lg');
        expect($group->getDropdownWidth())->toBe(Width::Large);
    });

    it('defaults `hasDropdownFlip()` to `true`', function (): void {
        $group = ActionGroup::make([Action::make('edit')]);
        expect($group->hasDropdownFlip())->toBeTrue();
    });

    it('can disable `dropdownFlip()`', function (): void {
        $group = ActionGroup::make([Action::make('edit')])->dropdownFlip(false);
        expect($group->hasDropdownFlip())->toBeFalse();
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

    it('keeps `BulkActionGroup` trigger and dropdown attributes in their respective slots', function (): void {
        $group = BulkActionGroup::make([
            Action::make('edit'),
        ])
            ->extraAttributes(static fn (): array => ['data-group-only' => 'group'])
            ->extraDropdownAttributes([
                'data-dropdown-only' => 'array',
                'data-merge-precedence' => 'first',
            ])
            ->extraDropdownAttributes(static fn (): array => [
                'data-closure-only' => 'closure',
                'data-merge-precedence' => 'second',
            ], merge: true);

        expect($group->getExtraDropdownAttributes())->toMatchArray([
            'data-dropdown-only' => 'array',
            'data-merge-precedence' => 'first',
            'data-closure-only' => 'closure',
        ]);

        $usesInternalErrors = libxml_use_internal_errors(true);
        $document = new DOMDocument;
        $document->loadHTML($group->toHtml());
        libxml_clear_errors();
        libxml_use_internal_errors($usesInternalErrors);
        $xpath = new DOMXPath($document);
        $dropdown = $xpath->query('//*[@data-dropdown-only="array"]')->item(0);

        expect($dropdown)->toBeInstanceOf(DOMElement::class);

        if (! ($dropdown instanceof DOMElement)) {
            throw new LogicException('The bulk action group dropdown was not rendered.');
        }

        expect($dropdown->getAttribute('data-dropdown-only'))->toBe('array')
            ->and($dropdown->getAttribute('data-closure-only'))->toBe('closure')
            ->and($dropdown->getAttribute('data-merge-precedence'))->toBe('first')
            ->and($dropdown->hasAttribute('data-group-only'))->toBeFalse()
            ->and($xpath->query('.//button[@data-group-only="group"]', $dropdown)->count())->toBeGreaterThan(0);
    });

    it('can set `extraDropdownAttributes()` on `ActionGroup`', function (): void {
        $group = ActionGroup::make([Action::make('edit')])->extraDropdownAttributes(['data-test' => 'dropdown']);
        $attributes = $group->getExtraDropdownAttributes();
        expect($attributes)->toHaveKey('data-test');
        expect($attributes['data-test'])->toBe('dropdown');
    });

    it('can merge `extraDropdownAttributes()` on `ActionGroup`', function (): void {
        $group = ActionGroup::make([Action::make('edit')])->extraDropdownAttributes(['data-first' => 'a'])->extraDropdownAttributes(['data-second' => 'b'], merge: true);
        $attributes = $group->getExtraDropdownAttributes();
        expect($attributes)->toHaveKey('data-first');
        expect($attributes)->toHaveKey('data-second');
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

    it('can get `getFlatActions()` from `ActionGroup`', function (): void {
        $group = ActionGroup::make([Action::make('edit'), Action::make('delete')]);
        $flat = $group->getFlatActions();
        expect($flat)->toHaveCount(2);
        expect($flat)->toHaveKeys(['edit', 'delete']);
    });

    it('flattens nested `ActionGroup` actions into `getFlatActions()`', function (): void {
        $group = ActionGroup::make([Action::make('view'), ActionGroup::make([Action::make('edit'), Action::make('delete')])]);
        $flat = $group->getFlatActions();
        expect($flat)->toHaveCount(3);
        expect($flat)->toHaveKeys(['view', 'edit', 'delete']);
    });

    it('detects non-bulk actions via `hasNonBulkAction()` on `ActionGroup`', function (): void {
        $group = ActionGroup::make([Action::make('delete')->bulk(), Action::make('export')->bulk()]);
        expect($group->hasNonBulkAction())->toBeFalse();
        $mixed = ActionGroup::make([Action::make('delete')->bulk(), Action::make('edit')]);
        expect($mixed->hasNonBulkAction())->toBeTrue();
    });

    it('can `getClone()` an `ActionGroup` independently', function (): void {
        $original = ActionGroup::make([Action::make('edit')->label('Original edit'), Action::make('delete')]);
        $clone = $original->getClone();

        $clone->getFlatActions()['edit']->label('Cloned edit');

        expect($clone->getFlatActions())->toHaveCount(2);
        expect($clone->getFlatActions()['edit']->getLabel())->toBe('Cloned edit');
        expect($original->getFlatActions()['edit']->getLabel())->toBe('Original edit');
        expect($clone->getFlatActions()['edit']->getName())->toBe('edit');
    });
});

describe('visibility', function (): void {
    it('returns `false` from `isHidden()` when the group has visible actions', function (): void {
        $group = ActionGroup::make([Action::make('edit'), Action::make('delete')]);
        expect($group->isHidden())->toBeFalse();
    });

    it('returns `true` from `isHidden()` when the group has no actions', function (): void {
        $group = ActionGroup::make([]);
        expect($group->isHidden())->toBeTrue();
    });

    it('returns `true` from `isHidden()` when explicitly hidden', function (): void {
        $group = ActionGroup::make([Action::make('edit')])->hidden();
        expect($group->isHidden())->toBeTrue();
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

    it('can serialize `ActionGroup` via `toArray()` and restore via `fromArray()`', function (): void {
        $group = ActionGroup::make([Action::make('edit')->label('Edit'), Action::make('delete')->label('Delete')])->label('Actions')->color('gray')->tooltip('Click for options');
        $array = $group->toArray();
        expect($array['label'])->toBe('Actions');
        expect($array['color'])->toBe('gray');
        expect($array['tooltip'])->toBe('Click for options');
        expect($array['actions'])->toHaveCount(2);
        $restored = ActionGroup::fromArray($array);
        expect($restored->getLabel())->toBe('Actions');
        expect($restored->getColor())->toBe('gray');
        expect($restored->getTooltip())->toBe('Click for options');
        expect($restored->getFlatActions())->toHaveCount(2);
    });
});

describe('rendering', function (): void {
    it('renders an `ActionGroup` with a dropdown trigger', function (): void {
        $group = ActionGroup::make([
            Action::make('first')->label('First'),
            Action::make('second')->label('Second'),
        ])->label('Open menu');

        $html = $group->toHtml();

        expect($html)
            ->toContain('fi-dropdown')
            ->toContain('Open menu');
    });

    it('renders an `ActionGroup` containing all child action labels', function (): void {
        $group = ActionGroup::make([
            Action::make('alpha')->label('Alpha'),
            Action::make('bravo')->label('Bravo'),
            Action::make('charlie')->label('Charlie'),
        ]);

        $html = $group->toHtml();

        expect($html)
            ->toContain('Alpha')
            ->toContain('Bravo')
            ->toContain('Charlie');
    });

    it('skips hidden actions in `ActionGroup` rendering', function (): void {
        $group = ActionGroup::make([
            Action::make('visible')->label('Visible Action'),
            Action::make('hidden')->label('Hidden Action')->hidden(),
        ]);

        $html = $group->toHtml();

        expect($html)
            ->toContain('Visible Action')
            ->not->toContain('Hidden Action');
    });
});
