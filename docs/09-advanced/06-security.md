---
title: Security
---


## Introduction

<Aside variant="info">
    This page provides a general overview of security considerations when using Filament. Many individual features have their own specific security recommendations documented alongside them — for example, file uploads, rich editors, inline editable columns, and more. When using any Filament feature, make sure to read the full documentation for that feature, including any security warnings it contains.
</Aside>

Filament is a powerful framework that gives developers extensive control over how components are configured and rendered. This flexibility is by design — developers need to be able to do powerful things with configuration methods like `url()`, `icon()`, `html()`, and others. However, this means that Filament trusts the values you pass into these methods, and it is your responsibility to ensure that any user-supplied data is properly validated and sanitized before it reaches Filament.

This page covers key security considerations when building applications with Filament, including authorization, input validation, and HTML sanitization.

## Authorization

### Resource authorization

Filament automatically checks [Laravel Model Policies](https://laravel.com/docs/authorization#creating-policies) for standard CRUD operations on [resources](../resources/overview#authorization). When a policy exists for a resource's model, Filament will check methods like `viewAny()`, `create()`, `update()`, `view()`, `delete()`, and others before allowing access to the corresponding pages and actions.

<Aside variant="warning">
    By default, a missing resource policy or policy method does not deny access: Filament allows the operation unless a Gate before callback denies it. Define the policy methods your application needs, or enable [strict authorization](../panel-configuration#strict-authorization-mode) to detect missing policies and methods. Strict mode does not add authorization to custom actions, inline editable columns, or other code that does not use resource authorization. Skipping authorization on a resource bypasses its policy checks even in strict mode.
</Aside>

However, Filament's automatic authorization only covers these built-in resource operations. Any custom functionality you add — custom actions, custom pages, custom Livewire components, API endpoints, or other business logic — must be authorized by you. Filament cannot know your application's authorization requirements beyond the standard CRUD operations it provides.

Resource access and record scoping are separate. A `viewAny()` check controls access to the resource; it does not filter its table through each record's `view()` policy. Likewise, built-in bulk delete, force-delete, and restore actions check their `*Any()` abilities, not each record's policy by default. Apply query restrictions for records a user must not see, and enable [individual record authorization](../tables/actions#authorizing-bulk-actions) when a bulk operation needs per-record checks.

### Authorization and the Livewire request lifecycle

Filament repeats its built-in authorization checks when a component mounts and on later Livewire requests. If a user's access changes while a component is open, their next interaction is checked against their current access:

- Resource pages repeat their page access checks. For example, edit and view pages check access to the resource, its parent resources, and the current record. A `ManageRelatedRecords` page checks access to the related resource or model, but you must add an owner-record check to its `canAccess()` method if your application requires one.
- Custom panel pages that extend `Filament\Pages\Page` check `canAccess()`.
- Relation managers check `canViewForRecord()`.
- Widgets check `canView()`.
- Tenant registration and profile pages check `canView()`.

Panel access is also checked by `canAccessPanel()` on every request, including Livewire requests.

### Restricting methods to server-side calls

Livewire exposes public component methods to client requests, even when there is no button or other UI that calls them. Use `protected` or `private` visibility for methods that do not need to be public. If a method must remain public so that other PHP objects can call it, you may add the `#[Unexposed]` attribute:

```php
use Filament\Support\Components\Attributes\Unexposed;

#[Unexposed]
public function recalculateTotals(): void
{
    // ...
}
```

Filament rejects client calls to this method, including JavaScript calls, `wire:click` calls, and Livewire event listener dispatches, with HTTP 403 before executing the method. Laravel does not report these HTTP exceptions by default. Direct PHP calls such as `$component->recalculateTotals()` and `$this->recalculateTotals()` still work, including during a Livewire request.

Methods declaring a return type of `Filament\Support\Components\Component` or a subclass are also unexposed by default, without an attribute. This includes `Schema`, `Table`, `Action`, fields, entries, columns, filters, and custom subclasses. For example, `form(): Schema`, `table(): Table`, and `editAction(): Action` are automatically restricted. Nullable types such as `?Schema` are included. Filament checks the declared type before executing the method; it does not infer component types from `mixed`, `object`, arrays, untyped methods, or union and intersection types.

An explicit `#[Exposed]` overrides this return-type default. Explicit access attributes, including those inherited from parent classes, take priority over the default.

Method access attributes are inherited from parent classes when a child overrides a method without specifying its own access attribute. To allow frontend calls to an inherited `#[Unexposed]` method, apply `#[Exposed]` to the override:

```php
use Filament\Support\Components\Attributes\Exposed;

#[Exposed]
public function recalculateTotals(): void
{
    parent::recalculateTotals();
}
```

The nearest declaration in the parent-class chain with an access attribute determines access. A child can also apply `#[Unexposed]` to block a parent's exposed method. If a declaration has both attributes, `#[Unexposed]` takes precedence. Methods without either attribute anywhere in their parent-class chain use the return-type default described above; other methods retain Livewire's normal behavior. `#[Exposed]` does not bypass Livewire's visibility restrictions or make lifecycle methods callable.

Trait methods use PHP's native attribute behavior:

- Importing an annotated trait method, including under an alias, retains its attributes.
- Replacing a trait method in a class or another trait does not inherit the replaced method's attributes. The replacement uses its own attributes, parent-class policy, or return-type default.
- Attributes on abstract trait requirements or methods discarded during trait conflict resolution do not establish access policy. Annotate the selected concrete implementation if you need to override its default policy.

Filament does not reconstruct displaced trait declarations or combine their policies at runtime. Parent-class inheritance still applies to the resulting method.

Filament's own public component and component-trait method declarations carry explicit access attributes, including overrides: `#[Unexposed]` for internal methods and `#[Exposed]` for intended frontend entry points. The repository's Rector rule adds `#[Unexposed]` to declarations missing either attribute; it does not infer frontend exposure from parents or consumers. Intended frontend overrides must explicitly declare `#[Exposed]`.

New methods you declare in your application are not automatically restricted unless their declared return type matches the component rule above. Overriding a Filament parent-class method inherits its access policy; replacing a trait method follows the rules above. Add `#[Exposed]` if your frontend integration needs to call an unexposed method, and implement any required authorization before calling it.

<Aside variant="danger">
    This attribute only controls Livewire component method calls, not methods on schema components or table columns. It blocks direct client invocation, not calls made by other PHP code. An exposed action, listener, or forwarding method can still call the method internally. You must authorize those entry points yourself. The attribute does not prevent Livewire from invoking lifecycle hooks or rendering methods internally, and it does not protect public properties from client updates.
</Aside>

### Protecting server-managed component state

Filament uses Livewire's `#[Locked]` attribute for server-managed public properties, including record identities, notification collections, table loading and reordering flags, default-action settings, and reactive widget inputs supplied by a parent page. Direct PHP assignments still work, but JavaScript assignments, `$wire.set()`, and other incoming property updates are rejected. Use the component's supported methods and events instead, such as `loadTable()`, `toggleTableReordering()`, `mountAction()`, and notification delivery events.

Parent-driven updates to reactive widget inputs still work. For example, update dashboard filters on the parent page rather than assigning the widget's `$pageFilters` property from JavaScript. Ordinary editable form state, table filters, search, sorting, pagination, and selection remain writable on their owning components.

Locked URL-bound properties, such as default-action settings and `$isTableReordering`, still accept their initial query string values. Keep their default replace-mode URL synchronization: push-history restoration requires client property writes and conflicts with `#[Locked]`.

If you redeclare a locked property in an application component, add `#[Locked]` to the new declaration too. PHP does not inherit property attributes onto redeclarations.

<Aside variant="danger">
    Locking prevents direct property updates; it does not make initial URL values, method arguments, or event payloads trustworthy. It also does not hide serialized values, authorize records, or change Eloquent model restoration. Validate and authorize the input accepted by your own methods, actions, and listeners.
</Aside>

### Understanding Eloquent model restoration in Livewire

When a public Livewire property contains an Eloquent model, Livewire stores the model's identifier in the component snapshot. On the next request, Livewire queries the database to restore it. A signed snapshot prevents a user from changing the identifier, but it does not prove that they are still allowed to access the model.

Livewire does not reuse the query that originally loaded the model. Its restoration query does not apply Filament resource or table queries, and Laravel does not apply the model's global scopes by default.

#### Re-querying models with resource or tenant context

On later requests, Filament restores these built-in properties using the context owned by their component:

- Resource page `$record`: the page's record resolver and resource route binding query, including nested parent scoping. These queries preserve intentional query customizations, such as allowing soft-deleted records.
- Nested resource page `$parentRecord`: the parent resource route binding queries. When ancestor route parameters are available, Filament validates the full ancestor chain. Without those parameters, Filament validates a supplied parent through its immediate parent resource's binding query and preserves its model identity; it does not reconstruct missing ancestors.
- Tenant profile `$tenant`: the panel's current tenant.

If the query can no longer find the model, Filament returns a 404 response. Any policy or component authorization check runs separately.

This protection does not follow a model into another Livewire component. A relation manager or widget cannot reliably reconstruct the resource query that originally loaded a model passed to it. Filament leaves these properties to Livewire's native restoration instead of applying a different query with all global scopes:

- Relation manager `$ownerRecord`.
- Widget `$record` and `$parentRecord`.
- Modal table select `$record`.
- Tenant registration `$tenant`.

These properties are not automatically re-queried against their original resource query or the model's global scopes. You must enforce any tenant, ownership, or other record-access restrictions before using them. A request to a child component does not run the parent page's record binding or authorization again.

`InteractsWithPageTable` is a specific exception when accessing table data: it knows the table's page class. For a resource page, it resolves the synthetic page's parent and checks ancestor resource access before booting or mounting that page, using the nested resource rules above. It does not check the parent record's `view()` or `update()` policy, or validate other uses of the widget's own model properties.

Table row actions resolve their record through the table query. Attach and associate actions resolve selected records through their configured relationship or options query. Filament does not provide either guarantee for model values passed to custom Livewire methods or action arguments.

<Aside variant="warning">
    Component authorization is separate from record scoping. A relation manager still runs `canViewForRecord()`, and a widget still runs `canView()`. Neither automatically reapplies the parent resource's query or authorizes a passed-in record. For a relation manager, enforce owner-record restrictions in `canViewForRecord()`. For widget or custom component code, query and authorize records before accessing them.
</Aside>

#### Protecting your own model properties

Filament does not re-query model values that belong to your application, including:

- model properties that you add to a custom Livewire component, custom page, or other Filament component,
- application-owned model properties on a Livewire component embedded inside a schema,
- model arguments passed to a lazy component's `mount()` method,
- models nested inside arrays, collections, form state, or other public properties, and
- model values passed through custom Livewire method or action arguments.

Do not assume that these models, or the passed-in built-in properties listed above, still pass the query or authorization rules that applied when they were first loaded. For your own properties, store a scalar key instead, then query and authorize the model when you need it. You may also re-query and authorize a restored model before using it.

For example, store a locked record ID and use a computed property to query and authorize the record when you access it. Include any tenant or ownership constraints your application requires in the query, and authorize the intended operation. This example checks `view` access:

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

When a relationship is queried, it uses its relationship query and the related model's global scopes. Accessing an already-loaded relationship does not run that query again. Filament does not automatically run a policy for every related model that your code accesses.

### Running code before authorization

On a later Livewire request:

1. Livewire restores public properties. This can run an unscoped model query, Eloquent retrieval events, and callbacks such as a Livewire form object's `boot()` method.
2. For resource pages and tenant profile pages, Filament restores the context-aware properties listed above. A resource-bound model that no longer passes its query is rejected before your component lifecycle hooks run. This step does not re-query passed-in properties on relation managers, widgets, or other child components.
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

This also applies to [Select option actions](../forms/select#authorizing-option-actions) enabled by `createOptionForm()` and `editOptionForm()`. Access to the parent resource does not authorize creating or editing related records. Authorize the option action itself so that unauthorized users cannot open its modal or submit it, and scope the related records separately.

### Testing authorization

Your application should have a comprehensive test suite that verifies authorization is enforced correctly across all entry points — not just Filament's resource pages, but also any custom actions, custom pages, Livewire components, API routes, and other functionality. Filament provides [testing helpers](../testing/overview) for asserting that actions, pages, and resources behave correctly for different user roles.

If a component keeps a model between requests, test what happens when its access changes. Mount the component, change the model's tenant, ownership, scope, or policy result, then send another request using the existing component. Assert that the requested update, action, or render is rejected. Test any authorization that protects earlier lifecycle code separately.

Do not rely solely on Filament's built-in policy checks. Treat them as a helpful layer, but always verify that your authorization rules are enforced end-to-end through testing.

## Validating user input

Many Filament configuration methods accept closures that can return dynamic values. Methods like `url()`, `icon()`, `html()`, and others are designed to be flexible, allowing developers to build rich, dynamic interfaces. However, when the values passed to these methods originate from user input or untrusted database content, it is your responsibility to validate and sanitize them appropriately.

For example, the `url()` method on columns, entries, and actions HTML-escapes the URL when rendering an `<a href="...">` tag, but does not restrict its scheme. The same applies to table [`recordUrl()`](../tables/overview#record-urls-clickable-rows) values and [Blade component](../components/link) `href` attributes. If you pass a URL sourced from user input without validation, a malicious value like `javascript:alert(document.cookie)` could be rendered as a clickable link, leading to XSS. Always validate that URLs use a safe scheme such as `http` or `https` before passing them to Filament.

Filament ships a `Str::sanitizeUrl()` helper that returns the URL when it is schemeless (relative) or uses the `http`/`https` scheme, and returns `null` for anything else. Before checking the scheme, it decodes HTML entities and rejects control characters and raw whitespace. The return value is the original input unchanged when it passes the check; the helper does not HTML-escape or rewrite the URL. Filament escapes the value when its configuration methods render it into an HTML attribute. If you render the value yourself, escape it as an HTML attribute, including its ampersands.

```php
use Filament\Tables\Columns\TextColumn;
use Illuminate\Support\Str;

TextColumn::make('website')
    ->url(fn (string $state): ?string => Str::sanitizeUrl($state))
```

Use the helper when providing an untrusted URL, including values read from a database. Filament does not call it automatically for `url()`, `recordUrl()`, or Blade component `href` attributes. `FileUpload` and `SpatieMediaLibraryFileUpload` already sanitize their preview, open, and download URLs; that does not authorize access to the underlying files.

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

The rich content renderer sanitizes editor-authored content, but treats HTML returned by [registered custom blocks](../forms/rich-editor#rendering-content-with-custom-blocks) as trusted application HTML. Neither the block's final output nor its [preview](../forms/rich-editor#rendering-a-preview-for-a-custom-block) is automatically sanitized. Your block methods must escape or sanitize user-supplied configuration values before including them in HTML.

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
