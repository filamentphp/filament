---
name: capturing-filament-screenshots
description: Creates and regenerates Filament documentation screenshots using the docs demo app and screenshot schema. Use when adding screenshot examples, changing screenshot definitions, or capturing images under docs-assets/screenshots/.
---

# Capturing Filament Screenshots

Screenshots are in `docs-assets/screenshots/`. All paths below are relative to the repository root unless stated otherwise.

## Adding examples

Add component examples to the appropriate Livewire component in `docs-assets/app/app/Livewire/` (e.g., `Schemas/LayoutDemo.php`). Give each example a unique `->id()` for the selector:

```php
Group::make()
    ->id('myComponent')
    ->extraAttributes(['class' => 'p-16 max-w-2xl'])
    ->schema([
        // Your component here
    ]),
```

Use natural, realistic content, not test-like examples.

Add screenshot definitions to `docs-assets/screenshots/schema.js`:

```js
'schemas/layout/my-component/simple': {
    url: 'schemas/layout',
    selector: '#myComponent',
    viewport: { width: 1920, height: 640, deviceScaleFactor: 3 },
},
```

## Building assets

If you changed any CSS or JS files, run both builds in order. The repo root compiles each package's dist output, and the docs app has its own Vite build that bundles those outputs into `docs-assets/app/public/build/`. Skipping the second step leaves the docs app serving stale CSS, and screenshots will render against pre-change styles.

```bash
# From the repository root: compile package dist output
npm run build

# From docs-assets/app: bundle the docs app's CSS from the package output
npm run build
```

If you also changed the Livewire demo or Blade views, run `php artisan optimize:clear` from `docs-assets/app` afterwards.

## Preparing the app

Set up the disposable docs app database first. Run `php artisan migrate:fresh --seed && php artisan storage:link` from `docs-assets/app`. This resets that app's database; do not point it at a database containing non-disposable data. Seeding also copies the file upload demos' sample images from `database/seed-images/` to the `public` disk.

## Generating screenshots

Run from `docs-assets/screenshots`:

```bash
export PUPPETEER_SKIP_CHROMIUM_DOWNLOAD=true
export PUPPETEER_EXECUTABLE_PATH=$(which chromium)
node script.js --parallel "tables/*" # Add =N to --parallel for a specific worker count
```

`--parallel` starts its own servers on ports 8001+ (no `php artisan serve` needed), each with its own copy of the seeded database, because many demos mutate the database on mount and would corrupt each other's screenshots if they shared one. The pristine database is restored before every page load, so each screenshot always sees freshly seeded data. This makes parallel mode the most reliable way to run large batches. Screenshots using the `configure` option run serially after the parallel pool because they mutate a PHP file shared by every server.

For serial capture, start the app server from `docs-assets/app` on the default port 8000. In Amp orbs, use a supervised service:

```bash
amp orb service start filament-docs --port 8000 --command 'php artisan serve --host=0.0.0.0 --port=8000'
```

Then run from `docs-assets/screenshots`, with the same Puppeteer environment variables:

```bash
node script.js "schemas/layout/my-component/*"
```

The serial script expects `http://127.0.0.1:8000`. Don't use a custom port. In serial mode, a demo that truncates tables can cause later entries, e.g. tenancy or resource pages, to return 404 until you reseed.

Schema entries without a `before` callback that share the same URL, viewport, and theme are captured from a single page load, and one browser process is shared across the whole run. If an entry needs an isolated page load (e.g. its demo mutates state when rendered), give it a `before` callback.

Pages are captured with `prefers-reduced-motion: reduce` and with CSS animations, transitions, and the input caret disabled, so screenshots never depend on which animation frame they caught (e.g. spinning loading indicators, modal fade-ins, caret blinking).

## Using screenshots in docs

Screenshots are generated in `images/light/` and `images/dark/` under `docs-assets/screenshots/`. Inspect the generated images in both themes before using them.

```mdx
<AutoScreenshot name="schemas/layout/my-component/simple" alt="Description" />
```
