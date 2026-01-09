# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

Filament is a full-stack UI framework for Laravel built with Livewire. It provides admin panels, forms, tables, notifications, actions, infolists, and widgets as composable packages.

## Critical: Naming Conventions

### Variable Names

**Never use abbreviated variable names.** Use full descriptive names:

```php
// GOOD
$exception, $component, $response, $configuration, $record, $livewire

// BAD - never do this
$e, $comp, $res, $cfg, $rec, $lw
```

Only exception: universally understood abbreviations like `$id`, `$url`.

### Pest Test Names

**Always use backticks for code references. Add `()` for methods:**

```php
// GOOD
it('can use `aspectRatio()` to force image cropping')
it('returns `null` for `getImageCropAspectRatio()` by default')
it('validates `$record` is an instance of `Model`')

// BAD - missing backticks
it('can use aspectRatio to force image cropping')
it('returns null for getImageCropAspectRatio by default')
```

### Code Comments

**Use backticks when referencing code in comments:**

```php
// GOOD
// Uses `evaluate()` to resolve the `Closure`
// Returns `null` if the `$record` is not set

// BAD
// Uses evaluate() to resolve the Closure
```

## Development Commands

**Always update tests when making changes.** For UI components, add browser tests using Pest Browser with `visit()`. Always call `assertNoAccessibilityIssues()` in both light and dark modes (`->inDarkMode()`).

```bash
composer test              # Run all tests (SQLite + commands + PHPStan)
composer test:sqlite       # Run tests with SQLite
composer test:mysql        # Run tests with MySQL
composer test:pgsql        # Run tests with PostgreSQL
composer test:phpstan      # Run PHPStan static analysis
composer cs                # Run all code style fixes (Rector + Pint + Prettier)

npm run build              # Build all JS and CSS
npm run build-demo         # Build and publish to ../demo if it exists

# Run a single test file
vendor/bin/pest tests/src/Forms/Components/FileUploadTest.php

# Run a single test by name
vendor/bin/pest --filter="it can use \`aspectRatio\(\)\` to force image cropping"
```

## Coding Patterns

### Fluent API

Components use `make()` constructor and fluent chainable methods. Nullable properties have nullable setters so they can be undone:

```php
TextInput::make('name')
    ->label('Full name')
    ->icon('heroicon-o-user')

// Property and setter share the same name, nullable to allow unsetting
protected string | Closure | null $icon = null;

public function icon(string | Closure | null $icon): static
{
    $this->icon = $icon;

    return $this;
}

// Getter prefixed with `get`, uses `evaluate()` for `Closure` support
public function getIcon(): ?string
{
    return $this->evaluate($this->icon);
}
```

### Boolean Methods

```php
// Property - `is`/`should`/`can`/`has` prefix, defaults `false`, supports `Closure`
protected bool | Closure $isDisabled = false;

// Setter - verb form, defaults `true`, pass `false` to undo
public function disabled(bool | Closure $condition = true): static
{
    $this->isDisabled = $condition;

    return $this;
}

// Getter - cast to `bool`
public function isDisabled(): bool
{
    return (bool) $this->evaluate($this->isDisabled);
}
```

### Static Closures

Use `static fn` when the closure doesn't use `$this`:

```php
->placeholder(static fn (Select $component): ?string => $component->isDisabled() ? null : 'Select...')
->visible(fn (): bool => $this->canView()) // Uses `$this`, cannot be static
```

### Container Resolution

Use `app()` instead of `new` to allow users to bind custom implementations:

```php
app(RelationshipJoiner::class)->prepareQuery($relationship) // Good
(new RelationshipJoiner())->prepareQuery($relationship)     // Avoid
```

### Extensibility

Do not use `final` or `readonly` classes - users need to extend Filament classes.

### Concerns and Contracts

Traits in `Concerns/` directories: `Can*` (capabilities), `Has*` (properties).
Interfaces in `Contracts/` directories.

## Coding Standards

### PHPDoc

Only add when providing type info beyond native PHP types:

```php
/** @var array<string, array{label: string, icon: string}> */  // Good
/** @param string $name The name */                            // Redundant
```

### Deprecations

Keep old public methods used in docs, mark deprecated:

```php
/** @deprecated Use `newMethod()` instead. */
public function oldMethod(): void
{
    return $this->newMethod();
}
```

## Architecture

### Packages (`packages/`)

Core: **support** (base utilities) → **schemas** (UI layouts) → **forms**, **infolists**, **tables**, **actions**, **notifications**, **widgets** → **panels** (full admin framework)

Other: query-builder, upgrade, spatie-laravel-media-library-plugin, spatie-laravel-settings-plugin, spatie-laravel-tags-plugin, spatie-laravel-google-fonts-plugin, spark-billing-provider

### Key Classes

- **Resources** (`packages/panels/src/Resources/`): CRUD interfaces for Eloquent models
- **Pages** (`packages/panels/src/Pages/`): Livewire page components
- **Schema Components** (`packages/schemas/src/Components/`): Base UI components
- **Actions** (`packages/actions/src/`): Modal-based operations
- **Panel** (`packages/panels/src/Panel.php`): Admin panel configuration

### File Locations

- Tests: `tests/src/{Forms,Tables,Actions,Panels}/`
- Docs: `docs/` and `packages/{package}/docs/`
- Views: `packages/{package}/resources/views/`
- CSS: `packages/{package}/resources/css/`
- Translations: `packages/{package}/resources/lang/{locale}/`

### CSS Hook Classes

**Never use Tailwind classes directly in Blade views.** All Tailwind classes must be in CSS files using `@apply`:

```css
.fi-fo-field {
    @apply grid gap-y-2;
}
```

Hook class naming:
- Prefix: `fi-` with package codes (`fi-fo-` forms, `fi-ta-` tables, `fi-ac-` actions, etc.)
- Abbreviations: `btn`, `col`, `ctn`, `wrp`

## Writing Documentation

**Always update documentation for user-facing features** in `packages/{package}/docs/`.

- **Tone**: Direct, second person ("You may set...", "You can do this using...")
- **Structure**: Start with `## Introduction`, show simplest code first
- **Headings**: Use gerunds ("Setting the type" not "Type settings", "Enabling search" not "Search")
- **Formatting**: Backticks for code (`method()`, `ClassName`), include `use` statements
- **Asides**: `<Aside variant="tip|info|danger">...</Aside>`
