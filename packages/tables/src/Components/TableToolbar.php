<?php

namespace Filament\Tables\Components;

use Filament\Schemas\Components\Component;

class TableToolbar extends TableGroup
{
    protected bool $hasDefaultItems = false;

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
     * layout component, such as a `Flex` or a `TableGroup`, renders as a direct child of the toolbar
     * instead, so the toolbar lays it out as a group of its own, like the search field in the default toolbar.
     */
    public function renderItems(): string
    {
        return Component::withVisibilityCache(function (): string {
            /** @var array<int, Component | array<int, TablePart>> $groups */
            $groups = [];

            foreach ($this->getChildSchema()->getComponents(withHidden: true) as $component) {
                if (! $component->isVisible()) {
                    continue;
                }

                if (! ($component instanceof TablePart)) {
                    $groups[] = $component;

                    continue;
                }

                $lastGroupKey = array_key_last($groups);

                if (($lastGroupKey !== null) && is_array($groups[$lastGroupKey])) {
                    $groups[$lastGroupKey][] = $component;

                    continue;
                }

                $groups[] = [$component];
            }

            $html = '';

            foreach ($groups as $group) {
                $html .= is_array($group)
                    ? '<div class="fi-ta-actions fi-align-start fi-wrapped">' . implode('', array_map(fn (TablePart $part): string => $part->toHtml(), $group)) . '</div>'
                    : $group->toHtml();
            }

            return $html;
        });
    }

    public function toEmbeddedHtml(): string
    {
        return view('filament-tables::components.parts.toolbar', [
            'table' => $this->getTable(),
            'part' => $this,
        ])->render();
    }
}
