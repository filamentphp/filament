---
title: Style customization
---

## Overview

Filament uses CSS "hook" classes to allow various HTML elements to be customized using CSS.

## Discovering hook classes

We could document all the hook classes across the entire Filament UI, but that would be a lot of work, and probably not very useful to you. Instead, we recommend using your browser's developer tools to inspect the elements you want to customize, and then use the hook classes to target those elements.

All hook classes are prefixed with `fi-`, which is a great way to identify them. They are usually right at the start of the class list, so they are easy to find, but sometimes they may fall further down the list if we have to apply them conditionally with JavaScript or Blade.

If you don't find a hook class you're looking for, try not to hack around it, as it might expose your styling customizations to breaking changes in future releases. Instead, please open a pull request to add the hook class you need. We can help you maintain naming consistency. You probably don't even need to pull down the Filament repository locally for these pull requests, as you can just edit the Blade files directly on GitHub.

## Applying styles to hook classes

For example, if you want to customize the color of the sidebar, you can inspect the sidebar element in your browser's developer tools, see that it uses the `fi-sidebar`, and then add CSS to your app like this:

```css
.fi-sidebar {
    background-color: #fafafa;
}
```

Alternatively, since Filament is built upon Tailwind CSS, you can use their `@apply` directive to apply Tailwind classes to Filament elements:

```css
.fi-sidebar {
    @apply bg-gray-50 dark:bg-gray-950;
}
```

Occasionally, you may need to use the `!important` modifier to override existing styles, but please use this sparingly, as it can make your styles difficult to maintain:

```css
.fi-sidebar {
    @apply bg-gray-50 dark:bg-gray-950 !important;
}
```

You can even apply `!important` to only specific Tailwind classes, which is a little less intrusive, by prefixing the class name with `!`:

```css
.fi-sidebar {
    @apply !bg-gray-50 dark:!bg-gray-950;
}
```

## Common hook class abbreviations

We use a few common abbreviations in our hook classes to keep them short and readable:

- `fi` is short for "Filament"
- `fi-ac` is used to represent classes used in the Actions package
- `fi-fo` is used to represent classes used in the Form Builder package
- `fi-in` is used to represent classes used in the Infolist Builder package
- `fi-no` is used to represent classes used in the Notifications package
- `fi-ta` is used to represent classes used in the Table Builder package
- `fi-wi` is used to represent classes used in the Widgets package
- `btn` is short for "button"
- `col` is short for "column"
- `ctn` is short for "container"
- `wrp` is short for "wrapper"

## Publishing Blade views

You may be tempted to publish the internal Blade views to your application so that you can customize them. We don't recommend this, as it will introduce breaking changes into your application in future updates. Please use the [CSS hook classes](#applying-styles-to-hook-classes) wherever possible.

If you do decide to publish the Blade views, please lock all Filament packages to a specific version in your `composer.json` file, and then update Filament manually by bumping this number, testing your entire application after each update. This will help you identify breaking changes safely.
