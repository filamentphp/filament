<?php

namespace Filament\Tables\Components;

use Filament\Schemas\Components\Component;

class TableToolbar extends TableStack
{
    protected bool $hasDefaultItems = false;

    protected bool $hasNonBulkItems = false;

    protected bool $hasToolbarActionsItem = false;

    protected function setUp(): void
    {
        parent::setUp();

        if (($this->childComponents['default'] ?? []) !== []) {
            return;
        }

        $this->hasDefaultItems = true;

        $this->schema([
            TableReorderTrigger::make(),
            TableToolbarActions::make(),
            TableGroupingSettings::make(),
            TableSearch::make(),
            TableFiltersTrigger::make(),
            TableColumnManager::make(),
        ]);
    }

    public function hasDefaultItems(): bool
    {
        return $this->hasDefaultItems;
    }

    /**
     * Parts placed directly in the toolbar share one actions group at the start of the row. A nested
     * layout component, such as a `Flex` or a `TableStack`, renders as a direct child of the toolbar
     * instead, so the toolbar lays it out as a group of its own, like the search field in the default toolbar.
     */
    public function renderItems(): string
    {
        $this->hasNonBulkItems = false;
        $this->hasToolbarActionsItem = false;

        return Component::withVisibilityCache(function (): string {
            /** @var array<int, string | array<int, string>> $groups */
            $groups = [];

            foreach ($this->getChildSchema()->getComponents(withHidden: true) as $component) {
                if (! $component->isVisible()) {
                    continue;
                }

                $html = $component->toHtml();

                if ($component instanceof TableToolbarActions) {
                    $this->hasToolbarActionsItem = true;
                    $this->hasNonBulkItems = $this->hasNonBulkItems || $this->getTable()->hasNonBulkToolbarAction();
                } elseif (! $this->getTable()->isLayoutHtmlBlank($html)) {
                    $this->hasNonBulkItems = true;
                }

                if (! ($component instanceof TablePart)) {
                    $groups[] = $html;

                    continue;
                }

                $lastGroupKey = array_key_last($groups);

                if (($lastGroupKey !== null) && is_array($groups[$lastGroupKey])) {
                    $groups[$lastGroupKey][] = $html;

                    continue;
                }

                $groups[] = [$html];
            }

            $html = '';

            foreach ($groups as $group) {
                $html .= is_array($group)
                    ? '<div class="fi-ta-actions fi-align-start fi-wrapped">' . implode('', $group) . '</div>'
                    : $group;
            }

            return $html;
        });
    }

    /**
     * Whether the rendered custom items include anything beyond the bulk actions, which only show while records are selected.
     */
    public function hasNonBulkItems(): bool
    {
        return $this->hasNonBulkItems;
    }

    public function hasToolbarActionsItem(): bool
    {
        return $this->hasToolbarActionsItem;
    }

    public function toEmbeddedHtml(): string
    {
        return view('filament-tables::components.parts.toolbar', [
            'table' => $this->getTable(),
            'part' => $this,
        ])->render();
    }
}
