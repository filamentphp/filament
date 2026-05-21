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

### Authorization and the Livewire request lifecycle

Filament re-runs authorization on every Livewire request — both on the initial page load and on every subsequent update (search, filter, pagination, action call, form interaction). This means that if a user's permissions change while they are using the panel, the next interaction they make will be authorized against the current policy state, not the policy state at the time the component was first mounted.

This applies across every Livewire component Filament ships:

- **Resource pages** (`ListRecords`, `CreateRecord`, `EditRecord`, `ViewRecord`, `ManageRelatedRecords`) — the resource-level `Resource::canAccess()` check (and parent resource's check, if any) re-runs on every request via the `CanAuthorizeResourceAccess` trait. The page-specific record-scoped checks (`canEdit($record)`, `canView($record)`, `canCreate()`, parameterized `canAccess(['record' => ...])`) re-run on every request via each page type's `hydrate()` method, mirroring the existing `mount()`-time call to `$this->authorizeAccess()`.
- **Custom panel pages** (anything extending `Filament\Pages\Page`, including `SettingsPage`, the auth pages, the dashboard, cluster pages) — the page's `canAccess()` method re-runs on every request via the `CanAuthorizeAccess` trait.
- **Relation managers** — the `canViewForRecord($ownerRecord, $pageClass)` check re-runs on every request via the `CanAuthorizeAccess` trait under `Filament\Resources\RelationManagers\Concerns`. Initial mount is gated by the parent page's render-time filter, so the trait only registers a hydrate-time check to avoid a duplicate call on the first request.
- **Widgets** — the static `canView()` check re-runs on every request via the `CanAuthorizeAccess` trait under `Filament\Widgets\Concerns`. As with relation managers, the parent dashboard's render-time filter handles the initial-mount gate.
- **Tenancy pages** (`RegisterTenant`, `EditTenantProfile`) — their `canView()` checks re-run on every Livewire request via a `hydrate()` method mirroring the existing `mount()`-time check.

Panel-level access (`canAccessPanel`) is enforced by the panel's `Authenticate` middleware, which runs on every HTTP request (including Livewire updates) — so users who lose panel access mid-session are bounced at the middleware layer before any component-level authorization is consulted.

When you build custom Livewire components on a Filament panel, be aware that **several Livewire activities run before Filament's authorization hooks fire**:

- Public properties are deserialized from the request payload (Livewire's "synth" step) before any of your hooks run.
- The `boot()` and `boot{TraitName}()` lifecycle hooks fire before authorization.
- The user's `mount()` body runs before trait-level `mount{TraitName}` hooks on initial mount.
- Per-property `hydrate{PropertyName}()` hooks fire after Filament's hydrate-time authorization but still complete before the request progresses to update or render.

In practice this means **work that happens during these earlier hooks runs even when authorization will subsequently abort the request**. Filament aborts before the response is rendered or any update method is called, so unauthorized data is never returned to the user, but server-side side effects (database queries to resolve a record, audit log entries that fire on `SELECT`, dispatched events in custom hooks, etc.) can occur before the abort.

If your component does anything significant that should not happen for an unauthorized user — emitting events, writing to the database, calling external services — do that work in a method or hook that runs **after** Filament's authorization has already fired (for example, in the `mount()` body **after** an explicit `$this->authorizeAccess()` call, or in an action method invoked via `wire:click`, which always runs post-authorization). Avoid putting such work in `boot()` or per-property hydrate hooks.

### Inline editable columns

Inline editable table columns such as `ToggleColumn`, `TextInputColumn`, `SelectColumn`, and `CheckboxColumn` do not check Model Policies before saving changes. They only check the column's `disabled()` state. If you need to restrict who can edit these columns, use the `disabled()` method with your own authorization logic. See the documentation for each [editable column type](../tables/columns/toggle) for more details.

### Custom actions

When you create [custom actions](../actions/overview#authorization), you are responsible for authorizing them. Filament provides `visible()`, `hidden()`, and `authorize()` methods to help with this, but you must use them — they are not applied automatically. If an action modifies data or performs sensitive operations, always ensure it is authorized.

### Testing authorization

Your application should have a comprehensive test suite that verifies authorization is enforced correctly across all entry points — not just Filament's resource pages, but also any custom actions, custom pages, Livewire components, API routes, and other functionality. Filament provides [testing helpers](../testing/overview) for asserting that actions, pages, and resources behave correctly for different user roles.

Do not rely solely on Filament's built-in policy checks. Treat them as a helpful layer, but always verify that your authorization rules are enforced end-to-end through testing.

## Validating user input

Many Filament configuration methods accept closures that can return dynamic values. Methods like `url()`, `icon()`, `html()`, and others are designed to be flexible, allowing developers to build rich, dynamic interfaces. However, when the values passed to these methods originate from user input or untrusted database content, it is your responsibility to validate and sanitize them appropriately.

For example, the `url()` method on columns, entries, and actions renders an `<a href="...">` tag with whatever value you provide. If you pass a URL sourced from user input without validation, a malicious value like `javascript:alert(document.cookie)` could be rendered as a clickable link, leading to XSS. Always validate that URLs use a safe scheme such as `http` or `https` before passing them to Filament.

Filament ships a `Str::sanitizeUrl()` helper that returns the URL when it is schemeless (relative) or uses the `http`/`https` scheme, and returns `null` for anything else. It also normalizes obfuscation tricks such as leading whitespace, embedded control characters (`\t`, `\n`, `\r`, NUL bytes), and mixed-case schemes before checking — so values like `"\tJaVa\nScRiPt:alert(1)"` are rejected, and even on a safe URL the return value has those bytes stripped so they cannot reach the rendered HTML:

```php
use Filament\Tables\Columns\TextColumn;
use Illuminate\Support\Str;

TextColumn::make('website')
    ->url(fn (string $state): ?string => Str::sanitizeUrl($state))
```

You can call the helper anywhere a URL is being passed to a Filament configuration method (`url()`, `image()`, `icon()` when given a URL, `openUrlInNewTab()` callbacks, and so on). Internally Filament already runs every file URL it emits from components like `FileUpload` and `SpatieMediaLibraryFileUpload` through this helper.

If you need to allow additional schemes — for example `mailto:` or `tel:` — pass them in as the second argument. The default allowlist is replaced by what you pass, so include `http` and `https` if you still want them:

```php
TextColumn::make('contact')
    ->url(fn (string $state): ?string => Str::sanitizeUrl(
        $state,
        allowedSchemes: ['http', 'https', 'mailto', 'tel'],
    ))
```

`Str::sanitizeUrl()` is a scheme allowlist designed to prevent XSS from dangerous URL schemes. It does not:

- check that the host belongs to a domain you control (open-redirect protection),
- check that the URL is safe for the server to fetch (SSRF protection),
- decode percent-encoded or HTML-entity-encoded payloads (the browser's URL parser doesn't either, so this is intentional, but it means callers that decode the value before rendering need their own check on the decoded form),
- validate that an `http(s)` URL is reachable or trusted in any other way.

If you need any of those guarantees, layer your own check on top of the helper's return value.

If you need a stricter allowlist (for example, only your own domains), wrap the helper:

```php
TextColumn::make('website')
    ->url(function (string $state): ?string {
        $sanitized = Str::sanitizeUrl($state);

        if (blank($sanitized)) {
            return null;
        }

        $host = parse_url($sanitized, PHP_URL_HOST);

        return in_array($host, ['example.com', 'cdn.example.com'], true)
            ? $sanitized
            : null;
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

## File uploads and rich editor attachments

Filament's `FileUpload` and `RichEditor` components both have their own security considerations — uploaded file names, storage visibility, accepted file types, and client-controlled file paths can each be abused if misconfigured. The relevant guidance lives alongside each component:

- [File upload security](../forms/file-upload#security-implications-of-controlling-file-names) — file name preservation risks and the [authorizing existing file paths](../forms/file-upload#authorizing-existing-file-paths) flow.
- [Rich editor file attachment IDs](../forms/rich-editor#securing-file-attachment-ids) — `data-id` tampering and how the default vs. `spatie/laravel-medialibrary` providers differ in scoping.

### Restricting Livewire file uploads to schema components

Every Livewire component that uses the `InteractsWithSchemas` trait exposes Livewire's `_startUpload` and `_finishUpload` RPC methods, because the trait composes Livewire's `WithFileUploads` so that schema components like `FileUpload` and `MarkdownEditor` can use Livewire's standard file upload mechanism. By default, those RPC methods accept uploads to any Livewire property name — they do not check whether the property corresponds to a real upload field in the component's schemas. This means an attacker who can reach the page can tamper with a Livewire request to upload files to arbitrary property paths on any page that uses `InteractsWithSchemas`, even pages that do not display an upload field at all.

If your Livewire component is reachable to users you do not want uploading arbitrary files (for example, an unauthenticated page, or any page whose schema does not contain an upload field), add the `RestrictsFileUploadsToSchemaComponents` trait. This causes `_startUpload` and `_finishUpload` to abort with a `403` response unless the upload's target property maps to a `FileUpload` field (or any field that supports file attachments) registered in one of the component's schemas:

```php
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Concerns\RestrictsFileUploadsToSchemaComponents;
use Filament\Schemas\Contracts\HasSchemas;
use Livewire\Component;

class ViewProduct extends Component implements HasSchemas
{
    use InteractsWithSchemas;
    use RestrictsFileUploadsToSchemaComponents;

    // ...
}
```

With the trait in place, an attacker tampering with a Livewire request to upload to an arbitrary property name is rejected; legitimate uploads from your schema's `FileUpload` fields continue to work because their target property matches a registered component. Uploads from components that support file attachments — such as the [`MarkdownEditor`](../forms/markdown-editor) and [`RichEditor`](../forms/rich-editor) — are also allowed when they target a registered component.

<Aside variant="tip">
    Hidden fields are not considered matchable targets. If a `FileUpload` field is conditionally hidden (`->visible(false)` or equivalent), uploads to its state path are rejected — only fields the user can actually see are valid upload targets.
</Aside>

## Scoping queries

When building tables, resources, or custom Livewire components, ensure that database queries are properly scoped to the current user's permissions. Filament's resource system uses Eloquent queries that return all records by default — it is up to you to apply appropriate query scopes using the `modifyQueryUsing()` method on your table or by overriding the `getEloquentQuery()` method on your resource to ensure users can only access records they are authorized to see.

For example, in a multi-tenant application, forgetting to scope queries to the current tenant would allow users to see other tenants' data. If you are using Filament's built-in [tenancy](../users/tenancy) features, queries are scoped automatically for resources. However, any custom queries, actions, or pages you build must be scoped manually.
