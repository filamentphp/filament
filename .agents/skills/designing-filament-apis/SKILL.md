---
name: designing-filament-apis
description: Designs and audits consistent Filament public APIs and compatibility-preserving deprecations. Use when adding or renaming public or protected PHP APIs, named arguments, documented frontend contracts, or upgrade migrations.
---

# Designing Filament APIs

Choose names from the closest existing API with the same meaning, and preserve the contracts that developers already use.

## Establishing the vocabulary

Before proposing a name, inspect its implementation, callers, documentation, parallel components and existing deprecations. Search `packages/*/src` and the shipped `packages/*/.stubs.php` IDE declarations. Testing macros expose parameters on their returned closures; the outer method signature alone does not describe the public API.

Compare equivalent behavior across forms, tables, infolists, schemas and actions. A matching word is not enough: `record`, `model`, `state`, `data`, `schema`, `form`, `query` and `relationship` describe different contracts.

Use these established conventions:

| Surface               | Convention                                                                                                                             | Important boundary                                                                                                                                                                                                    |
| --------------------- | -------------------------------------------------------------------------------------------------------------------------------------- | --------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------- |
| Value configuration   | `icon($icon)`, `$icon`, `getIcon()`                                                                                                    | Keep the same noun across setter, storage and getter unless the getter intentionally returns a transformed value.                                                                                                     |
| Boolean configuration | `disabled($condition = true)`, `$isDisabled`, `isDisabled()`                                                                           | Prefer a positive, reversible configuration API over `enable*`, `disable*` or `without*` aliases. Actual commands may still use verbs.                                                                                |
| Boolean meaning       | `is*` for state, `has*` for possession, `can*` for capability, `should*` for policy                                                    | Pick the grammar that describes the value; do not mechanically convert every predicate to `is*`.                                                                                                                      |
| Conditional values    | `Closure` plus `evaluate()` where the owning API supports it                                                                           | Preserve null/unset/inherited semantics. Nullable setters can undo nullable configuration.                                                                                                                            |
| Named callbacks       | Follow equivalent `*Using`, `modify*Using`, `mutate*Using`, `before*` and `after*` APIs                                                | Distinguish resolving a value, transforming a supplied object, mutating data and lifecycle callbacks.                                                                                                                 |
| Reusable concerns     | `Has*` for properties and `Can*` for capabilities                                                                                      | `InteractsWith*` expresses integration. Traits and interfaces need not share a prefix just because they are related.                                                                                                  |
| Composition           | `schema` / `component` for generic schema infrastructure                                                                               | `form` remains correct for an actual input form, validation, HTML form wrapper or domain-specific form method.                                                                                                        |
| Actions               | Name the location or role when ambiguous: `recordActions`, `toolbarActions`, `modalFooterActions`                                      | An action is not merely its button rendering; avoid reintroducing old `*ButtonLabel` names for configurable actions.                                                                                                  |
| Text                  | `label`, `title`, `heading`, `description`, `body`, `placeholder` according to the actual role                                         | Notification titles replaced old notification-message APIs; that does not make every message a title.                                                                                                                 |
| Cardinality           | Singular for one item, plural for a collection                                                                                         | Use the semantic result, not merely an array type: one configuration can itself be an array.                                                                                                                          |
| Identifiers           | Descriptive parameter/property names; established `id`, `url`, `html`, `js` terminology                                                | Do not invent abbreviations, or expand well-established abbreviations in one API while peers retain them.                                                                                                             |
| Classes and values    | PascalCase classes/traits/interfaces/enum cases; uppercase hook/alias constants; camelCase methods/properties; snake_case free helpers | Color palette constants use PascalCase, as do enum compatibility aliases. Preserve these established families rather than enforcing one casing on every constant. Backed values are a separate compatibility surface. |

Keep Laravel, Livewire, PHP magic-method, package interface and third-party callback names when those systems define the contract. Physical `left`/`right` alignment is not synonymous with logical `start`/`end` in RTL. Do not rename distinct behavior merely to make spellings match.

### Maintainer-selected naming policies

Apply these policies to new APIs and planned migrations, not as permission to break existing supported APIs:

- Use `Schema` for component definitions, including import/export options, Select option definitions and MFA challenges. Keep `Form` for actual HTML submission and form-input operations.
- Include `Interval` in a polling setter name when its interval argument is required; omit it when the interval is optional. Nullable does not mean optional: an argument without a default is required.
- Name fluent configuration setters for what they configure: `activeWhen()` rather than the predicate-shaped `isActiveWhen()`, and `guesses()` for import-column candidate names. Preserve the distinction between configuration presence and evaluated state.
- Use unabbreviated PHP configuration parameters (`$configuration`) and `Integer` in descriptive names. Keep language types such as `int` and persisted/JavaScript payload keys unchanged.
- Use `RollBack` consistently within transaction-control method, property and parameter names. Case-only methods cannot coexist as aliases; coordinate parameter/property changes in a major release.
- Use `cached…` for scalar memoized properties. Keep map-like cache-store names such as `$methodCache`; preserve cache invalidation and null/uninitialized sentinels.
- Prefer `Count` for item-limit traits and `LazyLoaded` for table-loading API families. A trait name should describe the same capability as its fluent API without conflating distinct owners.
- Name operations for their actual result: `saveEmailAuthentication($condition)` persists a requested state rather than toggling it; `recordAttributesToArray()` preserves serialization/casting rather than returning raw Eloquent attributes.
- Keep meaningful conveniences and established vocabulary: `Color::hex()` / `rgb()` and Resource `getRelations()` remain intentional. Removing the `reactive()` synonym in favor of `live()` is a specific migration decision, not a rule to deprecate every convenience wrapper; `debounce()` is outside it.
- Prefer `$search` for search text and `$query` for a Builder in new callback guidance. Preserve all existing named-injection aliases, meanings and lookup precedence; this policy does not schedule their removal in v6.
- Prefer the package-plural `schemas::` icon namespace for its planned canonical spellings. Existing literal keys still require explicit compatibility and precedence handling; a naming decision alone does not authorize removal of old keys.

