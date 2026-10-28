<?php

namespace Filament\Tests\Upgrade\Fixtures\FrontendKeys;

function iconAliases(): array
{
    return [
        'forms:components.rich-editor.toolbar.align-center',
        'forms:components.rich-editor.toolbar.align-end',
        'forms:components.rich-editor.toolbar.align-justify',
        'forms:components.rich-editor.toolbar.align-start',
        'forms:components.rich-editor.toolbar.attach-files',
        'forms:components.rich-editor.toolbar.blockquote',
        'forms:components.rich-editor.toolbar.bold',
        'forms:components.rich-editor.toolbar.bullet-list',
        'forms:components.rich-editor.toolbar.clear_formatting',
        'forms:components.rich-editor.toolbar.code',
        'forms:components.rich-editor.toolbar.code-block',
        'forms:components.rich-editor.toolbar.custom-blocks',
        'forms:components.rich-editor.toolbar.details',
        'forms:components.rich-editor.toolbar.grid',
        'forms:components.rich-editor.toolbar.grid_add_column_after',
        'forms:components.rich-editor.toolbar.grid_add_column_before',
        'forms:components.rich-editor.toolbar.grid_delete',
        'forms:components.rich-editor.toolbar.grid_delete_column',
        'forms:components.rich-editor.toolbar.h1',
        'forms:components.rich-editor.toolbar.h2',
        'forms:components.rich-editor.toolbar.h3',
        'forms:components.rich-editor.toolbar.h4',
        'forms:components.rich-editor.toolbar.h5',
        'forms:components.rich-editor.toolbar.h6',
        'forms:components.rich-editor.toolbar.highlight',
        'forms:components.rich-editor.toolbar.horizontal-rule',
        'forms:components.rich-editor.toolbar.italic',
        'forms:components.rich-editor.toolbar.lead',
        'forms:components.rich-editor.toolbar.link',
        'forms:components.rich-editor.toolbar.merge-tags',
        'forms:components.rich-editor.toolbar.ordered-list',
        'forms:components.rich-editor.toolbar.paragraph',
        'forms:components.rich-editor.toolbar.redo',
        'forms:components.rich-editor.toolbar.small',
        'forms:components.rich-editor.toolbar.strike',
        'forms:components.rich-editor.toolbar.subscript',
        'forms:components.rich-editor.toolbar.superscript',
        'forms:components.rich-editor.toolbar.table',
        'forms:components.rich-editor.toolbar.table_add_column_after',
        'forms:components.rich-editor.toolbar.table_add_column_before',
        'forms:components.rich-editor.toolbar.table_add_row_after',
        'forms:components.rich-editor.toolbar.table_add_row_before',
        'forms:components.rich-editor.toolbar.table_delete',
        'forms:components.rich-editor.toolbar.table_delete_column',
        'forms:components.rich-editor.toolbar.table_delete_row',
        'forms:components.rich-editor.toolbar.table_merge_cells',
        'forms:components.rich-editor.toolbar.table_split_cell',
        'forms:components.rich-editor.toolbar.table_toggle_header_cell',
        'forms:components.rich-editor.toolbar.table_toggle_header_row',
        'forms:components.rich-editor.toolbar.text-color',
        'forms:components.rich-editor.toolbar.underline',
        'forms:components.rich-editor.toolbar.undo',
        'schema::components.callout.danger',
        'schema::components.callout.info',
        'schema::components.callout.success',
        'schema::components.callout.warning',
        'schema::components.tabs.dropdown-trigger-button',
        'schema::components.tabs.more-tabs-button',
        'schema::components.wizard.completed-step',
    ];
}

function renderHooks(): array
{
    return [
        'panels::page.sub-navigation.module-menu.before',
        'panels::page.sub-navigation.module-menu.after',
    ];
}

function customAliases(string $tool): array
{
    return [
        'forms:components.rich-editor.toolbar.custom_action',
        'forms::components.rich-editor.toolbar.clear_formatting',
        'forms::components.rich-editor.toolbar.bold',
        'schema::components.custom.icon',
        'panels::page.sub-navigation.module-menu.custom',
        'custom::forms:components.rich-editor.toolbar.bold',
        'forms:components.rich-editor.toolbar.' . $tool,
        "forms:components.rich-editor.toolbar.{$tool}",
    ];
}
-----
<?php

