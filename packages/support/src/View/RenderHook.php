<?php

namespace Filament\Support\View;

class RenderHook
{
    const PANELS_AUTH_LOGIN_FORM_AFTER = 'panels::auth.login.form.after';

    const PANELS_AUTH_LOGIN_FORM_BEFORE = 'panels::auth.login.form.before';

    const PANELS_AUTH_PASSWORD_RESET_REQUEST_FORM_AFTER = 'panels::auth.password-reset.request.form.after';

    const PANELS_AUTH_PASSWORD_RESET_REQUEST_FORM_BEFORE = 'panels::auth.password-reset.request.form.before';

    const PANELS_AUTH_PASSWORD_RESET_RESET_FORM_AFTER = 'panels::auth.password-reset.reset.form.after';

    const PANELS_AUTH_PASSWORD_RESET_RESET_FORM_BEFORE = 'panels::auth.password-reset.reset.form.before';

    const PANELS_AUTH_REGISTER_FORM_AFTER = 'panels::auth.register.form.after';

    const PANELS_AUTH_REGISTER_FORM_BEFORE = 'panels::auth.register.form.before';

    const PANELS_BODY_END = 'panels::body.end';

    const PANELS_BODY_START = 'panels::body.start';

    const PANELS_CONTENT_END = 'panels::content.end';

    const PANELS_CONTENT_START = 'panels::content.start';

    const PANELS_FOOTER = 'panels::footer';

    const PANELS_GLOBAL_SEARCH_AFTER = 'panels::global-search.after';

    const PANELS_GLOBAL_SEARCH_BEFORE = 'panels::global-search.before';

    const PANELS_GLOBAL_SEARCH_END = 'panels::global-search.end';

    const PANELS_GLOBAL_SEARCH_START = 'panels::global-search.start';

    const PANELS_HEAD_END = 'panels::head.end';

    const PANELS_HEAD_START = 'panels::head.start';

    const PANELS_PAGE_END = 'panels::page.end';

    const PANELS_PAGE_FOOTER_WIDGETS_AFTER = 'panels::page.footer-widgets.after';

    const PANELS_PAGE_FOOTER_WIDGETS_BEFORE = 'panels::page.footer-widgets.before';

    const PANELS_PAGE_HEADER_ACTIONS_AFTER = 'panels::page.header.actions.after';

    const PANELS_PAGE_HEADER_ACTIONS_BEFORE = 'panels::page.header.actions.before';

    const PANELS_PAGE_HEADER_WIDGETS_AFTER = 'panels::page.header-widgets.after';

    const PANELS_PAGE_HEADER_WIDGETS_BEFORE = 'panels::page.header-widgets.before';

    const PANELS_PAGE_START = 'panels::page.start';

    const PANELS_RESOURCE_PAGES_LIST_RECORDS_TABLE_AFTER = 'panels::resource.pages.list-records.table.after';

    const PANELS_RESOURCE_PAGES_LIST_RECORDS_TABLE_BEFORE = 'panels::resource.pages.list-records.table.before';

    const PANELS_RESOURCE_PAGES_LIST_RECORDS_TABS_END = 'panels::resource.pages.list-records.tabs.end';

    const PANELS_RESOURCE_PAGES_LIST_RECORDS_TABS_START = 'panels::resource.pages.list-records.tabs.start';

    const PANELS_RESOURCE_PAGES_MANAGE_RELATED_RECORDS_TABLE_AFTER = 'panels::resource.pages.manage-related-records.table.after';

    const PANELS_RESOURCE_PAGES_MANAGE_RELATED_RECORDS_TABLE_BEFORE = 'panels::resource.pages.manage-related-records.table.before';

    const PANELS_RESOURCE_RELATION_MANAGER_AFTER = 'panels::resource.relation-manager.after';

    const PANELS_RESOURCE_RELATION_MANAGER_BEFORE = 'panels::resource.relation-manager.before';

    const PANELS_RESOURCE_TABS_END = 'panels::resource.tabs.end';

    const PANELS_RESOURCE_TABS_START = 'panels::resource.tabs.start';

    const PANELS_SCRIPTS_AFTER = 'panels::scripts.after';

    const PANELS_SCRIPTS_BEFORE = 'panels::scripts.before';

    const PANELS_SIDEBAR_NAV_END = 'panels::sidebar.nav.end';

    const PANELS_SIDEBAR_NAV_START = 'panels::sidebar.nav.start';

    const PANELS_SIDEBAR_FOOTER = 'panels::sidebar.footer';

    const PANELS_STYLES_AFTER = 'panels::styles.after';

    const PANELS_STYLES_BEFORE = 'panels::styles.before';

    const PANELS_TENANT_MENU_AFTER = 'panels::tenant-menu.after';

    const PANELS_TENANT_MENU_BEFORE = 'panels::tenant-menu.before';

    const PANELS_TOPBAR_AFTER = 'panels::topbar.after';

    const PANELS_TOPBAR_BEFORE = 'panels::topbar.before';

    const PANELS_TOPBAR_END = 'panels::topbar.end';

    const PANELS_TOPBAR_START = 'panels::topbar.start';

    const PANELS_USER_MENU_AFTER = 'panels::user-menu.after';

    const PANELS_USER_MENU_BEFORE = 'panels::user-menu.before';

    const PANELS_USER_MENU_PROFILE_AFTER = 'panels::user-menu.profile.after';

    const PANELS_USER_MENU_PROFILE_BEFORE = 'panels::user-menu.profile.before';

    const TABLES_TOOLBAR_END = 'tables::toolbar.end';

    const TABLES_TOOLBAR_GROUPING_SELECTOR_AFTER = 'tables::toolbar.grouping-selector.after';

    const TABLES_TOOLBAR_GROUPING_SELECTOR_BEFORE = 'tables::toolbar.grouping-selector.before';

    const TABLES_TOOLBAR_REORDER_TRIGGER_AFTER = 'tables::toolbar.reorder-trigger.after';

    const TABLES_TOOLBAR_REORDER_TRIGGER_BEFORE = 'tables::toolbar.reorder-trigger.before';

    const TABLES_TOOLBAR_SEARCH_AFTER = 'tables::toolbar.search.after';

    const TABLES_TOOLBAR_SEARCH_BEFORE = 'tables::toolbar.search.before';

    const TABLES_TOOLBAR_START = 'tables::toolbar.start';

    const TABLES_TOOLBAR_TOGGLE_COLUMN_TRIGGER_AFTER = 'tables::toolbar.toggle-column-trigger.after';

    const TABLES_TOOLBAR_TOGGLE_COLUMN_TRIGGER_BEFORE = 'tables::toolbar.toggle-column-trigger.before';

    const WIDGETS_TABLE_WIDGET_END = 'widgets::table-widget.end';

    const WIDGETS_TABLE_WIDGET_START = 'widgets::table-widget.start';
}
