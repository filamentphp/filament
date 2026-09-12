# Framework field browser fixtures

React, Vue, and Svelte implement the same composite field and DOM structure. Keep their inputs, outputs, and utility actions equivalent so the Pest framework dataset tests the same contract in every renderer. These are test fixtures, not supported framework adapters.

From the repository root:

```sh
npm ci
npm run test:js
vendor/bin/pest -c phpunit.sqlite.xml tests/src/Forms/Components/JsFieldFrameworkTest.php
```

`npm run test:js` runs the Node tests, checks the TypeScript contract, and builds all three renderers from source with `tests/js/build-renderers.js`. Run it again after changing a renderer or its dependencies. CI runs it once before parallel Pest workers start, so workers never rebuild shared modules. Pest registers the generated entry modules, CSS, and hashed shared chunks through `FilamentAsset`, publishes them with `filament:assets`, and serves the fixture page through the core browser infrastructure. These browser tests belong to the `serial` group because asset publication writes to a shared public directory. Run them without `--parallel`; CI runs them after the parallel suite. No demo checkout, CDN, running Vite server, or committed framework bundles are required. Framework libraries and TypeScript are root development dependencies only; none are bundled into Filament's distributed assets.

The dataset covers composite JSON values, deferred/live/blur/debounce state, server reset, dynamic PHP props (including escaped values and cleared keys), relative and absolute schema utilities, component-local state, repeated nested plugin fields using `HasJsRenderer`, lazy module reuse, CSS/chunk loading, disposal/remount, disabled/read-only props, isolated initialization failures, and helper/error descriptions. Named exposed PHP methods, the general caller, renderless responses, unexposed-method rejection, and the original `$wire` proxy are exercised alongside ordinary Blade fields and Flex children. Every scenario runs separately in light and dark modes. Dedicated accessibility scenarios render only the fields under test, covering helper text, validation, both field wrappers, and initialization failures. Long multi-field behavioral scenarios do not repeat whole-page contrast scans, which proved intermittent in CI. The method, props, and descriptions scenarios also save comparable field screenshots under `tests/Browser/Screenshots/`.
