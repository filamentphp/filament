<?php

// Historical callbacks from `0a007375a4b796a984703e40d52b1808ee193973`, selected by the 77 deprecated `assert*` stubs.
// Only comments/imports/formatting are normalized; `Halted` factory aliases resolve to `Mounted`.
return [
    'assertFormSet' => function (array|\Closure $state, string $form = 'form'): static {
        $this->assertSchemaStateSet($state, $form);
        return $this;
    },
    'assertFormExists' => function (string $name = 'form'): static {
        $this->assertSchemaExists($name);
        return $this;
    },
    'assertFormComponentExists' => function (string $componentKey, string|\Closure $form = 'form', ?\Closure $checkComponentUsing = null): static {
        if ($form instanceof \Closure) {
            $checkComponentUsing = $form;
            $form = 'form';
        }
        $this->assertSchemaComponentExists($componentKey, $form, $checkComponentUsing);
        return $this;
    },
    'assertFormComponentDoesNotExist' => function (string $componentKey, string $form = 'form'): static {
        $this->assertSchemaComponentDoesNotExist($componentKey, $form);
        return $this;
    },
    'assertFormComponentActionExists' => function (string|array $components, string|array $actions, string $formName = 'form'): static {
        $actions = $this->parseNestedFormComponentActions($components, $actions, $formName);
        $this->assertActionExists($actions);
        return $this;
    },
    'assertFormComponentActionDoesNotExist' => function (string|array $components, string|array $actions, string $formName = 'form'): static {
        $actions = $this->parseNestedFormComponentActions($components, $actions, $formName);
        $this->assertActionDoesNotExist($actions);
        return $this;
    },
    'assertFormComponentActionVisible' => function (string|array $components, string|array $actions, array $arguments = [], string $formName = 'form'): static {
        $actions = $this->parseNestedFormComponentActions($components, $actions, $formName, $arguments);
        $this->assertActionVisible($actions);
        return $this;
    },
    'assertFormComponentActionHidden' => function (string|array $components, string|array $actions, array $arguments = [], string $formName = 'form'): static {
        $actions = $this->parseNestedFormComponentActions($components, $actions, $formName, $arguments);
        $this->assertActionHidden($actions);
        return $this;
    },
    'assertFormComponentActionEnabled' => function (string|array $components, string|array $actions, array $arguments = [], string $formName = 'form'): static {
        $actions = $this->parseNestedFormComponentActions($components, $actions, $formName, $arguments);
        $this->assertActionEnabled($actions);
        return $this;
    },
    'assertFormComponentActionDisabled' => function (string|array $components, string|array $actions, array $arguments = [], string $formName = 'form'): static {
        $actions = $this->parseNestedFormComponentActions($components, $actions, $formName, $arguments);
        $this->assertActionDisabled($actions);
        return $this;
    },
    'assertFormComponentActionMounted' => function (string|array $components, string|array $actions, string $formName = 'form'): static {
        $actions = $this->parseNestedFormComponentActions($components, $actions, $formName);
        $this->assertActionMounted($actions);
        return $this;
    },
    'assertFormComponentActionNotMounted' => function (string|array $components, string|array $actions, string $formName = 'form'): static {
        $actions = $this->parseNestedFormComponentActions($components, $actions, $formName);
        $this->assertActionNotMounted($actions);
        return $this;
    },
    'assertFormComponentActionHalted' => function (string|array $components, string|array $actions, string $formName = 'form'): static {
        $actions = $this->parseNestedFormComponentActions($components, $actions, $formName);
        $this->assertActionMounted($actions);
        return $this;
    },
    'assertFormComponentActionHasIcon' => function (string|array $components, string|array $actions, string|\BackedEnum $icon, array $arguments = [], string $formName = 'form'): static {
        $actions = $this->parseNestedFormComponentActions($components, $actions, $formName, $arguments);
        $this->assertActionHasIcon($actions, $icon);
        return $this;
    },
    'assertFormComponentActionDoesNotHaveIcon' => function (string|array $components, string|array $actions, string|\BackedEnum $icon, array $arguments = [], string $formName = 'form'): static {
        $actions = $this->parseNestedFormComponentActions($components, $actions, $formName, $arguments);
        $this->assertActionDoesNotHaveIcon($actions, $icon);
        return $this;
    },
    'assertFormComponentActionHasLabel' => function (string|array $components, string|array $actions, string $label, array $arguments = [], string $formName = 'form'): static {
        $actions = $this->parseNestedFormComponentActions($components, $actions, $formName, $arguments);
        $this->assertActionHasLabel($actions, $label);
        return $this;
    },
    'assertFormComponentActionDoesNotHaveLabel' => function (string|array $components, string|array $actions, string $label, array $arguments = [], string $formName = 'form'): static {
        $actions = $this->parseNestedFormComponentActions($components, $actions, $formName, $arguments);
        $this->assertActionDoesNotHaveLabel($actions, $label);
        return $this;
    },
    'assertFormComponentActionHasColor' => function (string|array $components, string|array $actions, string|array $color, array $arguments = [], string $formName = 'form'): static {
        $actions = $this->parseNestedFormComponentActions($components, $actions, $formName, $arguments);
        $this->assertActionHasColor($actions, $color);
        return $this;
    },
    'assertFormComponentActionDoesNotHaveColor' => function (string|array $components, string|array $actions, string|array $color, array $arguments = [], string $formName = 'form'): static {
        $actions = $this->parseNestedFormComponentActions($components, $actions, $formName, $arguments);
        $this->assertActionDoesNotHaveColor($actions, $color);
        return $this;
    },
    'assertFormComponentActionHasUrl' => function (string|array $components, string|array $actions, string $url, array $arguments = [], string $formName = 'form'): static {
        $actions = $this->parseNestedFormComponentActions($components, $actions, $formName, $arguments);
        $this->assertActionHasUrl($actions, $url);
        return $this;
    },
    'assertFormComponentActionDoesNotHaveUrl' => function (string|array $components, string|array $actions, string $url, array $arguments = [], string $formName = 'form'): static {
        $actions = $this->parseNestedFormComponentActions($components, $actions, $formName, $arguments);
        $this->assertActionDoesNotHaveUrl($actions, $url);
        return $this;
    },
    'assertFormComponentActionShouldOpenUrlInNewTab' => function (string|array $components, string|array $actions, array $arguments = [], string $formName = 'form'): static {
        $actions = $this->parseNestedFormComponentActions($components, $actions, $formName, $arguments);
        $this->assertActionShouldOpenUrlInNewTab($actions);
        return $this;
    },
    'assertFormComponentActionShouldNotOpenUrlInNewTab' => function (string|array $components, string|array $actions, array $arguments = [], string $formName = 'form'): static {
        $actions = $this->parseNestedFormComponentActions($components, $actions, $formName, $arguments);
        $this->assertActionShouldNotOpenUrlInNewTab($actions);
        return $this;
    },
    'assertInfolistActionExists' => function (string $component, string|array $actions, string $schema = 'infolist'): static {
        $actions = $this->parseNestedInfolistActions($component, $actions, $schema);
        $this->assertActionExists($actions);
        return $this;
    },
    'assertInfolistActionDoesNotExist' => function (string $component, string|array $actions, string $schema = 'infolist'): static {
        $actions = $this->parseNestedInfolistActions($component, $actions, $schema);
        $this->assertActionDoesNotExist($actions);
        return $this;
    },
    'assertInfolistActionVisible' => function (string $component, string|array $actions, string $schema = 'infolist'): static {
        $actions = $this->parseNestedInfolistActions($component, $actions, $schema);
        $this->assertActionVisible($actions);
        return $this;
    },
    'assertInfolistActionHidden' => function (string $component, string|array $actions, string $schema = 'infolist'): static {
        $actions = $this->parseNestedInfolistActions($component, $actions, $schema);
        $this->assertActionHidden($actions);
        return $this;
    },
    'assertInfolistActionEnabled' => function (string $component, string|array $actions, string $schema = 'infolist'): static {
        $actions = $this->parseNestedInfolistActions($component, $actions, $schema);
        $this->assertActionEnabled($actions);
        return $this;
    },
    'assertInfolistActionDisabled' => function (string $component, string|array $actions, string $schema = 'infolist'): static {
        $actions = $this->parseNestedInfolistActions($component, $actions, $schema);
        $this->assertActionDisabled($actions);
        return $this;
    },
    'assertInfolistActionMounted' => function (string $component, string|array $actions, string $schema = 'infolist'): static {
        $actions = $this->parseNestedInfolistActions($component, $actions, $schema);
        $this->assertActionMounted($actions);
        return $this;
    },
    'assertInfolistActionNotMounted' => function (string $component, string|array $actions, string $schema = 'infolist'): static {
        $actions = $this->parseNestedInfolistActions($component, $actions, $schema);
        $this->assertActionNotMounted($actions);
        return $this;
    },
    'assertInfolistActionHalted' => function (string $component, string|array $actions, string $schema = 'infolist'): static {
        $actions = $this->parseNestedInfolistActions($component, $actions, $schema);
        $this->assertActionMounted($actions);
        return $this;
    },
    'assertInfolistActionHasIcon' => function (string $component, string|array $actions, string|\BackedEnum $icon, string $schema = 'infolist'): static {
        $actions = $this->parseNestedInfolistActions($component, $actions, $schema);
        $this->assertActionHasIcon($actions, $icon);
        return $this;
    },
    'assertInfolistActionDoesNotHaveIcon' => function (string $component, string|array $actions, string|\BackedEnum $icon, string $schema = 'infolist'): static {
        $actions = $this->parseNestedInfolistActions($component, $actions, $schema);
        $this->assertActionDoesNotHaveIcon($actions, $icon);
        return $this;
    },
    'assertInfolistActionHasLabel' => function (string $component, string|array $actions, string $label, string $schema = 'infolist'): static {
        $actions = $this->parseNestedInfolistActions($component, $actions, $schema);
        $this->assertActionHasLabel($actions, $label);
        return $this;
    },
    'assertInfolistActionDoesNotHaveLabel' => function (string $component, string|array $actions, string $label, string $schema = 'infolist'): static {
        $actions = $this->parseNestedInfolistActions($component, $actions, $schema);
        $this->assertActionDoesNotHaveLabel($actions, $label);
        return $this;
    },
    'assertInfolistActionHasColor' => function (string $component, string|array $actions, string|array $color, string $schema = 'infolist'): static {
        $actions = $this->parseNestedInfolistActions($component, $actions, $schema);
        $this->assertActionHasColor($actions, $color);
        return $this;
    },
    'assertInfolistActionDoesNotHaveColor' => function (string $component, string|array $actions, string|array $color, string $schema = 'infolist'): static {
        $actions = $this->parseNestedInfolistActions($component, $actions, $schema);
        $this->assertActionDoesNotHaveColor($actions, $color);
        return $this;
    },
    'assertInfolistActionHasUrl' => function (string $component, string|array $actions, string $url, string $schema = 'infolist'): static {
        $actions = $this->parseNestedInfolistActions($component, $actions, $schema);
        $this->assertActionHasUrl($actions, $url);
        return $this;
    },
    'assertInfolistActionDoesNotHaveUrl' => function (string $component, string|array $actions, string $url, string $schema = 'infolist'): static {
        $actions = $this->parseNestedInfolistActions($component, $actions, $schema);
        $this->assertActionDoesNotHaveUrl($actions, $url);
        return $this;
    },
    'assertInfolistActionShouldOpenUrlInNewTab' => function (string $component, string|array $actions, string $schema = 'infolist'): static {
        $actions = $this->parseNestedInfolistActions($component, $actions, $schema);
        $this->assertActionShouldOpenUrlInNewTab($actions);
        return $this;
    },
    'assertInfolistActionShouldNotOpenUrlInNewTab' => function (string $component, string|array $actions, string $schema = 'infolist'): static {
        $actions = $this->parseNestedInfolistActions($component, $actions, $schema);
        $this->assertActionShouldNotOpenUrlInNewTab($actions);
        return $this;
    },
    'assertTableActionExists' => function (string|array $actions, ?\Closure $checkActionUsing = null, $record = null): static {
        $actions = $this->parseNestedTableActions($actions, $record);
        $this->assertActionExists($actions, $checkActionUsing);
        return $this;
    },
    'assertTableActionDoesNotExist' => function (string|array $actions, ?\Closure $checkActionUsing = null, $record = null): static {
        $actions = $this->parseNestedTableActions($actions, $record);
        $this->assertActionDoesNotExist($actions, $checkActionUsing);
        return $this;
    },
    'assertTableActionVisible' => function (string|array $actions, $record = null): static {
        $actions = $this->parseNestedTableActions($actions, $record);
        $this->assertActionVisible($actions);
        return $this;
    },
    'assertTableActionHidden' => function (string|array $actions, $record = null): static {
        $actions = $this->parseNestedTableActions($actions, $record);
        $this->assertActionHidden($actions);
        return $this;
    },
    'assertTableActionEnabled' => function (string|array $actions, $record = null): static {
        $actions = $this->parseNestedTableActions($actions, $record);
        $this->assertActionEnabled($actions);
        return $this;
    },
    'assertTableActionDisabled' => function (string|array $actions, $record = null): static {
        $actions = $this->parseNestedTableActions($actions, $record);
        $this->assertActionDisabled($actions);
        return $this;
    },
    'assertTableActionMounted' => function (string|array $actions): static {
        $actions = $this->parseNestedTableActions($actions);
        $this->assertActionMounted($actions);
        return $this;
    },
    'assertTableActionNotMounted' => function (string|array $actions): static {
        $actions = $this->parseNestedTableActions($actions);
        $this->assertActionNotMounted($actions);
        return $this;
    },
    'assertTableActionHalted' => function (string|array $actions): static {
        $actions = $this->parseNestedTableActions($actions);
        $this->assertActionMounted($actions);
        return $this;
    },
    'assertTableBulkActionExists' => function (string|array $actions): static {
        $actions = $this->parseNestedTableBulkActions($actions);
        $this->assertActionExists($actions);
        return $this;
    },
    'assertTableBulkActionDoesNotExist' => function (string|array $actions): static {
        $actions = $this->parseNestedTableBulkActions($actions);
        $this->assertActionDoesNotExist($actions);
        return $this;
    },
    'assertTableBulkActionsExistInOrder' => function (array $names): static {
        $this->assertActionListInOrder($names, $this->instance()->getTable()->getToolbarActions(), 'table bulk', \Filament\Actions\BulkAction::class);
        return $this;
    },
    'assertTableBulkActionVisible' => function (string|array $actions): static {
        $actions = $this->parseNestedTableBulkActions($actions);
        $this->assertActionVisible($actions);
        return $this;
    },
    'assertTableBulkActionHidden' => function (string|array $actions): static {
        $actions = $this->parseNestedTableBulkActions($actions);
        $this->assertActionHidden($actions);
        return $this;
    },
    'assertTableBulkActionEnabled' => function (string|array $actions): static {
        $actions = $this->parseNestedTableBulkActions($actions);
        $this->assertActionEnabled($actions);
        return $this;
    },
    'assertTableBulkActionDisabled' => function (string|array $actions): static {
        $actions = $this->parseNestedTableBulkActions($actions);
        $this->assertActionDisabled($actions);
        return $this;
    },
    'assertTableActionHasIcon' => function (string|array $actions, string|\BackedEnum $icon, $record = null): static {
        $actions = $this->parseNestedTableActions($actions, $record);
        $this->assertActionHasIcon($actions, $icon);
        return $this;
    },
    'assertTableActionDoesNotHaveIcon' => function (string|array $actions, string|\BackedEnum $icon, $record = null): static {
        $actions = $this->parseNestedTableActions($actions, $record);
        $this->assertActionDoesNotHaveIcon($actions, $icon);
        return $this;
    },
    'assertTableActionHasLabel' => function (string|array $actions, string $label, $record = null): static {
        $actions = $this->parseNestedTableActions($actions, $record);
        $this->assertActionHasLabel($actions, $label);
        return $this;
    },
    'assertTableActionDoesNotHaveLabel' => function (string|array $actions, string $label, $record = null): static {
        $actions = $this->parseNestedTableActions($actions, $record);
        $this->assertActionDoesNotHaveLabel($actions, $label);
        return $this;
    },
    'assertTableActionHasColor' => function (string|array $actions, string|array $color, $record = null): static {
        $actions = $this->parseNestedTableActions($actions, $record);
        $this->assertActionHasColor($actions, $color);
        return $this;
    },
    'assertTableActionDoesNotHaveColor' => function (string|array $actions, string|array $color, $record = null): static {
        $actions = $this->parseNestedTableActions($actions, $record);
        $this->assertActionDoesNotHaveColor($actions, $color);
        return $this;
    },
    'assertTableBulkActionHasIcon' => function (string|array $actions, string|\BackedEnum $icon): static {
        $actions = $this->parseNestedTableBulkActions($actions);
        $this->assertActionHasIcon($actions, $icon);
        return $this;
    },
    'assertTableBulkActionDoesNotHaveIcon' => function (string|array $actions, string|\BackedEnum $icon): static {
        $actions = $this->parseNestedTableBulkActions($actions);
        $this->assertActionDoesNotHaveIcon($actions, $icon);
        return $this;
    },
    'assertTableBulkActionHasLabel' => function (string|array $actions, string $label): static {
        $actions = $this->parseNestedTableBulkActions($actions);
        $this->assertActionHasLabel($actions, $label);
        return $this;
    },
    'assertTableBulkActionDoesNotHaveLabel' => function (string|array $actions, string $label): static {
        $actions = $this->parseNestedTableBulkActions($actions);
        $this->assertActionDoesNotHaveLabel($actions, $label);
        return $this;
    },
    'assertTableBulkActionHasColor' => function (string|array $actions, string|array $color): static {
        $actions = $this->parseNestedTableBulkActions($actions);
        $this->assertActionHasColor($actions, $color);
        return $this;
    },
    'assertTableBulkActionDoesNotHaveColor' => function (string|array $actions, string|array $color): static {
        $actions = $this->parseNestedTableBulkActions($actions);
        $this->assertActionDoesNotHaveColor($actions, $color);
        return $this;
    },
    'assertTableActionHasUrl' => function (string|array $actions, string $url, $record = null): static {
        $actions = $this->parseNestedTableActions($actions, $record);
        $this->assertActionHasUrl($actions, $url);
        return $this;
    },
    'assertTableActionDoesNotHaveUrl' => function (string|array $actions, string $url, $record = null): static {
        $actions = $this->parseNestedTableActions($actions, $record);
        $this->assertActionDoesNotHaveUrl($actions, $url);
        return $this;
    },
    'assertTableActionShouldOpenUrlInNewTab' => function (string|array $actions, $record = null): static {
        $actions = $this->parseNestedTableActions($actions, $record);
        $this->assertActionShouldOpenUrlInNewTab($actions);
        return $this;
    },
    'assertTableActionShouldNotOpenUrlInNewTab' => function (string|array $actions, $record = null): static {
        $actions = $this->parseNestedTableActions($actions, $record);
        $this->assertActionShouldNotOpenUrlInNewTab($actions);
        return $this;
    },
    'assertTableBulkActionMounted' => function (string|array $actions): static {
        $actions = $this->parseNestedTableBulkActions($actions);
        $this->assertActionMounted($actions);
        return $this;
    },
    'assertTableBulkActionNotMounted' => function (string|array $actions): static {
        $actions = $this->parseNestedTableBulkActions($actions);
        $this->assertActionNotMounted($actions);
        return $this;
    },
    'assertTableBulkActionHalted' => function (string|array $actions): static {
        $actions = $this->parseNestedTableBulkActions($actions);
        $this->assertActionMounted($actions);
        return $this;
    },
];
