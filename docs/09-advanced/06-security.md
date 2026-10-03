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

Filament repeats its built-in authorization checks when a component mounts and on later Livewire requests. If a user's access changes while a component is open, their next interaction is checked against their current access:

- Resource pages repeat their page access checks. For example, edit and view pages check access to the resource, its parent resources, and the current record. A `ManageRelatedRecords` page checks access to the related resource or model, but you must add an owner-record check to its `canAccess()` method if your application requires one.
- Custom panel pages that extend `Filament\Pages\Page` check `canAccess()`.
- Relation managers check `canViewForRecord()`.
- Widgets check `canView()`.
- Tenant registration and profile pages check `canView()`.

Panel access is also checked by `canAccessPanel()` on every request, including Livewire requests.

### Understanding Eloquent model restoration in Livewire

When a public Livewire property contains an Eloquent model, Livewire stores the model's identifier in the component snapshot. On the next request, Livewire queries the database to restore it. A signed snapshot prevents a user from changing the identifier, but it does not prove that they are still allowed to access the model.

Livewire does not reuse the query that originally loaded the model. Its restoration query does not apply Filament resource or table queries, and Laravel does not apply the model's global scopes by default.

#### Re-querying built-in model properties

On later requests, Filament runs another query for these built-in properties:

- Resource page `$record`: the resource query, including nested parent scoping.
- Nested resource page `$parentRecord`: the parent resource query when the page has route context; otherwise, the model's global scopes.
- Relation manager `$ownerRecord`: the model's global scopes.
- Widget `$record` and `$parentRecord`: the model's global scopes.
- Modal table select `$record` and tenant registration `$tenant`: the model's global scopes.
- Tenant profile `$tenant`: the panel's current tenant.

If the query can no longer find the model, Filament returns a 404 response. Any policy or component authorization check runs separately.

Table row actions resolve their record through the table query. Attach and associate actions resolve selected records through their configured relationship or options query. Filament does not provide either guarantee for model values passed to custom Livewire methods or action arguments.

<Aside variant="warning">
    A scoped query is not an authorization check. For example, a widget's `canView()` method controls access to the widget, but it does not authorize the widget's `$record`. If access depends on a policy or another condition, authorize the model yourself.
</Aside>

#### Protecting your own model properties

Filament does not re-query model values that belong to your application, including:

- model properties that you add to a custom Livewire component, custom page, or other Filament component,
- application-owned model properties on a Livewire component embedded inside a schema,
- model arguments passed to a lazy component's `mount()` method,
- models nested inside arrays, collections, form state, or other public properties, and
- model values passed through custom Livewire method or action arguments.

Do not assume that these models still pass the query or authorization rules that applied when they were first loaded. Store a scalar key instead, then query and authorize the model when you need it. You may also re-query and authorize a restored model before using it.

For example, store a locked record ID and use a computed property to query and authorize the record when you access it:

```php
use App\Models\Post;
use Illuminate\Support\Facades\Gate;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Locked;

#[Locked]
public int $postId;

#[Computed]
public function post(): Post
{
    $post = Post::query()->findOrFail($this->postId);

    Gate::authorize('view', $post);

    return $post;
}
```

Relationships use their relationship query and the related model's global scopes. Filament does not automatically run a policy for every related model that your code accesses.

### Running code before authorization

On a later Livewire request:

1. Livewire restores public properties. This can run an unscoped model query, Eloquent retrieval events, and callbacks such as a Livewire form object's `boot()` method.
2. Filament re-queries the built-in model properties listed above. A model that no longer passes this query is rejected before your component lifecycle hooks run.
3. Filament runs the component's applicable authorization checks. Passing the query in the previous step does not mean that the model is authorized.

Some lifecycle hooks can run before step 3:

- A page's `boot()`, trait initializers, and early `mount()` or `hydrate()` code.
- A widget or relation manager's `boot()` method.
- A lazy widget or relation manager's placeholder and initial dehydration hooks.

Do not rely on the order of lifecycle hooks from different traits for authorization. If code must not run for an unauthorized user, authorize the user before running it. This includes database writes, events, audit logging, and external service calls.

After the applicable built-in authorization check, Livewire runs per-property hydration hooks, requested property updates, public methods requested by Livewire, actions, and normal rendering. This only protects models covered by that check. For example, a custom page's `canAccess()` method does not authorize each model property that you add to the page.

<Aside variant="warning">
    Filament explicitly checks widget and relation manager authorization while a lazy component is waiting to mount. Pages do not have an equivalent check during this waiting state. Do not enable Livewire lazy loading on a Filament page that relies on page authorization hooks.
