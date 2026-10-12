<?php

namespace Filament\Tests\Forms\Components;

use Filament\Forms\Components\KeyValue;
use Filament\Schemas\Schema;
use Filament\Tests\Fixtures\Livewire\Livewire;
use Filament\Tests\Fixtures\Models\User;
use Filament\Tests\TestCase;
use Illuminate\Support\Facades\Artisan;

use function Filament\Tests\livewire;

uses(TestCase::class);

beforeEach(function (): void {
    Artisan::call('filament:assets');
});

describe('defaults', function (): void {
    it('defaults `isAddable()` to `true`', function (): void {
        $keyValue = KeyValue::make('meta');

        expect($keyValue->isAddable())->toBeTrue();
    });

    it('defaults `isDeletable()` to `true`', function (): void {
        $keyValue = KeyValue::make('meta');

        expect($keyValue->isDeletable())->toBeTrue();
    });
});
describe('default label values', function (): void {
    it('returns translated default for `getAddActionLabel()`', function (): void {
        $keyValue = KeyValue::make('meta');

        expect($keyValue->getAddActionLabel())->toBeString();
        expect($keyValue->getAddActionLabel())->not->toBeEmpty();
    });

    it('returns translated default for `getKeyLabel()`', function (): void {
        $keyValue = KeyValue::make('meta');

        expect($keyValue->getKeyLabel())->toBeString();
        expect($keyValue->getKeyLabel())->not->toBeEmpty();
    });

    it('returns translated default for `getValueLabel()`', function (): void {
        $keyValue = KeyValue::make('meta');

        expect($keyValue->getValueLabel())->toBeString();
        expect($keyValue->getValueLabel())->not->toBeEmpty();
    });
});

