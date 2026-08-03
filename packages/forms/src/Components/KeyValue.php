<?php

namespace Filament\Forms\Components;

use Closure;
use Filament\Actions\Action;
use Filament\Forms\View\FormsIconAlias;
use Filament\Schemas\Components\StateCasts\KeyValueStateCast;
use Filament\Support\Components\Contracts\HasEmbeddedView;
use Filament\Support\Concerns\HasExtraAlpineAttributes;
use Filament\Support\Concerns\HasReorderAnimationDuration;
use Filament\Support\Enums\Size;
use Filament\Support\Facades\FilamentAsset;
use Filament\Support\Facades\FilamentIcon;
use Filament\Support\Icons\Heroicon;

class KeyValue extends Field implements HasEmbeddedView
{
    use HasExtraAlpineAttributes;
    use HasReorderAnimationDuration;

    protected ?string $publishedViewOverrideCheckPath = 'filament-forms::components.key-value';

    protected string | Closure | null $addActionLabel = null;

    protected string | Closure | null $deleteActionLabel = null;

    protected string | Closure | null $reorderActionLabel = null;

    protected bool | Closure $isAddable = true;

    protected bool | Closure $isDeletable = true;

    protected bool | Closure $canEditKeys = true;

    protected bool | Closure $canEditValues = true;

    protected string | Closure | null $keyLabel = null;

    protected string | Closure | null $valueLabel = null;

    protected string | Closure | null $keyPlaceholder = null;

    protected string | Closure | null $valuePlaceholder = null;

    protected bool | Closure $isReorderable = false;

    protected ?Closure $modifyAddActionUsing = null;

    protected ?Closure $modifyDeleteActionUsing = null;

    protected ?Closure $modifyReorderActionUsing = null;

    protected function setUp(): void
    {
        parent::setUp();

        $this->default([]);

        $this->registerActions([
            fn (KeyValue $component): Action => $component->getAddAction(),
            fn (KeyValue $component): Action => $component->getDeleteAction(),
            fn (KeyValue $component): Action => $component->getReorderAction(),
        ]);
    }

    public function getAddAction(): Action
    {
        $action = Action::make($this->getAddActionName())
            ->label(fn (KeyValue $component) => $component->getAddActionLabel())
            ->color('gray')
            ->livewireClickHandlerEnabled(false)
            ->link()
            ->visible(fn (): bool => $this->isAddable());

        if ($this->modifyAddActionUsing) {
            $action = $this->evaluate($this->modifyAddActionUsing, [
                'action' => $action,
            ]) ?? $action;
        }

        return $action;
    }

    public function addAction(?Closure $callback): static
    {
        $this->modifyAddActionUsing = $callback;

        return $this;
    }

    public function getAddActionName(): string
    {
        return 'add';
    }

    public function getDeleteAction(): Action
    {
        $action = Action::make($this->getDeleteActionName())
            ->label(__('filament-forms::components.key_value.actions.delete.label'))
            ->icon(FilamentIcon::resolve(FormsIconAlias::COMPONENTS_KEY_VALUE_ACTIONS_DELETE) ?? Heroicon::Trash)
            ->color('danger')
            ->livewireClickHandlerEnabled(false)
            ->iconButton()
            ->size(Size::Small)
            ->visible(fn (): bool => $this->isDeletable());

        if ($this->modifyDeleteActionUsing) {
            $action = $this->evaluate($this->modifyDeleteActionUsing, [
                'action' => $action,
            ]) ?? $action;
        }

        return $action;
    }

    public function deleteAction(?Closure $callback): static
    {
        $this->modifyDeleteActionUsing = $callback;

        return $this;
    }

    public function getDeleteActionName(): string
    {
        return 'delete';
    }

    public function getReorderAction(): Action
    {
        $action = Action::make($this->getReorderActionName())
            ->label(__('filament-forms::components.key_value.actions.reorder.label'))
            ->icon(FilamentIcon::resolve(FormsIconAlias::COMPONENTS_KEY_VALUE_ACTIONS_REORDER) ?? Heroicon::ArrowsUpDown)
            ->color('gray')
            ->livewireClickHandlerEnabled(false)
            ->iconButton()
            ->size(Size::Small)
            ->visible(fn (): bool => $this->isReorderable());

        if ($this->modifyReorderActionUsing) {
            $action = $this->evaluate($this->modifyReorderActionUsing, [
                'action' => $action,
            ]) ?? $action;
        }

        return $action;
    }

    public function reorderAction(?Closure $callback): static
    {
        $this->modifyReorderActionUsing = $callback;

        return $this;
    }

    public function getReorderActionName(): string
    {
        return 'reorder';
    }

    public function addActionLabel(string | Closure | null $label): static
    {
        $this->addActionLabel = $label;

        return $this;
    }

    public function deleteActionLabel(string | Closure | null $label): static
    {
        $this->deleteActionLabel = $label;

        return $this;
    }

