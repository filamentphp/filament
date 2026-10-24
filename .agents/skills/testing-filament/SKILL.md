---
name: testing-filament
description: Writes and runs Filament tests with Pest, Pest Browser, accessibility checks, and test databases. Use when planning verification, adding or changing tests, running tests, or validating asynchronous JavaScript changes in this repository.
---

# Testing Filament

Use the repository's existing tests and supported public behavior to verify changes.

## Choosing coverage

Introduce new tests only for actual frontend or backend behavior changes. Styling changes and bug fixes that restore existing intended behavior should not introduce new tests. Verify those fixes using existing tests and, for styling, visual inspection.

- Keep inexpensive non-browser coverage of supported configuration and rendering paths, including setters, getters, `Closure` evaluation, and successful rendering.
- For frontend behavior changes, add browser tests using Pest Browser with `visit()`.
- For asynchronous JavaScript changes, exercise out-of-order completion, failure/cancellation, and destruction with pending work. Use the real integration where mocked callbacks would hide its contract. This validation does not automatically require new permanent tests.

## Organizing tests

- Keep each test focused on one behavior and each test file focused on its owning component or feature. Separate scenarios only when combining them would obscure the behavior or introduce order-dependent state.
- Before inserting tests, read the file's existing organization. Place new cases beside related coverage, reusing an appropriate `describe()` group when present; add a focused group only when it improves organization. Keep basic rendering and common scenarios before specialized configuration and edge cases. Do not default to inserting tests immediately after `uses()` or at the top of the file.
- Before adding a browser test, search the file for the same route and setup. Combine related assertions into one coherently named test and reuse the page; use another visit only when isolation or a state reset is required.

## Testing browser behavior

- In browser tests, do not assert exact translated copy, CSS classes, inline styles, utility classes, incidental HTML, or computed visual styling unless that output proves the behavior under test.
- Prefer native Pest Browser / Playwright interactions and assertions with stable semantic selectors or `data-testid` hooks. Do not inspect framework/component internals, replace component methods, simulate private lifecycle calls, or synchronize on private markup such as NProgress. Use custom JavaScript only for public integration setup or observable facts that native APIs cannot reasonably express. Do not use presentation classes or visible copy merely as readiness checks.
- Wait for observable readiness or outcomes, not fixed delays. Server-rendered presence, default attributes, or enabled state do not necessarily establish readiness. Perform each interaction once; do not replay actions inside polling assertions or add whole-test retries to hide failures.
- If coverage cannot be made durable without implementation coupling, remove the assertion or scenario and its exclusive fixtures, and report the lost coverage. Do not force results, weaken meaningful outcomes, or add test-only production APIs to retain coverage numbers.
- Check representative stable states with `assertNoAccessibilityIssues()` in both light and dark modes, using the page returned by `inDarkMode()`. Report dependency-related accessibility limitations rather than suppressing violations or waiting for private framework markup.
- Run behavior scenarios once unless the theme changes the behavior. Keep light- and dark-mode accessibility checks together for representative distinct states (such as open popups, errors, and disabled controls), not every interaction or dataset permutation. Reuse existing coverage instead of repeating equivalent whole-document scans or setup.

## Running tests

Run commands from the repository root:

```bash
composer test              # Run all tests (SQLite + commands + PHPStan)
composer test:sqlite       # Run tests with SQLite
composer test:mysql        # Run tests with MySQL
composer test:pgsql        # Run tests with PostgreSQL
composer test:sqlsrv       # Run tests with SQL Server
composer test:phpstan      # Run PHPStan static analysis

# Run a single test file
vendor/bin/pest tests/src/Forms/Components/FileUploadTest.php

# Run a single test by name
vendor/bin/pest --filter="it can use \`aspectRatio\(\)\` to force image cropping"
```

### Starting test databases in Amp orbs

The demo uses SQLite. MySQL, PostgreSQL, and SQL Server do not start automatically. Before running their tests, run `.agents/database start mysql`, `.agents/database start pgsql`, or `.agents/database start sqlsrv`. Stop the database afterward using the corresponding `.agents/database stop` command. Database volumes persist across stops; stopping the last container also stops Docker.