describe('properties', function (): void {
    it('can set `addActionLabel()` and get with `getAddActionLabel()`', function (): void {
        $keyValue = KeyValue::make('metadata')
            ->addActionLabel('Add row');

        expect($keyValue->getAddActionLabel())->toBe('Add row');
    });

    it('can set `deleteActionLabel()` and get with `getDeleteActionLabel()`', function (): void {
        $keyValue = KeyValue::make('metadata')
            ->deleteActionLabel('Remove row');

        expect($keyValue->getDeleteActionLabel())->toBe('Remove row');
    });

    it('can set `reorderActionLabel()` and get with `getReorderActionLabel()`', function (): void {
        $keyValue = KeyValue::make('metadata')
            ->reorderActionLabel('Drag to reorder');

        expect($keyValue->getReorderActionLabel())->toBe('Drag to reorder');
    });

    it('can set `keyLabel()` and get with `getKeyLabel()`', function (): void {
        $keyValue = KeyValue::make('metadata')
            ->keyLabel('Setting name');

        expect($keyValue->getKeyLabel())->toBe('Setting name');
    });

    it('can set `valueLabel()` and get with `getValueLabel()`', function (): void {
        $keyValue = KeyValue::make('metadata')
            ->valueLabel('Setting value');

        expect($keyValue->getValueLabel())->toBe('Setting value');
    });

    it('can set `keyPlaceholder()` and get with `getKeyPlaceholder()`', function (): void {
        $keyValue = KeyValue::make('metadata')
            ->keyPlaceholder('Enter key...');

        expect($keyValue->getKeyPlaceholder())->toBe('Enter key...');
    });

    it('returns `null` for `getKeyPlaceholder()` by default', function (): void {
        $keyValue = KeyValue::make('metadata');

        expect($keyValue->getKeyPlaceholder())->toBeNull();
    });

    it('can set `valuePlaceholder()` and get with `getValuePlaceholder()`', function (): void {
        $keyValue = KeyValue::make('metadata')
            ->valuePlaceholder('Enter value...');

        expect($keyValue->getValuePlaceholder())->toBe('Enter value...');
    });

    it('returns `null` for `getValuePlaceholder()` by default', function (): void {
        $keyValue = KeyValue::make('metadata');

        expect($keyValue->getValuePlaceholder())->toBeNull();
    });

    it('can set `addable()` and check `isAddable()`', function (): void {
        $addable = KeyValue::make('metadata')->addable();
        $nonAddable = KeyValue::make('metadata')->addable(false);

        expect($addable->isAddable())->toBeTrue();
        expect($nonAddable->isAddable())->toBeFalse();
    });

    it('can set `deletable()` and check `isDeletable()`', function (): void {
        $deletable = KeyValue::make('metadata')->deletable();
        $nonDeletable = KeyValue::make('metadata')->deletable(false);

        expect($deletable->isDeletable())->toBeTrue();
        expect($nonDeletable->isDeletable())->toBeFalse();
    });

    it('can set `editableKeys()` and check `canEditKeys()`', function (): void {
        $editable = KeyValue::make('metadata')->editableKeys();
        $nonEditable = KeyValue::make('metadata')->editableKeys(false);

        expect($editable->canEditKeys())->toBeTrue();
        expect($nonEditable->canEditKeys())->toBeFalse();
    });

    it('has `canEditKeys()` returning true by default', function (): void {
        $keyValue = KeyValue::make('metadata');

        expect($keyValue->canEditKeys())->toBeTrue();
    });

    it('can set `editableValues()` and check `canEditValues()`', function (): void {
        $editable = KeyValue::make('metadata')->editableValues();
        $nonEditable = KeyValue::make('metadata')->editableValues(false);

        expect($editable->canEditValues())->toBeTrue();
        expect($nonEditable->canEditValues())->toBeFalse();
    });

    it('has `canEditValues()` returning true by default', function (): void {
        $keyValue = KeyValue::make('metadata');

        expect($keyValue->canEditValues())->toBeTrue();
    });

    it('can set `reorderable()` and check `isReorderable()`', function (): void {
        $reorderable = KeyValue::make('metadata')->reorderable();
        $nonReorderable = KeyValue::make('metadata')->reorderable(false);

        expect($reorderable->isReorderable())->toBeTrue();
        expect($nonReorderable->isReorderable())->toBeFalse();
    });

    it('has `isReorderable()` returning false by default', function (): void {
        $keyValue = KeyValue::make('metadata');

        expect($keyValue->isReorderable())->toBeFalse();
    });

    it('can return correct action names', function (): void {
        $keyValue = KeyValue::make('metadata');

        expect($keyValue->getAddActionName())->toBe('add');
        expect($keyValue->getDeleteActionName())->toBe('delete');
        expect($keyValue->getReorderActionName())->toBe('reorder');
    });
});

describe('closure support', function (): void {
    it('can set `addActionLabel()` with a `Closure`', function (): void {
        $keyValue = KeyValue::make('meta')
            ->addActionLabel(static fn (): string => 'Custom add');

        expect($keyValue->getAddActionLabel())->toBe('Custom add');
    });

    it('can set `keyLabel()` with a `Closure`', function (): void {
        $keyValue = KeyValue::make('meta')
            ->keyLabel(static fn (): string => 'Property');

        expect($keyValue->getKeyLabel())->toBe('Property');
    });

    it('can set `valueLabel()` with a `Closure`', function (): void {
        $keyValue = KeyValue::make('meta')
            ->valueLabel(static fn (): string => 'Content');

        expect($keyValue->getValueLabel())->toBe('Content');
    });

    it('can set `keyPlaceholder()` with a `Closure`', function (): void {
        $keyValue = KeyValue::make('meta')
            ->keyPlaceholder(static fn (): string => 'type key...');

        expect($keyValue->getKeyPlaceholder())->toBe('type key...');
    });

    it('can set `valuePlaceholder()` with a `Closure`', function (): void {
        $keyValue = KeyValue::make('meta')
            ->valuePlaceholder(static fn (): string => 'type value...');

        expect($keyValue->getValuePlaceholder())->toBe('type value...');
    });

    it('can set `addable()` with a `Closure`', function (): void {
        $keyValue = KeyValue::make('meta')
            ->addable(static fn (): bool => false);

        expect($keyValue->isAddable())->toBeFalse();
    });

    it('can set `deletable()` with a `Closure`', function (): void {
        $keyValue = KeyValue::make('meta')
            ->deletable(static fn (): bool => false);

        expect($keyValue->isDeletable())->toBeFalse();
    });

    it('can set `reorderable()` with a `Closure`', function (): void {
        $keyValue = KeyValue::make('meta')
            ->reorderable(static fn (): bool => true);

        expect($keyValue->isReorderable())->toBeTrue();
    });

    it('can set `editableKeys()` with a `Closure`', function (): void {
        $keyValue = KeyValue::make('meta')
            ->editableKeys(static fn (): bool => false);

        expect($keyValue->canEditKeys())->toBeFalse();
    });

    it('can set `editableValues()` with a `Closure`', function (): void {
        $keyValue = KeyValue::make('meta')
            ->editableValues(static fn (): bool => false);

        expect($keyValue->canEditValues())->toBeFalse();
    });

    it('can set `deleteActionLabel()` with a `Closure`', function (): void {
        $keyValue = KeyValue::make('meta')
            ->deleteActionLabel(static fn (): string => 'Remove');

        expect($keyValue->getDeleteActionLabel())->toBe('Remove');
    });

    it('can set `reorderActionLabel()` with a `Closure`', function (): void {
        $keyValue = KeyValue::make('meta')
            ->reorderActionLabel(static fn (): string => 'Sort');

        expect($keyValue->getReorderActionLabel())->toBe('Sort');
    });
});

