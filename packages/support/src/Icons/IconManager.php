<?php

namespace Filament\Support\Icons;

use BackedEnum;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Arr;

class IconManager
{
    /**
     * Exact alternative spellings share the existing registry keys.
     *
     * @var array<string, string>
     */
    private const ALIASES = [
        'forms::components.rich-editor.toolbar.align-center' => 'forms:components.rich-editor.toolbar.align-center',
        'forms::components.rich-editor.toolbar.align-end' => 'forms:components.rich-editor.toolbar.align-end',
        'forms::components.rich-editor.toolbar.align-justify' => 'forms:components.rich-editor.toolbar.align-justify',
        'forms::components.rich-editor.toolbar.align-start' => 'forms:components.rich-editor.toolbar.align-start',
        'forms::components.rich-editor.toolbar.attach-files' => 'forms:components.rich-editor.toolbar.attach-files',
        'forms::components.rich-editor.toolbar.blockquote' => 'forms:components.rich-editor.toolbar.blockquote',
        'forms::components.rich-editor.toolbar.bold' => 'forms:components.rich-editor.toolbar.bold',
        'forms::components.rich-editor.toolbar.bullet-list' => 'forms:components.rich-editor.toolbar.bullet-list',
        'forms::components.rich-editor.toolbar.clear-formatting' => 'forms:components.rich-editor.toolbar.clear_formatting',
        'forms::components.rich-editor.toolbar.code' => 'forms:components.rich-editor.toolbar.code',
        'forms::components.rich-editor.toolbar.code-block' => 'forms:components.rich-editor.toolbar.code-block',
        'forms::components.rich-editor.toolbar.custom-blocks' => 'forms:components.rich-editor.toolbar.custom-blocks',
        'forms::components.rich-editor.toolbar.details' => 'forms:components.rich-editor.toolbar.details',
        'forms::components.rich-editor.toolbar.grid' => 'forms:components.rich-editor.toolbar.grid',
        'forms::components.rich-editor.toolbar.grid-add-column-after' => 'forms:components.rich-editor.toolbar.grid_add_column_after',
        'forms::components.rich-editor.toolbar.grid-add-column-before' => 'forms:components.rich-editor.toolbar.grid_add_column_before',
        'forms::components.rich-editor.toolbar.grid-delete' => 'forms:components.rich-editor.toolbar.grid_delete',
        'forms::components.rich-editor.toolbar.grid-delete-column' => 'forms:components.rich-editor.toolbar.grid_delete_column',
        'forms::components.rich-editor.toolbar.h1' => 'forms:components.rich-editor.toolbar.h1',
        'forms::components.rich-editor.toolbar.h2' => 'forms:components.rich-editor.toolbar.h2',
        'forms::components.rich-editor.toolbar.h3' => 'forms:components.rich-editor.toolbar.h3',
        'forms::components.rich-editor.toolbar.h4' => 'forms:components.rich-editor.toolbar.h4',
        'forms::components.rich-editor.toolbar.h5' => 'forms:components.rich-editor.toolbar.h5',
        'forms::components.rich-editor.toolbar.h6' => 'forms:components.rich-editor.toolbar.h6',
        'forms::components.rich-editor.toolbar.highlight' => 'forms:components.rich-editor.toolbar.highlight',
        'forms::components.rich-editor.toolbar.horizontal-rule' => 'forms:components.rich-editor.toolbar.horizontal-rule',
        'forms::components.rich-editor.toolbar.italic' => 'forms:components.rich-editor.toolbar.italic',
        'forms::components.rich-editor.toolbar.lead' => 'forms:components.rich-editor.toolbar.lead',
        'forms::components.rich-editor.toolbar.link' => 'forms:components.rich-editor.toolbar.link',
        'forms::components.rich-editor.toolbar.merge-tags' => 'forms:components.rich-editor.toolbar.merge-tags',
        'forms::components.rich-editor.toolbar.ordered-list' => 'forms:components.rich-editor.toolbar.ordered-list',
        'forms::components.rich-editor.toolbar.paragraph' => 'forms:components.rich-editor.toolbar.paragraph',
        'forms::components.rich-editor.toolbar.redo' => 'forms:components.rich-editor.toolbar.redo',
        'forms::components.rich-editor.toolbar.small' => 'forms:components.rich-editor.toolbar.small',
        'forms::components.rich-editor.toolbar.strike' => 'forms:components.rich-editor.toolbar.strike',
        'forms::components.rich-editor.toolbar.subscript' => 'forms:components.rich-editor.toolbar.subscript',
        'forms::components.rich-editor.toolbar.superscript' => 'forms:components.rich-editor.toolbar.superscript',
        'forms::components.rich-editor.toolbar.table' => 'forms:components.rich-editor.toolbar.table',
        'forms::components.rich-editor.toolbar.table-add-column-after' => 'forms:components.rich-editor.toolbar.table_add_column_after',
        'forms::components.rich-editor.toolbar.table-add-column-before' => 'forms:components.rich-editor.toolbar.table_add_column_before',
        'forms::components.rich-editor.toolbar.table-add-row-after' => 'forms:components.rich-editor.toolbar.table_add_row_after',
        'forms::components.rich-editor.toolbar.table-add-row-before' => 'forms:components.rich-editor.toolbar.table_add_row_before',
        'forms::components.rich-editor.toolbar.table-delete' => 'forms:components.rich-editor.toolbar.table_delete',
        'forms::components.rich-editor.toolbar.table-delete-column' => 'forms:components.rich-editor.toolbar.table_delete_column',
        'forms::components.rich-editor.toolbar.table-delete-row' => 'forms:components.rich-editor.toolbar.table_delete_row',
        'forms::components.rich-editor.toolbar.table-merge-cells' => 'forms:components.rich-editor.toolbar.table_merge_cells',
        'forms::components.rich-editor.toolbar.table-split-cell' => 'forms:components.rich-editor.toolbar.table_split_cell',
        'forms::components.rich-editor.toolbar.table-toggle-header-cell' => 'forms:components.rich-editor.toolbar.table_toggle_header_cell',
        'forms::components.rich-editor.toolbar.table-toggle-header-row' => 'forms:components.rich-editor.toolbar.table_toggle_header_row',
        'forms::components.rich-editor.toolbar.text-color' => 'forms:components.rich-editor.toolbar.text-color',
        'forms::components.rich-editor.toolbar.underline' => 'forms:components.rich-editor.toolbar.underline',
        'forms::components.rich-editor.toolbar.undo' => 'forms:components.rich-editor.toolbar.undo',
        'schemas::components.callout.danger' => 'schema::components.callout.danger',
        'schemas::components.callout.info' => 'schema::components.callout.info',
        'schemas::components.callout.success' => 'schema::components.callout.success',
        'schemas::components.callout.warning' => 'schema::components.callout.warning',
        'schemas::components.tabs.dropdown-trigger-button' => 'schema::components.tabs.dropdown-trigger-button',
        'schemas::components.tabs.more-tabs-button' => 'schema::components.tabs.more-tabs-button',
        'schemas::components.wizard.completed-step' => 'schema::components.wizard.completed-step',
        'tables::filters.query-builder.constraints.boolean' => 'query-builder::constraints.boolean',
        'tables::filters.query-builder.constraints.date' => 'query-builder::constraints.date',
        'tables::filters.query-builder.constraints.number' => 'query-builder::constraints.number',
        'tables::filters.query-builder.constraints.relationship' => 'query-builder::constraints.relationship',
        'tables::filters.query-builder.constraints.select' => 'query-builder::constraints.select',
        'tables::filters.query-builder.constraints.text' => 'query-builder::constraints.text',
    ];

    /**
     * @var array<string, string | BackedEnum | Htmlable>
     */
    protected array $icons = [];

    /**
     * @internal
     */
    public static function normalizeAlias(string $alias): string
    {
        return self::ALIASES[$alias] ?? $alias;
    }

    /**
     * @param  array<string, string | BackedEnum | Htmlable>  $icons
     */
    public function register(array $icons): void
    {
        $registeredIcons = [];

        foreach ($icons as $alias => $icon) {
            $registeredIcons[static::normalizeAlias($alias)] = $icon;
        }

        $this->icons = [
            ...$this->icons,
            ...$registeredIcons,
        ];
    }

    /**
     * @param  string|array<string>  $alias
     */
    public function resolve(string | array $alias): string | BackedEnum | Htmlable | null
    {
        foreach (Arr::wrap($alias) as $alias) {
            $alias = static::normalizeAlias($alias);

            if (isset($this->icons[$alias])) {
                return $this->icons[$alias];
            }
        }

        return null;
    }
}