    public function reorderActionLabel(string | Closure | null $label): static
    {
        $this->reorderActionLabel = $label;

        return $this;
    }

    /**
     * @deprecated Use `addActionLabel()` instead.
     */
    public function addButtonLabel(string | Closure | null $label): static
    {
        $this->addActionLabel($label);

        return $this;
    }

    /**
     * @deprecated Use `deleteActionLabel()` instead.
     */
    public function deleteButtonLabel(string | Closure | null $label): static
    {
        $this->deleteActionLabel($label);

        return $this;
    }

    /**
     * @deprecated Use `reorderActionLabel()` instead.
     */
    public function reorderButtonLabel(string | Closure | null $label): static
    {
        $this->reorderActionLabel($label);

        return $this;
    }

    public function addable(bool | Closure $condition = true): static
    {
        $this->isAddable = $condition;

        return $this;
    }

    public function deletable(bool | Closure $condition = true): static
    {
        $this->isDeletable = $condition;

        return $this;
    }

    public function editableKeys(bool | Closure $condition = true): static
    {
        $this->canEditKeys = $condition;

        return $this;
    }

    public function editableValues(bool | Closure $condition = true): static
    {
        $this->canEditValues = $condition;

        return $this;
    }

    /**
     * @deprecated Use `addable()` instead.
     */
    public function disableAddingRows(bool | Closure $condition = true): static
    {
        $this->addable(fn (KeyValue $component): bool => ! $component->evaluate($condition));

        return $this;
    }

    /**
     * @deprecated Use `deletable()` instead.
     */
    public function disableDeletingRows(bool | Closure $condition = true): static
    {
        $this->deletable(fn (KeyValue $component): bool => ! $component->evaluate($condition));

        return $this;
    }

    /**
     * @deprecated Use `editableKeys()` instead.
     */
    public function disableEditingKeys(bool | Closure $condition = true): static
    {
        $this->editableKeys(fn (KeyValue $component): bool => ! $component->evaluate($condition));

        return $this;
    }

    /**
     * @deprecated Use `editableValues()` instead.
     */
    public function disableEditingValues(bool | Closure $condition = true): static
    {
        $this->editableValues(fn (KeyValue $component): bool => ! $component->evaluate($condition));

        return $this;
    }

    public function keyLabel(string | Closure | null $label): static
    {
        $this->keyLabel = $label;

        return $this;
    }

    public function valueLabel(string | Closure | null $label): static
    {
        $this->valueLabel = $label;

        return $this;
    }

    public function keyPlaceholder(string | Closure | null $placeholder): static
    {
        $this->keyPlaceholder = $placeholder;

        return $this;
    }

    public function valuePlaceholder(string | Closure | null $placeholder): static
    {
        $this->valuePlaceholder = $placeholder;

        return $this;
    }

    public function reorderable(bool | Closure $condition = true): static
    {
        $this->isReorderable = $condition;

        return $this;
    }

    public function isAddable(): bool
    {
        return (bool) $this->evaluate($this->isAddable);
    }

    public function isDeletable(): bool
    {
        return (bool) $this->evaluate($this->isDeletable);
    }

    public function canEditKeys(): bool
    {
        return (bool) $this->evaluate($this->canEditKeys);
    }

    public function canEditValues(): bool
    {
        return (bool) $this->evaluate($this->canEditValues);
    }

    public function getAddActionLabel(): string
    {
        return $this->evaluate($this->addActionLabel) ?? __('filament-forms::components.key_value.actions.add.label');
    }

    public function getDeleteActionLabel(): string
    {
        return $this->evaluate($this->deleteActionLabel) ?? __('filament-forms::components.key_value.actions.delete.label');
    }

    public function getReorderActionLabel(): string
    {
        return $this->evaluate($this->reorderActionLabel) ?? __('filament-forms::components.key_value.actions.reorder.label');
    }

    public function getKeyLabel(): string
    {
        return $this->evaluate($this->keyLabel) ?? __('filament-forms::components.key_value.fields.key.label');
    }

    public function getValueLabel(): string
    {
        return $this->evaluate($this->valueLabel) ?? __('filament-forms::components.key_value.fields.value.label');
    }

    public function getKeyPlaceholder(): ?string
    {
        return $this->evaluate($this->keyPlaceholder);
    }

    public function getValuePlaceholder(): ?string
    {
        return $this->evaluate($this->valuePlaceholder);
    }

    public function isReorderable(): bool
    {
        return (bool) $this->evaluate($this->isReorderable);
    }