describe('action modifiers', function (): void {
    it('can modify add action via `addAction()` callback', function (): void {
        $keyValue = KeyValue::make('meta')
            ->addAction(static fn ($action) => $action->label('New Row'));

        $action = $keyValue->getAddAction();

        expect($action->getLabel())->toBe('New Row');
    });

    it('can modify delete action via `deleteAction()` callback', function (): void {
        $keyValue = KeyValue::make('meta')
            ->deleteAction(static fn ($action) => $action->color('warning'));

        $action = $keyValue->getDeleteAction();

        expect($action->getColor())->toBe('warning');
    });

    it('can modify reorder action via `reorderAction()` callback', function (): void {
        $keyValue = KeyValue::make('meta')
            ->reorderAction(static fn ($action) => $action->color('primary'));

        $action = $keyValue->getReorderAction();

        expect($action->getColor())->toBe('primary');
    });

    it('returns fluent `$this` from `addAction()`', function (): void {
        $keyValue = KeyValue::make('meta');

        $result = $keyValue->addAction(static fn ($action) => $action);

        expect($result)->toBe($keyValue);
    });
});

describe('reorder animation', function (): void {
    it('defaults `getReorderAnimationDuration()` to `300`', function (): void {
        $keyValue = KeyValue::make('meta');

        expect($keyValue->getReorderAnimationDuration())->toBe(300);
    });

    it('can set `reorderAnimationDuration()`', function (): void {
        $keyValue = KeyValue::make('meta')
            ->reorderAnimationDuration(500);

        expect($keyValue->getReorderAnimationDuration())->toBe(500);
    });

    it('can set `reorderAnimationDuration()` with a `Closure`', function (): void {
        $keyValue = KeyValue::make('meta')
            ->reorderAnimationDuration(static fn (): int => 0);

        expect($keyValue->getReorderAnimationDuration())->toBe(0);
    });
});
describe('rendering and state', function (): void {
    it('can render', function (): void {
        livewire(TestComponentWithKeyValue::class)
            ->assertSuccessful();
    });

    it('can set and get state', function (): void {
        livewire(TestComponentWithKeyValue::class)
            ->fillForm(['metadata' => ['key1' => 'value1', 'key2' => 'value2']])
            ->assertSchemaStateSet(['metadata' => ['key1' => 'value1', 'key2' => 'value2']]);
    });

    it('can render when not addable', function (): void {
        livewire(TestComponentWithReadOnlyKeyValue::class)
            ->assertSuccessful();
    });

    it('can render when `reorderable()`', function (): void {
        livewire(TestComponentWithReorderableKeyValue::class)
            ->assertSuccessful();
    });
});