</Aside>

### Inline editable columns

Inline editable table columns such as `ToggleColumn`, `TextInputColumn`, `SelectColumn`, and `CheckboxColumn` do not check Model Policies before saving changes. They only check the column's `disabled()` state. If you need to restrict who can edit these columns, use the `disabled()` method with your own authorization logic. See the documentation for each [editable column type](../tables/columns/toggle) for more details.

### Custom actions

When you create [custom actions](../actions/overview#authorization), you are responsible for authorizing them. Filament provides `visible()`, `hidden()`, and `authorize()` methods to help with this, but you must use them — they are not applied automatically. If an action modifies data or performs sensitive operations, always ensure it is authorized.

### Testing authorization

Your application should have a comprehensive test suite that verifies authorization is enforced correctly across all entry points — not just Filament's resource pages, but also any custom actions, custom pages, Livewire components, API routes, and other functionality. Filament provides [testing helpers](../testing/overview) for asserting that actions, pages, and resources behave correctly for different user roles.

If a component keeps a model between requests, test what happens when its access changes. Mount the component, change the model's tenant, ownership, scope, or policy result, then send another request using the existing component. Assert that the requested update, action, or render is rejected. Test any authorization that protects earlier lifecycle code separately.

Do not rely solely on Filament's built-in policy checks. Treat them as a helpful layer, but always verify that your authorization rules are enforced end-to-end through testing.

## Validating user input

Many Filament configuration methods accept closures that can return dynamic values. Methods like `url()`, `icon()`, `html()`, and others are designed to be flexible, allowing developers to build rich, dynamic interfaces. However, when the values passed to these methods originate from user input or untrusted database content, it is your responsibility to validate and sanitize them appropriately.

For example, the `url()` method on columns, entries, and actions renders an `<a href="...">` tag with whatever value you provide. If you pass a URL sourced from user input without validation, a malicious value like `javascript:alert(document.cookie)` could be rendered as a clickable link, leading to XSS. Always validate that URLs use a safe scheme such as `http` or `https` before passing them to Filament.

Filament ships a `Str::sanitizeUrl()` helper that returns the URL when it is schemeless (relative) or uses the `http`/`https` scheme, and returns `null` for anything else. Before checking the scheme, it decodes HTML entities and rejects control characters and raw whitespace. The return value is the original input unchanged when it passes the check; the helper does not HTML-escape or rewrite the URL. Filament escapes the value when its configuration methods render it into an HTML attribute. If you render the value yourself, escape it as an HTML attribute, including its ampersands.

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
- guarantee safety after you transform its return value. For example, if you call `urldecode()` before setting `location.href`, apply your own scheme check to the transformed value,
- validate that an `http(s)` URL is reachable or trusted in any other way.

If you need any of those guarantees, layer your own check on top of the helper's return value.

For links that must stay within your application, prefer generating the URL from a named route instead of accepting a complete URL. If you accept complete URLs and restrict their hosts, use a URL parser that follows the browser URL standard. Do not use `parse_url()` alone for a security allowlist, since PHP and browsers can interpret the same URL differently.

Similarly, the `ColorColumn` and `ColorEntry` components render their state into a `background-color` CSS declaration. Filament runs every value through a `Str::sanitizeCssColor()` helper that only allows hex colors (`#rgb`, `#rgba`, `#rrggbb`, `#rrggbbaa`), bare CSS keyword colors (like `red`), and the functional notations `rgb()`, `rgba()`, `hsl()`, `hsla()`, `hwb()`, `lab()`, `lch()`, `oklab()`, `oklch()`, and `color()` whose contents do not contain CSS metacharacters. Anything else — such as `red;position:fixed;inset:0;background-image:url(//attacker)` — is rejected and the declaration is omitted, preventing a stored value from injecting extra CSS. You can call `Str::sanitizeCssColor()` yourself anywhere you build a color style from untrusted input; it returns the original value when it passes and `null` otherwise.

The `RichEditor` applies the same principle to the CSS it generates from stored content: text color marks route their color through `Str::sanitizeCssColor()`, and the grid layout blocks cast their column counts to integers before interpolating them into a `style` attribute. This matters because the HTML sanitizer that cleans rich content allows the `style` attribute through without parsing the CSS inside it, so any value emitted into a `style` string must be sanitized before it gets there.

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

For example, in a multi-tenant application, forgetting to scope queries to the current tenant would allow users to see other tenants' data. If you are using Filament's built-in [tenancy](../users/tenancy) features, Filament registers a tenant global scope for tenant-aware resource models after the current tenant is identified. Ordinary Eloquent queries for those models then use the scope, including queries in custom actions and pages. You must scope queries for other models, queries made before the tenant is identified or outside the tenant-aware panel, and queries that remove the scope. See [tenancy security](../users/tenancy#tenancy-security) for all limitations.

## Content Security Policy (CSP)

A Content Security Policy lets the browser reject scripts that you did not put on the page yourself, which is one of the strongest defences against cross-site scripting. Filament can add a [nonce](https://developer.mozilla.org/en-US/docs/Web/HTML/Global_attributes/nonce) to scripts rendered through its asset manager.

### Configuring the nonce

Filament uses Laravel's `Vite` nonce, which is also used by Livewire. After your Content Security Policy middleware generates a nonce, pass it to `Vite::useCspNonce()` before the response is rendered:

```php
use Illuminate\Support\Facades\Vite;

Vite::useCspNonce($nonce);
```

You must do this for every request, using the same nonce that you add to the `Content-Security-Policy` header. Do not configure it once in a service provider's `boot()` method, since the nonce must change between requests and service providers are not booted again between requests when using a long-running worker such as Laravel Octane.

Until you configure a nonce, Filament does not add a `nonce` attribute.

If you use [spatie/laravel-csp](https://github.com/spatie/laravel-csp), you can create middleware that resolves its request-scoped nonce and shares it with Laravel before the panel is rendered:

```php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Vite;
use Symfony\Component\HttpFoundation\Response;

class SetCspNonce
{
    public function handle(Request $request, Closure $next): Response
    {
        Vite::useCspNonce(app('csp-nonce'));

        return $next($request);
    }
}
```

<Aside variant="info">
    The `Vite` facade is only used to share the nonce between Laravel, Livewire, and Filament. Your application does not need to compile its assets with Vite or use the `@vite` Blade directive, so this also works in applications that use alternative frontend tooling.
</Aside>

### Sending the policy header

Whichever package or middleware you use to send the `Content-Security-Policy` header, register it in your panel's `middleware()` method rather than in your application's `web` middleware group:

```php
use App\Http\Middleware\SetCspNonce;
use Filament\Panel;
use Spatie\Csp\AddCspHeaders;

public function panel(Panel $panel): Panel
{
    return $panel
        // ...
        ->middleware([
            // ...
            SetCspNonce::class,
            AddCspHeaders::class,
        ]);
}
```

<Aside variant="warning">
    Panels build their own middleware stack and do not resolve Laravel's `web` group, so middleware appended to `web` never runs on panel routes and no header is sent. This is a common cause of a policy that appears to be configured but has no effect.
</Aside>

Registering it on the panel also scopes the policy to that panel, leaving the rest of your application untouched.

### Rendering the nonce in your own views

If you write your own `<script>` elements, in a custom page, a [render hook](render-hooks), or a plugin, you are responsible for adding the nonce to them. You can retrieve it using `Vite::cspNonce()`:

```blade
<script nonce="{{ \Illuminate\Support\Facades\Vite::cspNonce() }}">
    // ...
</script>
```

<Aside variant="info">
    Filament adds the nonce to the scripts it renders itself: the script tags from `FilamentAsset`, the inline `window.filamentData` script, and the inline scripts in Filament's own Blade views, such as the panel layout and the sidebar. Scripts registered with `Js::make()->html()` are rendered exactly as you provide them, so you must add the nonce to that HTML yourself.
</Aside>

<Aside variant="warning">
    Filament does not modify content that you inject through [render hooks](render-hooks), the `scripts` Blade stack, or `RawJs`, so you must add the nonce to any inline script you render there yourself. Configuring this nonce does not, by itself, allow you to remove `'unsafe-inline'` from `script-src`; you must audit and handle every script rendered by your application. Scripts inside Livewire's `@script` directive are evaluated by Alpine.js rather than executed as script elements, so they are governed by `script-src 'unsafe-eval'` rather than by the nonce. Filament also does not currently support a strict `style-src` directive, since Livewire, Alpine.js, and TipTap rely on inline styles.
</Aside>

### Allowing the file upload's web workers

The [file upload field](../forms/file-upload) generates image previews and [resizes images](../forms/file-upload#cropping-and-resizing-images-without-the-editor) in web workers, which it creates from `blob:` URLs. If your policy has no `worker-src` directive, the browser checks these workers against `script-src` instead, which usually does not allow `blob:`. The workers are then blocked: images are uploaded without a preview, and uploads that resize images never finish.

To allow these workers without allowing `blob:` scripts anywhere else, add `worker-src 'self' blob:` to your policy.