namespace Filament\Tests\Upgrade\Fixtures\FrontendKeys;

function iconAliases(): array
{
    return [
        'forms::components.rich-editor.toolbar.align-center',
        'forms::components.rich-editor.toolbar.align-end',
        'forms::components.rich-editor.toolbar.align-justify',
        'forms::components.rich-editor.toolbar.align-start',
        'forms::components.rich-editor.toolbar.attach-files',
        'forms::components.rich-editor.toolbar.blockquote',
        'forms::components.rich-editor.toolbar.bold',
        'forms::components.rich-editor.toolbar.bullet-list',
        'forms::components.rich-editor.toolbar.clear-formatting',
        'forms::components.rich-editor.toolbar.code',
        'forms::components.rich-editor.toolbar.code-block',
        'forms::components.rich-editor.toolbar.custom-blocks',
        'forms::components.rich-editor.toolbar.details',
        'forms::components.rich-editor.toolbar.grid',
        'forms::components.rich-editor.toolbar.grid-add-column-after',
        'forms::components.rich-editor.toolbar.grid-add-column-before',
        'forms::components.rich-editor.toolbar.grid-delete',
        'forms::components.rich-editor.toolbar.grid-delete-column',
        'forms::components.rich-editor.toolbar.h1',
        'forms::components.rich-editor.toolbar.h2',
        'forms::components.rich-editor.toolbar.h3',
        'forms::components.rich-editor.toolbar.h4',
        'forms::components.rich-editor.toolbar.h5',
        'forms::components.rich-editor.toolbar.h6',
        'forms::components.rich-editor.toolbar.highlight',
        'forms::components.rich-editor.toolbar.horizontal-rule',
        'forms::components.rich-editor.toolbar.italic',
        'forms::components.rich-editor.toolbar.lead',
        'forms::components.rich-editor.toolbar.link',
        'forms::components.rich-editor.toolbar.merge-tags',
        'forms::components.rich-editor.toolbar.ordered-list',
        'forms::components.rich-editor.toolbar.paragraph',
        'forms::components.rich-editor.toolbar.redo',
        'forms::components.rich-editor.toolbar.small',
        'forms::components.rich-editor.toolbar.strike',
        'forms::components.rich-editor.toolbar.subscript',
        'forms::components.rich-editor.toolbar.superscript',
        'forms::components.rich-editor.toolbar.table',
        'forms::components.rich-editor.toolbar.table-add-column-after',
        'forms::components.rich-editor.toolbar.table-add-column-before',
        'forms::components.rich-editor.toolbar.table-add-row-after',
        'forms::components.rich-editor.toolbar.table-add-row-before',
        'forms::components.rich-editor.toolbar.table-delete',
        'forms::components.rich-editor.toolbar.table-delete-column',
        'forms::components.rich-editor.toolbar.table-delete-row',
        'forms::components.rich-editor.toolbar.table-merge-cells',
        'forms::components.rich-editor.toolbar.table-split-cell',
        'forms::components.rich-editor.toolbar.table-toggle-header-cell',
        'forms::components.rich-editor.toolbar.table-toggle-header-row',
        'forms::components.rich-editor.toolbar.text-color',
        'forms::components.rich-editor.toolbar.underline',
        'forms::components.rich-editor.toolbar.undo',
        'schemas::components.callout.danger',
        'schemas::components.callout.info',
        'schemas::components.callout.success',
        'schemas::components.callout.warning',
        'schemas::components.tabs.dropdown-trigger-button',
        'schemas::components.tabs.more-tabs-button',
        'schemas::components.wizard.completed-step',
    ];
}

function renderHooks(): array
{
    return [
        'panels::page.sub-navigation.mobile-menu.before',
        'panels::page.sub-navigation.mobile-menu.after',
    ];
}

function customAliases(string $tool): array
{
    return [
        'forms:components.rich-editor.toolbar.custom_action',
        'forms::components.rich-editor.toolbar.clear_formatting',
        'forms::components.rich-editor.toolbar.bold',
        'schema::components.custom.icon',
        'panels::page.sub-navigation.module-menu.custom',
        'custom::forms:components.rich-editor.toolbar.bold',
        'forms:components.rich-editor.toolbar.' . $tool,
        "forms:components.rich-editor.toolbar.{$tool}",
    ];
}