describe('rendering', function (): void {
    it('can render with `keyLabel()`', function (): void {
        livewire(RenderKeyValueWithKeyLabel::class)
            ->assertSuccessful()
            ->assertSeeHtml('Setting name');
    });

    it('can render with `keyLabel()` set via `Closure`', function (): void {
        livewire(RenderKeyValueWithClosureKeyLabel::class)
            ->assertSuccessful()
            ->assertSeeHtml('Property');
    });

    it('can render with `valueLabel()`', function (): void {
        livewire(RenderKeyValueWithValueLabel::class)
            ->assertSuccessful()
            ->assertSeeHtml('Setting value');
    });

    it('can render with `valueLabel()` set via `Closure`', function (): void {
        livewire(RenderKeyValueWithClosureValueLabel::class)
            ->assertSuccessful()
            ->assertSeeHtml('Content');
    });

    it('can render with `keyPlaceholder()`', function (): void {
        livewire(RenderKeyValueWithKeyPlaceholder::class)
            ->assertSuccessful();
    });

    it('can render with `keyPlaceholder()` set via `Closure`', function (): void {
        livewire(RenderKeyValueWithClosureKeyPlaceholder::class)
            ->assertSuccessful();
    });

    it('can render with `valuePlaceholder()`', function (): void {
        livewire(RenderKeyValueWithValuePlaceholder::class)
            ->assertSuccessful();
    });

    it('can render with `valuePlaceholder()` set via `Closure`', function (): void {
        livewire(RenderKeyValueWithClosureValuePlaceholder::class)
            ->assertSuccessful();
    });

    it('can render with `editableKeys(false)`', function (): void {
        livewire(RenderKeyValueWithNonEditableKeys::class)
            ->assertSuccessful();
    });

    it('can render with `editableKeys()` set via `Closure`', function (): void {
        livewire(RenderKeyValueWithClosureEditableKeys::class)
            ->assertSuccessful();
    });

    it('can render with `editableValues(false)`', function (): void {
        livewire(RenderKeyValueWithNonEditableValues::class)
            ->assertSuccessful();
    });

    it('can render with `editableValues()` set via `Closure`', function (): void {
        livewire(RenderKeyValueWithClosureEditableValues::class)
            ->assertSuccessful();
    });

    it('can render with `addable()` set via `Closure`', function (): void {
        livewire(RenderKeyValueWithClosureAddable::class)
            ->assertSuccessful();
    });

    it('can render with `deletable()` set via `Closure`', function (): void {
        livewire(RenderKeyValueWithClosureDeletable::class)
            ->assertSuccessful();
    });

    it('can render with `reorderable()` set via `Closure`', function (): void {
        livewire(RenderKeyValueWithClosureReorderable::class)
            ->assertSuccessful();
    });

    it('can render with `reorderAnimationDuration()`', function (): void {
        livewire(RenderKeyValueWithReorderAnimationDuration::class)
            ->assertSuccessful();
    });

    it('can render with `reorderAnimationDuration()` set via `Closure`', function (): void {
        livewire(RenderKeyValueWithClosureReorderAnimationDuration::class)
            ->assertSuccessful();
    });

    it('can render with `addActionLabel()`', function (): void {
        livewire(RenderKeyValueWithAddActionLabel::class)
            ->assertSuccessful();
    });

    it('can render with `addActionLabel()` set via `Closure`', function (): void {
        livewire(RenderKeyValueWithClosureAddActionLabel::class)
            ->assertSuccessful();
    });

    it('can render with `deleteActionLabel()`', function (): void {
        livewire(RenderKeyValueWithDeleteActionLabel::class)
            ->assertSuccessful();
    });

    it('can render with `deleteActionLabel()` set via `Closure`', function (): void {
        livewire(RenderKeyValueWithClosureDeleteActionLabel::class)
            ->assertSuccessful();
    });

    it('can render with `reorderActionLabel()`', function (): void {
        livewire(RenderKeyValueWithReorderActionLabel::class)
            ->assertSuccessful();
    });

    it('can render with `reorderActionLabel()` set via `Closure`', function (): void {
        livewire(RenderKeyValueWithClosureReorderActionLabel::class)
            ->assertSuccessful();
    });
});

