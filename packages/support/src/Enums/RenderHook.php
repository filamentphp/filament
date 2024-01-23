<?php

namespace Filament\Support\Enums;

enum RenderHook: string
{
    case Panels_AuthLoginFormAfter = 'panels::auth.login.form.after';

    case Panels_AuthLoginFormBefore = 'panels::auth.login.form.before';

    case Panels_AuthPasswordResetRequestFormAfter = 'panels::auth.password-reset.request.form.after';

    case Panels_AuthPasswordResetRequestFormBefore = 'panels::auth.password-reset.request.form.before';

    case Panels_AuthPasswordResetResetFormAfter = 'panels::auth.password-reset.reset.form.after';

    case Panels_AuthPasswordResetResetFormBefore = 'panels::auth.password-reset.reset.form.before';

    case Panels_AuthRegisterFormAfter = 'panels::auth.register.form.after';

    case Panels_AuthRegisterFormBefore = 'panels::auth.register.form.before';

    case Panels_BodyEnd = 'panels::body.end';

    case Panels_BodyStart = 'panels::body.start';

    case Panels_ContentEnd = 'panels::content.end';

    case Panels_ContentStart = 'panels::content.start';

    case Panels_Footer = 'panels::footer';

    case Panels_GlobalSearchAfter = 'panels::global-search.after';

    case Panels_GlobalSearchBefore = 'panels::global-search.before';

    case Panels_GlobalSearchEnd = 'panels::global-search.end';

    case Panels_GlobalSearchStart = 'panels::global-search.start';

    case Panels_HeadEnd = 'panels::head.end';

    case Panels_HeadStart = 'panels::head.start';

    case Panels_PageEnd = 'panels::page.end';

    case Panels_PageFooterWidgetsAfter = 'panels::page.footer-widgets.after';

    case Panels_PageFooterWidgetsBefore = 'panels::page.footer-widgets.before';

    case Panels_PageHeaderActionsAfter = 'panels::page.header.actions.after';

    case Panels_PageHeaderActionsBefore = 'panels::page.header.actions.before';

    case Panels_PageHeaderWidgetsAfter = 'panels::page.header-widgets.after';

    case Panels_PageHeaderWidgetsBefore = 'panels::page.header-widgets.before';

    case Panels_PageStart = 'panels::page.start';

    case Panels_ResourcePagesListRecordsTableAfter = 'panels::resource.pages.list-records.table.after';

    case Panels_ResourcePagesListRecordsTableBefore = 'panels::resource.pages.list-records.table.before';

    case Panels_ResourcePagesListRecordsTabsEnd = 'panels::resource.pages.list-records.tabs.end';

    case Panels_ResourcePagesListRecordsTabsStart = 'panels::resource.pages.list-records.tabs.start';

    case Panels_ResourceRelationManagerAfter = 'panels::resource.relation-manager.after';

    case Panels_ResourceRelationManagerBefore = 'panels::resource.relation-manager.before';

    case Panels_ScriptsAfter = 'panels::scripts.after';

    case Panels_ScriptsBefore = 'panels::scripts.before';

    case Panels_SidebarNavEnd = 'panels::sidebar.nav.end';

    case Panels_SidebarNavStart = 'panels::sidebar.nav.start';

    case Panels_SidebarFooter = 'panels::sidebar.footer';

    case Panels_StylesAfter = 'panels::styles.after';

    case Panels_StylesBefore = 'panels::styles.before';

    case Panels_TenantMenuAfter = 'panels::tenant-menu.after';

    case Panels_TenantMenuBefore = 'panels::tenant-menu.before';

    case Panels_TopbarAfter = 'panels::topbar.after';

    case Panels_TopbarBefore = 'panels::topbar.before';

    case Panels_TopbarEnd = 'panels::topbar.end';

    case Panels_TopbarStart = 'panels::topbar.start';

    case Panels_UserMenuAfter = 'panels::user-menu.after';

    case Panels_UserMenuBefore = 'panels::user-menu.before';

    case Panels_UserMenuProfileAfter = 'panels::user-menu.profile.after';

    case Panels_UserMenuProfileBefore = 'panels::user-menu.profile.before';

    case Tables_ToolbarEnd = 'tables::toolbar.end';

    case Tables_ToolbarGroupingSelectorAfter = 'tables::toolbar.grouping-selector.after';

    case Tables_ToolbarGroupingSelectorBefore = 'tables::toolbar.grouping-selector.before';

    case Tables_ToolbarReorderTriggerAfter = 'tables::toolbar.reorder-trigger.after';

    case Tables_ToolbarReorderTriggerBefore = 'tables::toolbar.reorder-trigger.before';

    case Tables_ToolbarSearchAfter = 'tables::toolbar.search.after';

    case Tables_ToolbarSearchBefore = 'tables::toolbar.search.before';

    case Tables_ToolbarStart = 'tables::toolbar.start';

    case Tables_ToolbarToggleColumnTriggerAfter = 'tables::toolbar.toggle-column-trigger.after';

    case Tables_ToolbarToggleColumnTriggerBefore = 'tables::toolbar.toggle-column-trigger.before';

    case Widgets_TableWidgetEnd = 'widgets::table-widget.end';

    case Widgets_TableWidgetStart = 'widgets::table-widget.start';
}