## Planning a deprecation

Record the exact old symbol, proposed replacement, evidence from equivalent APIs, all affected declarations, release boundary and compatibility strategy. Separate recommendations from preferences that need a maintainer decision. An existing `@deprecated` is already a migration, not a new rename candidate.

### Additive changes in supported majors

- Keep the old entry point and its argument names/defaults; add the new entry point and mark the old one with ``@deprecated Use `replacement()` instead.``. Always put the deprecation annotation on its own line in a multiline docblock, with the opening and closing delimiters on separate lines:

  ```php
  /**
   * @deprecated Use `replacement()` instead.
   */
  ```

- Classify the method's intended role before choosing a bridge direction. Use documentation, framework dispatch and established usage; being technically overridable, public or protected does not by itself establish a supported extension hook. Absence from documentation alone does not prove a hook unsupported.
- For ordinary setters, getters and configuration APIs intended to be called, keep the implementation in the new canonical method and make the old deprecated method forward to it (`OLD → NEW`).
- For supported subclass extension hooks, use `NEW → OLD` when necessary to preserve existing overrides through supported majors: the new method calls the old virtual method, and framework entry points call the new method. Trace getter/output pipelines, recursive dispatch and `parent::` calls so both old and new overrides work. Do not apply this exception to ordinary APIs merely because PHP permits overriding them.
- Preserve existing parameter names/defaults and authoritative backing properties regardless of bridge direction. Never create reciprocal old/new wrappers.
- Preserve protected/static property configuration and direct subclass writes. Two independent properties are not aliases. Retain one authoritative storage location or define explicit new-versus-legacy precedence that distinguishes unset from `false` and meaningful `null`.
- Check traits, interfaces, subclass type relationships, service-container bindings, attributes and serialization before renaming a class. A subclass shim and `class_alias()` have different identity and inheritance behavior.
- Keep enum backed values, render-hook strings, event names and payload keys stable when adding a new PHP symbol. A renamed constant does not require renaming its string value.
- Keep explicit exposure/security attributes and frontend callability on compatibility endpoints where that is part of their contract. Sharing an attribute name does not imply every dispatcher has the same inheritance policy.
- Keep public docs, IDE stubs and necessary upgrade recipes consistent with the canonical API. Do not document deprecated methods or mention deprecated APIs or compatibility aliases in developer documentation unless doing so is absolutely necessary to use the feature. Deprecation alone does not justify a migration note or a list of old names. Keep required `@deprecated` annotations in source and IDE declarations; point replacement advice to the current canonical API, not another deprecated alias.

### Changes requiring a major version

- Rename existing PHP parameters only in a major version: named arguments are public API. Check overriding declarations, interfaces, factories, returned testing closures, documentation and stubs together.
- Treat named closure-injection keys as API. They can sometimes gain an alias in a minor release, but removing an old injection name is breaking, and conflicting keys need a defined resolution policy.
- Schedule property removals, changed interface requirements, incompatible class identity changes, event/payload changes, CSS-hook removals and serialized-value changes separately from adding replacement names.
- Case-only PHP method renames cannot have two separately declared alias methods. Check reflection, generated frontend calls and case-sensitive external consumers before deciding the release boundary.
- Do not remove every `@deprecated` mechanically. Some deprecated members still hold state or implement a fallback. Preserve behavior in the surviving implementation and distinguish methods/classes already removed on the target branch.
- Scope automated upgrade rules by receiver type. Explain manual migration boundaries for dynamic method names, string references, named arguments, subclasses and custom dispatch.

## Reviewing documented frontend names

Start with developer-facing documentation and trace each contract to its registration and implementation. Include JS globals/exports, Alpine registrations, exposed Livewire/schema/column methods, events and payloads, Blade props/slots, CSS hooks/custom properties, asset IDs and documented file entry points. Do not turn this into an audit of private minified internals.

Follow the owning package's registration conventions. JS source files generally use kebab-case and explicit `.js` imports; component-local naming is not automatically a public contract. Keep `fi-` CSS hooks and package prefixes, with established abbreviations such as `btn`, `col`, `ctn` and `wrp`. Check supported custom views and themes before removing selectors or renaming published assets.

## Proving coverage and compatibility

For a project-wide sweep, build or reuse a PHP AST inventory of the scoped declarations. Record a per-file review ledger with `path`, SHA-256 `sha256`, `status: reviewed`, meaningful `notes` and finding references. Validate complete path coverage, source hashes, duplicate entries and review status. A signature search is not a whole-file review; inventory coverage is not proof that no naming issue remains.

Pin the source revisions. Review v5-only additions separately from shared v4 APIs and compare existing v6 removals before creating a backlog. Use an independent omission scan and skeptical review of proposed renames; record rejected findings and why.

Load `testing-filament` before verification. Check old and new calls, named arguments, supported overrides, legacy/new property precedence and relevant framework dispatch. For frontend changes, exercise the real documented integration. Follow the repository's existing test-retention rules rather than adding one test per alias. An audit by itself does not authorize implementation, pushing, releases or changes to shared state.
