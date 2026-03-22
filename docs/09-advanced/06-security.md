---
title: Security
---

import Aside from "@components/Aside.astro"

## Introduction

<Aside variant="info">
    This page provides a general overview of security considerations when using Filament. Many individual features have their own specific security recommendations documented alongside them — for example, file uploads, rich editors, inline editable columns, and more. When using any Filament feature, make sure to read the full documentation for that feature, including any security warnings it contains.
</Aside>

Filament is a powerful framework that gives developers extensive control over how components are configured and rendered. This flexibility is by design — developers need to be able to do powerful things with configuration methods like `url()`, `icon()`, `html()`, and others. However, this means that Filament trusts the values you pass into these methods, and it is your responsibility to ensure that any user-supplied data is properly validated and sanitized before it reaches Filament.

This page covers key security considerations when building applications with Filament, including authorization, input validation, and HTML sanitization.

## Authorization

### Resource authorization

Filament automatically checks [Laravel Model Policies](https://laravel.com/docs/authorization#creating-policies) for standard CRUD operations on [resources](../resources/overview#authorization). When a policy exists for a resource's model, Filament will check methods like `viewAny()`, `create()`, `update()`, `view()`, `delete()`, and others before allowing access to the corresponding pages and actions.

However, Filament's automatic authorization only covers these built-in resource operations. Any custom functionality you add — custom actions, custom pages, custom Livewire components, API endpoints, or other business logic — must be authorized by you. Filament cannot know your application's authorization requirements beyond the standard CRUD operations it provides.

### Inline editable columns

Inline editable table columns such as `ToggleColumn`, `TextInputColumn`, `SelectColumn`, and `CheckboxColumn` do not check Model Policies before saving changes. They only check the column's `disabled()` state. If you need to restrict who can edit these columns, use the `disabled()` method with your own authorization logic. See the documentation for each [editable column type](../tables/columns/toggle) for more details.

### Custom actions

When you create [custom actions](../actions/overview#authorization), you are responsible for authorizing them. Filament provides `visible()`, `hidden()`, and `authorize()` methods to help with this, but you must use them — they are not applied automatically. If an action modifies data or performs sensitive operations, always ensure it is authorized.

### Testing authorization

Your application should have a comprehensive test suite that verifies authorization is enforced correctly across all entry points — not just Filament's resource pages, but also any custom actions, custom pages, Livewire components, API routes, and other functionality. Filament provides [testing helpers](../testing/overview) for asserting that actions, pages, and resources behave correctly for different user roles.

Do not rely solely on Filament's built-in policy checks. Treat them as a helpful layer, but always verify that your authorization rules are enforced end-to-end through testing.

## Validating user input

Many Filament configuration methods accept closures that can return dynamic values. Methods like `url()`, `icon()`, `html()`, and others are designed to be flexible, allowing developers to build rich, dynamic interfaces. However, when the values passed to these methods originate from user input or untrusted database content, it is your responsibility to validate and sanitize them appropriately.

For example, the `url()` method on columns, entries, and actions renders an `<a href="...">` tag with whatever value you provide. If you pass a URL sourced from user input without validation, a malicious value like `javascript:alert(document.cookie)` could be rendered as a clickable link, leading to XSS. Always validate that URLs use a safe scheme such as `http` or `https` before passing them to Filament:

```php
use Filament\Tables\Columns\TextColumn;

TextColumn::make('website')
    ->url(function (string $state): ?string {
        if (! str_starts_with($state, 'http://') && ! str_starts_with($state, 'https://')) {
            return null;
        }

        return $state;
    })
```

Similarly, the `icon()` method expects either a Blade icon name (like `heroicon-o-user`) or a valid image URL. If you pass unsanitized user input, it could be used to break out of HTML attributes. Always ensure icon values are either known icon names or validated URLs.

As a general rule: whenever you pass user-controlled data into a Filament configuration method, treat it with the same caution you would when rendering it directly in a Blade template.

## HTML sanitization

When rendering HTML content via methods like `html()` or `markdown()` on components such as `TextColumn` and `TextEntry`, Filament automatically sanitizes the output using Symfony's [HtmlSanitizer](https://symfony.com/doc/current/html_sanitizer.html) component. This removes potentially dangerous elements like `<script>` tags to help prevent XSS attacks.

### Default sanitizer configuration

Filament's default sanitizer configuration permits inline `style` attributes on all elements. This is necessary to support rich text formatting features from the rich editor, such as font colors, text highlighting, and image sizing. However, this means that CSS properties like `background: url(...)` (which can trigger external HTTP requests) or `position: fixed` (which can create phishing overlays) will not be stripped.

If your application renders HTML content from untrusted users, you should consider replacing the default sanitizer with a more restrictive configuration.

### Customizing the sanitizer

Filament binds the sanitizer as `HtmlSanitizerInterface` in Laravel's service container. You can override it by rebinding this interface in a service provider:

```php
use Symfony\Component\HtmlSanitizer\HtmlSanitizer;
use Symfony\Component\HtmlSanitizer\HtmlSanitizerConfig;
use Symfony\Component\HtmlSanitizer\HtmlSanitizerInterface;

public function register(): void
{
    $this->app->scoped(
        HtmlSanitizerInterface::class,
        fn (): HtmlSanitizer => new HtmlSanitizer(
            (new HtmlSanitizerConfig)
                ->allowSafeElements()
                ->allowRelativeLinks()
                ->allowRelativeMedias()
                ->allowAttribute('class', allowedElements: '*')
                ->allowAttribute('style', allowedElements: '*')
                ->withMaxInputLength(500000),
        ),
    );
}
```

You can restrict which CSS properties are allowed in `style` attributes, remove the `style` attribute allowance entirely, or make any other adjustments supported by Symfony's HtmlSanitizer. Refer to the [Symfony HtmlSanitizer documentation](https://symfony.com/doc/current/html_sanitizer.html) for the full list of configuration options.

### Sanitizing in Blade views

When outputting rich text content (from a rich editor or Markdown editor) in your own Blade views, you are responsible for sanitizing it. You can use Filament's `sanitizeHtml()` string helper:

```blade
{!! str($record->content)->sanitizeHtml() !!}
```

Never use `{!! $content !!}` with unsanitized user content. If you need to render Markdown as HTML, chain the helpers:

```blade
{!! str($record->content)->markdown()->sanitizeHtml() !!}
```

## Panel access

By default, all `App\Models\User` records can access Filament panels in local environments. In production, you must implement the `FilamentUser` contract on your User model and define the `canAccessPanel()` method to control who can log in. See the [users documentation](../users/overview#authorizing-access-to-the-panel) for details.

If your application has multiple panels (e.g. an admin panel and a user-facing panel), ensure that `canAccessPanel()` checks the `$panel` argument and returns the appropriate result for each one.

## Model attribute exposure

Filament exposes all non-`$hidden` model attributes to JavaScript via Livewire's model binding. This is necessary for dynamic form functionality, and only attributes with corresponding form fields are actually editable — this is not a mass assignment vulnerability. However, if your model contains sensitive attributes that should not be visible in the browser (such as API keys or internal flags), you should either add them to the model's `$hidden` property or remove them using the `mutateFormDataBeforeFill()` method on your Edit or View page. See the [resources documentation](../resources/overview#protecting-model-attributes) for more details.

## File uploads

Filament's `FileUpload` component uses Livewire's file upload mechanism. There are important security considerations when allowing users to upload files, particularly around file names, storage visibility, and accepted file types.

By default, Filament generates random file names and stores files with `private` visibility. If you use `preserveFilenames()` or `storeFileNamesIn()` with local or public disks, an attacker could upload a PHP file with a deceptive MIME type that gets executed by your server. See the [file upload documentation](../forms/file-upload#security-implications-of-controlling-file-names) for a full explanation of these risks and recommended mitigations.

You should always use `acceptedFileTypes()` to restrict the types of files users can upload, and validate file sizes with `maxSize()`. These constraints are enforced server-side, not just in the browser.

## Scoping queries

When building tables, resources, or custom Livewire components, ensure that database queries are properly scoped to the current user's permissions. Filament's resource system uses Eloquent queries that return all records by default — it is up to you to apply appropriate query scopes using the `modifyQueryUsing()` method on your table or by overriding the `getEloquentQuery()` method on your resource to ensure users can only access records they are authorized to see.

For example, in a multi-tenant application, forgetting to scope queries to the current tenant would allow users to see other tenants' data. If you are using Filament's built-in [tenancy](../users/tenancy) features, queries are scoped automatically for resources. However, any custom queries, actions, or pages you build must be scoped manually.
