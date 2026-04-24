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

The `icon()` method expects either a Blade icon name (like `heroicon-o-user`) or an image URL (any string containing `/`). Icon name strings are resolved via Blade's icon system, and URL strings are escaped before rendering into `src` attributes. However, passing an invalid icon name from user input will cause a rendering error, so you should still validate icon values against a known allowlist if they are user-controlled.

Methods like `extraAttributes()`, `extraInputAttributes()`, `extraCellAttributes()`, and other `extra*Attributes()` methods render their values into HTML without escaping. This is by design, as these methods are often used to pass Alpine.js directives and Livewire attributes that must not be escaped. However, if you pass user-controlled data as attribute names or values, an attacker could break out of the HTML attribute and inject arbitrary markup, leading to XSS. Always ensure that any dynamic values passed to these methods are validated or sourced from trusted data.

As a general rule: whenever you pass user-controlled data into a Filament configuration method, treat it with the same caution you would when rendering it directly in a Blade template.

## HTML sanitization

When rendering HTML content via methods like `html()` or `markdown()` on components such as `TextColumn` and `TextEntry`, Filament automatically sanitizes the output using Symfony's [HtmlSanitizer](https://symfony.com/doc/current/html_sanitizer.html) component. This removes potentially dangerous elements like `<script>` tags to help prevent XSS attacks.

### Default sanitizer configuration

Filament registers `HtmlSanitizerConfig` as a scoped binding in Laravel's service container with the following default configuration:

```php
use Symfony\Component\HtmlSanitizer\HtmlSanitizerConfig;

(new HtmlSanitizerConfig)
    ->allowSafeElements()
    ->allowRelativeLinks()
    ->allowRelativeMedias()
    ->allowAttribute('class', allowedElements: '*')
    ->allowAttribute('data-color', allowedElements: '*')
    ->allowAttribute('data-cols', allowedElements: '*')
    ->allowAttribute('data-col-span', allowedElements: '*')
    ->allowAttribute('data-from-breakpoint', allowedElements: '*')
    ->allowAttribute('data-id', allowedElements: '*')
    ->allowAttribute('data-type', allowedElements: '*')
    ->allowAttribute('style', allowedElements: '*')
    ->allowAttribute('width', allowedElements: 'img')
    ->allowAttribute('height', allowedElements: 'img')
    ->withMaxInputLength(500000)
```

The `data-*` attributes are used internally by Filament's rich editor for features such as text colors, grid layouts, merge tags, mentions, and custom blocks. The `style` attribute is necessary to support rich text formatting features such as font colors, text highlighting, and image sizing. However, this means that CSS properties like `background: url(...)` (which can trigger external HTTP requests) or `position: fixed` (which can create phishing overlays) will not be stripped.

If your application renders HTML content from untrusted users, you should consider restricting the default configuration.

### Customizing the sanitizer

Since `HtmlSanitizerConfig` is bound in the service container, you can use `extend()` in a service provider to modify the default configuration without replacing it entirely.

#### Adding allowed attributes

To allow additional attributes through the sanitizer, extend the config:

```php
use Symfony\Component\HtmlSanitizer\HtmlSanitizerConfig;

public function register(): void
{
    $this->app->extend(
        HtmlSanitizerConfig::class,
        fn (HtmlSanitizerConfig $config): HtmlSanitizerConfig => $config
            ->allowAttribute('data-custom', allowedElements: '*'),
    );
}
```

#### Restricting allowed attributes

To remove an attribute that Filament allows by default, use `dropAttribute()`:

```php
use Symfony\Component\HtmlSanitizer\HtmlSanitizerConfig;

public function register(): void
{
    $this->app->extend(
        HtmlSanitizerConfig::class,
        fn (HtmlSanitizerConfig $config): HtmlSanitizerConfig => $config
            ->dropAttribute('style', '*'),
    );
}
```

<Aside variant="danger">
    Removing attributes that Filament's rich editor depends on (such as `data-color`, `data-cols`, `data-id`, or `style`) may break rich text rendering. Only restrict attributes when you understand their impact on Filament's components.
</Aside>

#### Replacing the sanitizer configuration entirely

If you need full control, you can rebind `HtmlSanitizerConfig` entirely in a service provider:

```php
use Symfony\Component\HtmlSanitizer\HtmlSanitizerConfig;

public function register(): void
{
    $this->app->scoped(
        HtmlSanitizerConfig::class,
        fn (): HtmlSanitizerConfig => (new HtmlSanitizerConfig)
            ->allowSafeElements()
            ->allowRelativeLinks()
            ->allowRelativeMedias()
            ->allowAttribute('class', allowedElements: '*')
            ->withMaxInputLength(500000),
    );
}
```