describe('browser interactions', function (): void {
    it('supports row actions and configured controls in the browser', function (): void {
        $this->actingAs(User::factory()->create());

        $page = visit('/key-value-test');

        $page->assertPresent('[data-testid="basic-key-value"] .fi-fo-key-value-add-action-ctn')
            ->assertCount('[data-testid="basic-key-value"] tbody tr', 1)
            ->click('[data-testid="basic-key-value"] .fi-fo-key-value-add-action-ctn')
            ->assertCount('[data-testid="basic-key-value"] tbody tr', 2)
            ->click('[data-testid="reorderable-key-value"] .fi-fo-key-value-add-action-ctn')
            ->click('[data-testid="reorderable-key-value"] .fi-fo-key-value-add-action-ctn')
            ->assertCount('[data-testid="reorderable-key-value"] .fi-fo-key-value-table-row-sortable-handle', 3)
            ->assertMissing('[data-testid="not-addable-key-value"] .fi-fo-key-value-add-action-ctn')
            ->assertMissing('[data-testid="not-deletable-key-value"] tbody .fi-has-action')
            ->assertMissing('[data-testid="disabled-key-value"] .fi-fo-key-value-add-action-ctn')
            ->assertMissing('[data-testid="disabled-key-value"] [data-reorder-direction]')
            ->assertMissing('[data-testid="hidden-reorder-key-value"] [data-reorder-direction]')
            ->assertMissing('[data-testid="hidden-reorder-key-value"] [x-sortable]')
            ->assertMissing('[data-testid="disabled-reorder-key-value"] [data-reorder-direction]')
            ->assertMissing('[data-testid="disabled-reorder-key-value"] [x-sortable]')
            ->assertMissing('[data-testid="basic-key-value"] [data-reorder-direction]');

        $controls = '[data-testid="reorderable-key-value"] [data-reorder-direction]';
        foreach (range(1, 6) as $position) {
            $page->assertAttribute(':nth-match(' . $controls . ', ' . $position . ')', 'tabindex', '-1');
        }

        $page->assertNoSmoke()->assertNoAccessibilityIssues();

        $page->inDarkMode()->assertNoAccessibilityIssues();
    });

    it('preserves drafts and saved values when dragging rows', function (): void {
        $this->actingAs(User::factory()->create());

        $field = '[data-testid="accessible-key-value"]';
        $keys = $field . ' input[aria-label="Key"]';
        $values = $field . ' input[aria-label="Value"]';
        $moveUp = $field . ' [data-reorder-direction="up"]';
        $moveDown = $field . ' [data-reorder-direction="down"]';
        $handles = $field . ' [x-sortable-handle]';
        $page = visit('/key-value-test');

        $page->assertPresent($field . ' [x-sortable]')
            ->assertDisabled(':nth-match(' . $moveUp . ', 1)')
            ->assertDisabled(':nth-match(' . $moveDown . ', 3)')
            ->fill(':nth-match(' . $values . ', 1)', 'Unsaved first')
            ->fill(':nth-match(' . $keys . ', 2)', 'alpha')
            ->fill(':nth-match(' . $values . ', 2)', 'Unsaved duplicate')
            ->click('[data-testid="add-accessible-row"]')
            ->fill(':nth-match(' . $values . ', 4)', 'Blank draft')
            ->drag(':nth-match(' . $handles . ', 1)', ':nth-match(' . $handles . ', 4)')
            ->assertValue(':nth-match(' . $values . ', 1)', 'Unsaved duplicate')
            ->assertValue(':nth-match(' . $keys . ', 2)', 'gamma')
            ->assertValue(':nth-match(' . $values . ', 3)', 'Blank draft')
            ->assertValue(':nth-match(' . $values . ', 4)', 'Unsaved first')
            ->click('[data-testid="save"]')
            ->assertSeeIn('[data-testid="saved"]', '{"alpha":"Unsaved first","gamma":"Third"}')
            ->drag(':nth-match(' . $handles . ', 4)', ':nth-match(' . $handles . ', 1)')
            ->assertValue(':nth-match(' . $values . ', 1)', 'Unsaved first')
            ->assertValue(':nth-match(' . $values . ', 2)', 'Unsaved duplicate')
            ->assertValue(':nth-match(' . $keys . ', 3)', 'gamma')
            ->assertValue(':nth-match(' . $values . ', 4)', 'Blank draft')
            ->click('[data-testid="save"]')
            ->assertSeeIn('[data-testid="saved"]', '{"alpha":"Unsaved duplicate","gamma":"Third"}')
            ->assertValue(':nth-match(' . $values . ', 1)', 'Unsaved first')
            ->assertNoSmoke()->assertNoAccessibilityIssues();

        $page->click(':nth-match(' . $field . ' button[aria-label="Delete row"], 3)')
            ->click(':nth-match(' . $field . ' button[aria-label="Delete row"], 2)')
            ->click(':nth-match(' . $field . ' button[aria-label="Delete row"], 1)')
            ->click('[data-testid="save"]')
            ->assertPresent('[data-validation-error]')
            ->assertCount($field . ' tbody tr', 1)
            ->assertValue(':nth-match(' . $values . ', 1)', 'Blank draft')
            ->assertDisabled(':nth-match(' . $moveUp . ', 1)')
            ->assertDisabled(':nth-match(' . $moveDown . ', 1)')
            ->assertNoSmoke()->assertNoAccessibilityIssues();

        $page->inDarkMode()->assertNoAccessibilityIssues();
    });
});
class TestComponentWithKeyValue extends Livewire
{
    public function form(Schema $form): Schema
    {
        return $form
            ->schema([
                KeyValue::make('metadata'),
            ])
            ->statePath('data');
    }
}
class TestComponentWithReadOnlyKeyValue extends Livewire
{
    public function form(Schema $form): Schema
    {
        return $form
            ->schema([
                KeyValue::make('metadata')
                    ->addable(false)
                    ->deletable(false),
            ])
            ->statePath('data');
    }
}
class TestComponentWithReorderableKeyValue extends Livewire
{
    public function form(Schema $form): Schema
    {
        return $form
            ->schema([
                KeyValue::make('metadata')->reorderable(),
            ])
            ->statePath('data');
    }
}

