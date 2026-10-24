---
name: writing-filament-docs
description: Writes and updates Filament developer documentation with the repository's structure, examples, and safety guidance. Use when adding, editing, or reviewing documentation under docs/ or packages/*/docs/.
---

# Writing Filament Documentation

Document how developers configure, extend, and troubleshoot Filament, not how end users operate it.

## Deciding what to document

Update documentation for developer-facing features and changes to supported usage in the owning page under `docs/` or `packages/{package}/docs/`. Do not add documentation merely because a change is visible to end users. Automatic styling, layout, and interaction changes (such as dropdown closing or focus restoration), fixes that restore intended behavior, and implementation details need documentation only when they change how developers configure, use, extend, or integrate Filament, or introduce consequential compatibility or accessibility limitations. A change to default end-user behavior alone is not a change to supported developer usage and does not need documentation.

## Structuring the page

- Start with `## Introduction`, briefly explain the purpose, and show the simplest useful example. Defer optional configuration and specialized integration details to later sections. Split distinct tasks into short, focused subsections rather than collecting configuration, authorization, scoping, and data-handling guidance in one long section.
- Review the full page and analogous pages, then place sections by reader importance and logical flow, not by when they were added. Do not default new sections to the top.
- Keep foundational and common workflows before specialized content, and nest narrower topics under their parent section. Place consequential warnings beside the first affected recipe, even when the full explanation belongs later.

## Writing accurate guidance

- Use direct, second-person language ("You may set...", "You can do this using...").
- Explain built-in interactions or internal behavior only when they affect a developer's decision, integration, or consequential limitation.
- State what Filament provides, where that protection or support stops, and what the application developer must implement. Preserve meaningful security, accessibility, compatibility, and data-loss guidance; do not replace precise boundaries with broad assurances.
- Keep examples focused on the API being taught. Do not add application-specific authorization, re-querying, or defensive boilerplate to unrelated recipes; explain those responsibilities in an adjacent warning and link to the dedicated guidance. Examples must still demonstrate correct API usage and must not imply protections Filament does not provide.
- Check behavioral claims and examples against the current branch's implementation. Explain shared contracts in their owning section and link from contextual recipes, retaining any caveat needed to use that recipe safely.

## Formatting

- Use gerunds for headings ("Setting the type" not "Type settings", "Enabling search" not "Search").
- Use backticks for code (`method()`, `ClassName`), and include `use` statements in examples.
- Use `<Aside variant="tip|info|danger">...</Aside>` for asides.

For documentation screenshots, also load `capturing-filament-screenshots` from `.agents/skills/capturing-filament-screenshots/SKILL.md`.