Refer to the [Symfony HtmlSanitizer documentation](https://symfony.com/doc/current/html_sanitizer.html) for the full list of configuration options.

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

### Multi-factor authentication

Filament supports [multi-factor authentication](../users/multi-factor-authentication) via TOTP apps and email codes, but it is not enabled by default. MFA is enforced within the Filament panel authentication flow — if your application has other authentication paths (such as API routes or non-Filament login pages), MFA will not be enforced on those paths unless you implement it separately.

## Model attribute exposure

Filament exposes all non-`$hidden` model attributes to JavaScript via Livewire's model binding. This is necessary for dynamic form functionality, and only attributes with corresponding form fields are actually editable — this is not a mass assignment vulnerability. However, if your model contains sensitive attributes that should not be visible in the browser (such as API keys or internal flags), you should either add them to the model's `$hidden` property or remove them using the `mutateFormDataBeforeFill()` method on your Edit or View page. See the [resources documentation](../resources/overview#protecting-model-attributes) for more details.

## File uploads

Filament's `FileUpload` component uses Livewire's file upload mechanism. There are important security considerations when allowing users to upload files, particularly around file names, storage visibility, and accepted file types.

By default, Filament generates random file names and stores files with `private` visibility. If you use `preserveFilenames()` or `getUploadedFileNameForStorageUsing()` with local or public disks, an attacker could upload a PHP file with a deceptive MIME type that gets executed by your server. The safer alternative is to use `storeFileNamesIn()`, which stores original file names in a separate database column while keeping randomly generated file names on disk. See the [file upload documentation](../forms/file-upload#security-implications-of-controlling-file-names) for a full explanation of these risks and recommended mitigations.

You should always use `acceptedFileTypes()` to restrict the types of files users can upload, and validate file sizes with `maxSize()`. These constraints are enforced server-side, not just in the browser.

## File path tampering

The value of a `FileUpload` field is a string (or array of strings) pointing to a file on its configured disk. The `RichEditor` embeds images by storing their identifier in the `data-id` attribute of each image node, which is similarly resolved against a disk when the content is rendered. Like any other Livewire form field value, both are controlled by the client — a request can be intercepted to change a submitted path or `data-id` to any other file on the same disk. If the disk also stores files belonging to other users or records, an attacker can cause a record to reference (and serve a signed URL for) someone else's file.

Filament allows this by default because legitimate features depend on it — for example, an action that sets a field to a pre-uploaded template file, or a "copy from another record" button. If your forms do not rely on such a flow, opt in to the built-in checks:

- For `FileUpload` fields, call [`preventFilePathTampering()`](../forms/file-upload#authorizing-existing-file-paths) to fail validation when a submitted path does not match the original value on the record.
- For `RichEditor` fields, call [`preventFileAttachmentPathTampering()`](../forms/rich-editor#securing-file-attachment-ids) to fail validation when a submitted `data-id` is not already present in the record's stored content.

Both methods compare submitted values against the attribute on the record via `$record->getOriginal()`, and both accept an `allowFilePathUsing` callback for paths that are legitimately added outside the record (such as shared template files). Newly uploaded files and images always pass through unchanged.

<Aside variant="warning">
    These checks require a record on the form, so on create pages every submitted existing path fails validation unless the `allowFilePathUsing` callback approves it. New uploads are unaffected.
</Aside>

If you want these checks to apply across your entire application rather than remembering to add them to each field, enable them globally from a service provider's `boot()` method using `configureUsing()`:

```php
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor;

FileUpload::configureUsing(function (FileUpload $component): void {
    $component->preventFilePathTampering();
});

RichEditor::configureUsing(function (RichEditor $component): void {
    $component->preventFileAttachmentPathTampering();
});
```

Individual fields can still opt out by passing `false` to the corresponding method (for example, `preventFilePathTampering(false)`) when a specific form legitimately needs to accept paths that are not on the record.

If your application isolates uploads per user or per record at the disk level — for example, by using a separate disk or directory for each tenant — this class of tampering is not exploitable and these methods are unnecessary. The `spatie/laravel-medialibrary` rich editor provider also performs an equivalent check implicitly by looking up each `data-id` against the record's own media collection.

## Scoping queries

When building tables, resources, or custom Livewire components, ensure that database queries are properly scoped to the current user's permissions. Filament's resource system uses Eloquent queries that return all records by default — it is up to you to apply appropriate query scopes using the `modifyQueryUsing()` method on your table or by overriding the `getEloquentQuery()` method on your resource to ensure users can only access records they are authorized to see.

For example, in a multi-tenant application, forgetting to scope queries to the current tenant would allow users to see other tenants' data. If you are using Filament's built-in [tenancy](../users/tenancy) features, queries are scoped automatically for resources. However, any custom queries, actions, or pages you build must be scoped manually.