class RenderKeyValueWithKeyLabel extends Livewire
{
    public function form(Schema $form): Schema
    {
        return $form->schema([KeyValue::make('meta')->keyLabel('Setting name')])->statePath('data');
    }
}

class RenderKeyValueWithClosureKeyLabel extends Livewire
{
    public function form(Schema $form): Schema
    {
        return $form->schema([KeyValue::make('meta')->keyLabel(static fn (): string => 'Property')])->statePath('data');
    }
}

class RenderKeyValueWithValueLabel extends Livewire
{
    public function form(Schema $form): Schema
    {
        return $form->schema([KeyValue::make('meta')->valueLabel('Setting value')])->statePath('data');
    }
}

class RenderKeyValueWithClosureValueLabel extends Livewire
{
    public function form(Schema $form): Schema
    {
        return $form->schema([KeyValue::make('meta')->valueLabel(static fn (): string => 'Content')])->statePath('data');
    }
}

class RenderKeyValueWithKeyPlaceholder extends Livewire
{
    public function form(Schema $form): Schema
    {
        return $form->schema([KeyValue::make('meta')->keyPlaceholder('Enter key...')])->statePath('data');
    }
}

class RenderKeyValueWithClosureKeyPlaceholder extends Livewire
{
    public function form(Schema $form): Schema
    {
        return $form->schema([KeyValue::make('meta')->keyPlaceholder(static fn (): string => 'type key...')])->statePath('data');
    }
}

class RenderKeyValueWithValuePlaceholder extends Livewire
{
    public function form(Schema $form): Schema
    {
        return $form->schema([KeyValue::make('meta')->valuePlaceholder('Enter value...')])->statePath('data');
    }
}

