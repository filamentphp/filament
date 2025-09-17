<?php

namespace Filament\View;

class PanelsRenderHook
{
    const AUTH_LOGIN_FORM_AFTER = 'panels::auth.login.form.after';

    const AUTH_LOGIN_FORM_BEFORE = 'panels::auth.login.form.before';

    const AUTH_PASSWORD_RESET_REQUEST_FORM_AFTER = 'panels::auth.password-reset.request.form.after';

    const AUTH_PASSWORD_RESET_REQUEST_FORM_BEFORE = 'panels::auth.password-reset.request.form.before';

    const AUTH_PASSWORD_RESET_RESET_FORM_AFTER = 'panels::auth.password-reset.reset.form.after';

    const AUTH_PASSWORD_RESET_RESET_FORM_BEFORE = 'panels::auth.password-reset.reset.form.before';

    const AUTH_REGISTER_FORM_AFTER = 'panels::auth.register.form.after';

    const AUTH_REGISTER_FORM_BEFORE = 'panels::auth.register.form.before';

    const BODY_END = 'panels::body.end';

    const BODY_START = 'panels::body.start';

    const CONTENT_AFTER = 'panels::content.after';

    const CONTENT_BEFORE = 'panels::content.before';

    const CONTENT_END = 'panels::content.end';

    const CONTENT_START = 'panels::content.start';

    const FOOTER = 'panels::footer';

    const GLOBAL_SEARCH_AFTER = 'panels::global-search.after';

    const GLOBAL_SEARCH_BEFORE = 'panels::global-search.before';

    const GLOBAL_SEARCH_END = 'panels::global-search.end';

    const GLOBAL_SEARCH_START = 'panels::global-search.start';

    const HEAD_END = 'panels::head.end';

    const HEAD_START = 'panels::head.start';

    const LAYOUT_END = 'panels::layout.end';

    const LAYOUT_START = 'panels::layout.start';

    const PAGE_END = 'panels::page.end';

    const PAGE_FOOTER_WIDGETS_AFTER = 'panels::page.footer-widgets.after';

    const PAGE_FOOTER_WIDGETS_BEFORE = 'panels::page.footer-widgets.before';

    const PAGE_FOOTER_WIDGETS_END = 'panels::page.footer-widgets.end';

    const PAGE_FOOTER_WIDGETS_START = 'panels::page.footer-widgets.start';

    const PAGE_HEADER_ACTIONS_AFTER = 'panels::page.header.actions.after';

    const PAGE_HEADER_ACTIONS_BEFORE = 'panels::page.header.actions.before';

    const PAGE_HEADER_WIDGETS_AFTER = 'panels::page.header-widgets.after';

    const PAGE_HEADER_WIDGETS_BEFORE = 'panels::page.header-widgets.before';

    const PAGE_HEADER_WIDGETS_END = 'panels::page.header-widgets.end';

    const PAGE_HEADER_WIDGETS_START = 'panels::page.header-widgets.start';

    const PAGE_START = 'panels::page.start';

    const PAGE_SUB_NAVIGATION_MOBILE_MENU_AFTER = 'panels::page.sub-navigation.module-menu.after';

    const PAGE_SUB_NAVIGATION_MOBILE_MENU_BEFORE = 'panels::page.sub-navigation.module-menu.before';

    const PAGE_SUB_NAVIGATION_SIDEBAR_AFTER = 'panels::page.sub-navigation.sidebar.after';

    const PAGE_SUB_NAVIGATION_SIDEBAR_BEFORE = 'panels::page.sub-navigation.sidebar.before';

    const PAGE_SUB_NAVIGATION_START_AFTER = 'panels::page.sub-navigation.start.after';

    const PAGE_SUB_NAVIGATION_START_BEFORE = 'panels::page.sub-navigation.start.before';

    const PAGE_SUB_NAVIGATION_TOP_AFTER = 'panels::page.sub-navigation.top.after';

    const PAGE_SUB_NAVIGATION_TOP_BEFORE = 'panels::page.sub-navigation.top.before';

    const PAGE_SUB_NAVIGATION_END_AFTER = 'panels::page.sub-navigation.end.after';

    const PAGE_SUB_NAVIGATION_END_BEFORE = 'panels::page.sub-navigation.end.before';

    const RESOURCE_PAGES_LIST_RECORDS_TABLE_AFTER = 'panels::resource.pages.list-records.table.after';

    const RESOURCE_PAGES_LIST_RECORDS_TABLE_BEFORE = 'panels::resource.pages.list-records.table.before';

    const RESOURCE_PAGES_LIST_RECORDS_TABS_END = 'panels::resource.pages.list-records.tabs.end';

    const RESOURCE_PAGES_LIST_RECORDS_TABS_START = 'panels::resource.pages.list-records.tabs.start';

    const RESOURCE_PAGES_MANAGE_RELATED_RECORDS_TABLE_AFTER = 'panels::resource.pages.manage-related-records.table.after';

    const RESOURCE_PAGES_MANAGE_RELATED_RECORDS_TABLE_BEFORE = 'panels::resource.pages.manage-related-records.table.before';

    const RESOURCE_RELATION_MANAGER_AFTER = 'panels::resource.relation-manager.after';

    const RESOURCE_RELATION_MANAGER_BEFORE = 'panels::resource.relation-manager.before';

    const RESOURCE_TABS_END = 'panels::resource.tabs.end';

    const RESOURCE_TABS_START = 'panels::resource.tabs.start';

    const SCRIPTS_AFTER = 'panels::scripts.after';

    const SCRIPTS_BEFORE = 'panels::scripts.before';

    const SIDEBAR_FOOTER = 'panels::sidebar.footer';

    const SIDEBAR_LOGO_AFTER = 'panels::sidebar.logo.after';

    const SIDEBAR_LOGO_BEFORE = 'panels::sidebar.logo.before';

    const SIDEBAR_NAV_END = 'panels::sidebar.nav.end';

    const SIDEBAR_NAV_START = 'panels::sidebar.nav.start';

    const SIMPLE_LAYOUT_END = 'panels::simple-layout.end';

    const SIMPLE_LAYOUT_START = 'panels::simple-layout.start';

    const SIMPLE_PAGE_END = 'panels::simple-page.end';

    const SIMPLE_PAGE_START = 'panels::simple-page.start';

    const STYLES_AFTER = 'panels::styles.after';

    const STYLES_BEFORE = 'panels::styles.before';

    const TENANT_MENU_AFTER = 'panels::tenant-menu.after';

    const TENANT_MENU_BEFORE = 'panels::tenant-menu.before';

    const TOPBAR_AFTER = 'panels::topbar.after';

    const TOPBAR_BEFORE = 'panels::topbar.before';

    const TOPBAR_END = 'panels::topbar.end';

    const TOPBAR_LOGO_AFTER = 'panels::topbar.logo.after';

    const TOPBAR_LOGO_BEFORE = 'panels::topbar.logo.before';

    const TOPBAR_START = 'panels::topbar.start';

    const USER_MENU_AFTER = 'panels::user-menu.after';

    const USER_MENU_BEFORE = 'panels::user-menu.before';

    const USER_MENU_PROFILE_AFTER = 'panels::user-menu.profile.after';

    const USER_MENU_PROFILE_BEFORE = 'panels::user-menu.profile.before';
}