    public function toEmbeddedHtml(): string
    {
        $canEditKeys = $this->canEditKeys();
        $canEditValues = $this->canEditValues();
        $debounce = $this->getLiveDebounce();
        $id = $this->getId();
        $isAddable = $this->isAddable();
        $isDeletable = $this->isDeletable();
        $isDisabled = $this->isDisabled();
        $isReorderable = $this->isReorderable();
        $keyPlaceholder = $this->getKeyPlaceholder();
        $livewireKey = $this->getLivewireKey();
        $statePath = $this->getStatePath();
        $valuePlaceholder = $this->getValuePlaceholder();

        $wrapperAttributes = $this->getExtraAttributeBag()
            ->class(['fi-fo-key-value']);

        $alpineDivAttributes = $this->getExtraAlpineAttributeBag()
            ->class(['fi-fo-key-value-table-ctn']);

        ob_start(); ?>

        <div
            x-load
            x-load-src="<?= e(FilamentAsset::getAlpineComponentSrc('key-value', 'filament/forms')) ?>"
            x-data="keyValueFormComponent({
                        state: $wire.<?= $this->applyStateBindingModifiers("\$entangle('{$statePath}')") ?>,
                    })"
            wire:ignore
            wire:key="<?= e($livewireKey) ?>.<?= e(substr(md5(serialize([$isDisabled])), 0, 64)) ?>"
            <?= $alpineDivAttributes->toHtml() ?>
        >
                <table aria-labelledby="<?= e($id) ?>-label" id="<?= e($id) ?>" class="fi-fo-key-value-table">
                    <thead>
                        <tr>
                            <?php if ($isReorderable && (! $isDisabled)) { ?>
                                <th
                                    scope="col"
                                    x-show="rows.length"
                                    class="fi-has-action"
                                >
                                    <span class="fi-sr-only"><?= e(__('filament-forms::components.key_value.columns.reorder.label')) ?></span>
                                </th>
                            <?php } ?>

                            <th scope="col">
                                <?= e($this->getKeyLabel()) ?>
                            </th>

                            <th scope="col">
                                <?= e($this->getValueLabel()) ?>
                            </th>

                            <?php if ($isDeletable && (! $isDisabled)) { ?>
                                <th
                                    scope="col"
                                    x-show="rows.length"
                                    class="fi-has-action"
                                >
                                    <span class="fi-sr-only"><?= e(__('filament-forms::components.key_value.columns.actions.label')) ?></span>
                                </th>
                            <?php } ?>
                        </tr>
                    </thead>

                    <tbody
                        <?php if ($isReorderable) { ?>
                            x-on:end.stop="reorderRows($event)"
                            x-sortable
                            data-sortable-animation-duration="<?= e($this->getReorderAnimationDuration()) ?>"
                        <?php } ?>
                    >
                        <template
                            x-bind:key="index"
                            x-for="(row, index) in rows"
                        >
                            <tr
                                <?php if ($isReorderable) { ?>
                                    x-bind:x-sortable-item="row.key"
                                <?php } ?>
                            >
                                <?php if ($isReorderable && (! $isDisabled)) { ?>
                                    <td class="fi-has-action">
                                        <div
                                            x-sortable-handle
                                            class="fi-fo-key-value-table-row-sortable-handle"
                                        >
                                            <?= $this->getAction('reorder')->toHtml() ?>
                                        </div>
                                    </td>
                                <?php } ?>

                                <td>
                                    <input
                                        aria-label="<?= e($this->getKeyLabel()) ?>"
                                        <?= ((! $canEditKeys) || $isDisabled) ? 'disabled' : '' ?>
                                        placeholder="<?= e($keyPlaceholder) ?>"
                                        type="text"
                                        x-model="row.key"
                                        x-on:input.debounce.<?= e($debounce ?? '500ms') ?>="updateState"
                                        class="fi-input"
                                    />
                                </td>

                                <td>
                                    <input
                                        aria-label="<?= e($this->getValueLabel()) ?>"
                                        <?= ((! $canEditValues) || $isDisabled) ? 'disabled' : '' ?>
                                        placeholder="<?= e($valuePlaceholder) ?>"
                                        type="text"
                                        x-model="row.value"
                                        x-on:input.debounce.<?= e($debounce ?? '500ms') ?>="updateState"
                                        class="fi-input"
                                    />
                                </td>

                                <?php if ($isDeletable && (! $isDisabled)) { ?>
                                    <td class="fi-has-action">
                                        <div x-on:click="deleteRow(index)">
                                            <?= $this->getAction('delete')->toHtml() ?>
                                        </div>
                                    </td>
                                <?php } ?>
                            </tr>
                        </template>
                    </tbody>
                </table>

            <?php if ($isAddable && (! $isDisabled)) { ?>
                <div
                    x-on:click="addRow"
                    class="fi-fo-key-value-add-action-ctn"
                >
                    <?= $this->getAction('add')->toHtml() ?>
                </div>
            <?php } ?>
        </div>

        <?php $slotHtml = ob_get_clean();

        return $this->wrapEmbeddedHtml(
            $this->wrapInputHtml(
                $slotHtml,
                attributes: $wrapperAttributes,
            ),
            extraWrapperAttributes: ['class' => 'fi-fo-key-value-wrp'],
            labelTag: 'div',
        );
    }

    public function getDefaultStateCasts(): array
    {
        return [
            ...parent::getDefaultStateCasts(),
            app(KeyValueStateCast::class),
        ];
    }
}