class RenderKeyValueWithClosureValuePlaceholder extends Livewire
{
    public function form(Schema $form): Schema
    {
        return $form->schema([KeyValue::make('meta')->valuePlaceholder(static fn (): string => 'type value...')])->statePath('data');
    }
}

class RenderKeyValueWithNonEditableKeys extends Livewire
{
    public function form(Schema $form): Schema
    {
        return $form->schema([KeyValue::make('meta')->editableKeys(false)])->statePath('data');
    }
}

class RenderKeyValueWithClosureEditableKeys extends Livewire
{
    public function form(Schema $form): Schema
    {
        return $form->schema([KeyValue::make('meta')->editableKeys(static fn (): bool => false)])->statePath('data');
    }
}

class RenderKeyValueWithNonEditableValues extends Livewire
{
    public function form(Schema $form): Schema
    {
        return $form->schema([KeyValue::make('meta')->editableValues(false)])->statePath('data');
    }
}

class RenderKeyValueWithClosureEditableValues extends Livewire
{
    public function form(Schema $form): Schema
    {
        return $form->schema([KeyValue::make('meta')->editableValues(static fn (): bool => false)])->statePath('data');
    }
}

class RenderKeyValueWithClosureAddable extends Livewire
{
    public function form(Schema $form): Schema
    {
        return $form->schema([KeyValue::make('meta')->addable(static fn (): bool => false)])->statePath('data');
    }
}

class RenderKeyValueWithClosureDeletable extends Livewire
{
    public function form(Schema $form): Schema
    {
        return $form->schema([KeyValue::make('meta')->deletable(static fn (): bool => false)])->statePath('data');
    }
}

class RenderKeyValueWithClosureReorderable extends Livewire
{
    public function form(Schema $form): Schema
    {
        return $form->schema([KeyValue::make('meta')->reorderable(static fn (): bool => true)])->statePath('data');
    }
}

class RenderKeyValueWithReorderAnimationDuration extends Livewire
{
    public function form(Schema $form): Schema
    {
        return $form->schema([KeyValue::make('meta')->reorderable()->reorderAnimationDuration(500)])->statePath('data');
    }
}

class RenderKeyValueWithClosureReorderAnimationDuration extends Livewire
{
    public function form(Schema $form): Schema
    {
        return $form->schema([KeyValue::make('meta')->reorderable()->reorderAnimationDuration(static fn (): int => 0)])->statePath('data');
    }
}

class RenderKeyValueWithAddActionLabel extends Livewire
{
    public function form(Schema $form): Schema
    {
        return $form->schema([KeyValue::make('meta')->addActionLabel('Add row')])->statePath('data');
    }
}

class RenderKeyValueWithClosureAddActionLabel extends Livewire
{
    public function form(Schema $form): Schema
    {
        return $form->schema([KeyValue::make('meta')->addActionLabel(static fn (): string => 'Custom add')])->statePath('data');
    }
}

class RenderKeyValueWithDeleteActionLabel extends Livewire
{
    public function form(Schema $form): Schema
    {
        return $form->schema([KeyValue::make('meta')->deleteActionLabel('Remove row')])->statePath('data');
    }
}

class RenderKeyValueWithClosureDeleteActionLabel extends Livewire
{
    public function form(Schema $form): Schema
    {
        return $form->schema([KeyValue::make('meta')->deleteActionLabel(static fn (): string => 'Remove')])->statePath('data');
    }
}

class RenderKeyValueWithReorderActionLabel extends Livewire
{
    public function form(Schema $form): Schema
    {
        return $form->schema([KeyValue::make('meta')->reorderable()->reorderActionLabel('Drag to reorder')])->statePath('data');
    }
}

class RenderKeyValueWithClosureReorderActionLabel extends Livewire
{
    public function form(Schema $form): Schema
    {
        return $form->schema([KeyValue::make('meta')->reorderable()->reorderActionLabel(static fn (): string => 'Sort')])->statePath('data');
    }
}
