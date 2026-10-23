# AGENTS.md

Filament is a full-stack UI framework for Laravel built with Livewire. It provides admin panels, forms, tables, notifications, actions, infolists, and widgets as composable packages.

## Task-specific Skills

Load the relevant skill before doing its work. If your agent cannot load skills, read the linked `SKILL.md` directly.

- **Testing**: Load [testing-filament](.agents/skills/testing-filament/SKILL.md) when planning verification, adding or changing tests, running tests, or validating asynchronous JavaScript changes. It covers Pest, browser testing, accessibility, and test databases.
- **Documentation**: Load [writing-filament-docs](.agents/skills/writing-filament-docs/SKILL.md) when adding, editing, or reviewing developer documentation.
- **Documentation screenshots**: Load [capturing-filament-screenshots](.agents/skills/capturing-filament-screenshots/SKILL.md) when adding screenshot examples, changing screenshot definitions, or capturing documentation screenshots.

## Testing and Documentation Requirements

**Introduce new tests only for actual frontend or backend behavior changes.** Styling changes and bug fixes that restore existing intended behavior should not introduce new tests. Verify those fixes using existing tests and, for styling, visual inspection. For frontend behavior changes, add browser tests using Pest Browser. Load `testing-filament` before planning or performing verification.

**Update documentation for developer-facing features and changes to supported usage** in the owning page under `docs/` or `packages/{package}/docs/`. Automatic styling, layout, interaction changes, fixes restoring intended behavior, and implementation details need documentation only when they change how developers configure, use, extend, or integrate Filament, or introduce consequential compatibility or accessibility limitations. A change visible to end users alone does not require documentation.

## Naming Conventions

- **Never use abbreviated variable names.** Use `$exception`, `$component`, `$response`, `$configuration`, `$record`, and `$livewire`, not `$e`, `$comp`, `$res`, `$cfg`, `$rec`, or `$lw`. Universally understood abbreviations like `$id` and `$url` are allowed.
- Use backticks for code references in Pest test names, with `()` for methods: `` it('can use `aspectRatio()` to force image cropping') ``.
- Use backticks for code references in comments: `` // Uses `evaluate()` to resolve the `Closure` ``.

## Development Commands

Run from the repository root:

```bash
composer test              # Run all tests (SQLite + commands + PHPStan)
composer test:sqlite       # Run tests with SQLite
composer test:phpstan      # Run PHPStan static analysis
composer cs                # Run all code style fixes (Rector + Pint + Prettier)

npm run build              # Build all JS and CSS
npm run build-demo         # Build and publish to ../demo if it exists

vendor/bin/pest tests/src/Forms/Components/FileUploadTest.php
```

Database-specific commands and startup instructions are in `testing-filament`. The demo uses SQLite.

## Coding Patterns

### Fluent API

Components use `make()` and fluent chainable methods. Nullable properties have nullable setters so they can be undone. Properties and setters share a name; getters use `get` and `evaluate()` for `Closure` support:

```php
protected string | Closure | null $icon = null;

public function icon(string | Closure | null $icon): static
{
    $this->icon = $icon;

    return $this;
}

public function getIcon(): ?string
{
    return $this->evaluate($this->icon);
}
```

### Boolean Methods

Boolean properties use an `is`/`should`/`can`/`has` prefix, default to `false`, and support `Closure`. Setters use the verb form, default their argument to `true`, and accept `false` to undo. Getters cast to `bool`:

```php
protected bool | Closure $isDisabled = false;

public function disabled(bool | Closure $condition = true): static
{
    $this->isDisabled = $condition;

    return $this;
}

public function isDisabled(): bool
{
    return (bool) $this->evaluate($this->isDisabled);
}
```

### Other PHP Conventions

- Use `static fn` when the closure doesn't use `$this`.
- Use `app()` instead of `new` to allow users to bind custom implementations.
- Do not use `final` or `readonly` classes; users need to extend Filament classes.
- Traits in `Concerns/` use `Can*` for capabilities and `Has*` for properties. Interfaces belong in `Contracts/`.
- Add PHPDoc only when providing type information beyond native PHP types.
- Keep old public methods used in docs and mark them with `` /** @deprecated Use `newMethod()` instead. */ ``.

## JavaScript

### Organization

Use `components/` for eager and lazy Alpine factories, `stores/` for Alpine stores, `utilities/` for shared helpers and composition, and `components/<feature>/` for feature-private internals. Keep registration in `index.js`. Use kebab-case filenames and explicit `.js` extensions on relative JavaScript imports. Follow the owning package's frontend naming convention; check registration and markup usage before renaming existing names.

### Lifecycle Cleanup

- Alpine components and directives must release resources they own in `destroy()` or `cleanup()`, including listeners on surviving ancestors or global objects, observers, animation loops, and third-party instances. Retain the same callback reference when adding and removing listeners. Listeners attached only to removed subtree elements do not need separate cleanup.
- Guard asynchronous initialization and callbacks when they could create surviving resources or cause observable stale behavior after destruction. After asynchronous work, verify the owner and target are still current; saved document positions alone do not establish identity. Verify third-party completion, failure, cancellation, and teardown contracts against their implementation when necessary, and use their real teardown API.
- Balance processing-start/finish signals exactly once per operation, including failure, cancellation, and destruction. For batches, restore busy/editable state only after every task settles; preserve the original state.
- Do not add cleanup solely for short-lived timers; cancel or guard them when their callback could still cause stale behavior after destruction.

## CSS Hook Classes

**Never use Tailwind classes directly in Blade views.** Put them in CSS files using `@apply`:

```css
.fi-fo-field {
    @apply grid gap-y-2;
}
```

Use the `fi-` prefix with package codes (`fi-fo-` forms, `fi-ta-` tables, `fi-ac-` actions, etc.). Hook abbreviations include `btn`, `col`, `ctn`, and `wrp`.

## Repository Map

Core dependency direction: **support** → **schemas** → **forms**, **infolists**, **tables**, **actions**, **notifications**, **widgets** → **panels**.

Other packages include query-builder, upgrade, spatie-laravel-media-library-plugin, spatie-laravel-settings-plugin, spatie-laravel-tags-plugin, spatie-laravel-google-fonts-plugin, and spark-billing-provider.

- **Resources**: `packages/panels/src/Resources/` (Eloquent CRUD interfaces)
- **Pages**: `packages/panels/src/Pages/` (Livewire page components)
- **Schema components**: `packages/schemas/src/Components/`
- **Actions**: `packages/actions/src/` (modal-based operations)
- **Panel configuration**: `packages/panels/src/Panel.php`
- **Tests**: `tests/src/{Forms,Tables,Actions,Panels}/`
- **Docs**: `docs/` and `packages/{package}/docs/`
- **Views**: `packages/{package}/resources/views/`
- **CSS**: `packages/{package}/resources/css/`
- **Translations**: `packages/{package}/resources/lang/{locale}/`
