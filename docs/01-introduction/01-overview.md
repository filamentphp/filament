---
title: What is Filament?
contents: false
---

## Introduction

**Filament is an open-source framework for building polished, maintainable business software with Laravel.**

You can start with a simple admin panel or build a CRM, customer portal, or school management system. Business software isn't limited to commercial businesses: companies, charities, schools, governments, and other institutions all need systems for managing records, coordinating work, and serving people.

Filament brings coherent conventions, reusable components, and polished interfaces to applications with many screens, permissions, and workflows. These shared building blocks give your team less bespoke infrastructure to own as requirements change and new contributors join the project.

Built on Livewire, Alpine.js, and Tailwind CSS, Filament uses a Server-Driven UI (SDUI) approach: you define interfaces in PHP using structured configuration objects rather than writing every view by hand. These components connect interfaces to application behavior, from validating input and saving records to running actions. You can build many features without custom JavaScript, while retaining the ability to extend the frontend when you need to.

<Disclosure>
    <span slot="summary">What is Server-Driven UI?</span>

    SDUI is a proven architecture used by companies like Meta, Airbnb, and Shopify. It moves control of the UI to the server, allowing for faster iteration, greater consistency, and centralized logic. Filament embraces this pattern for web development, letting you define interfaces declaratively using PHP classes that are rendered into HTML by the server.

    One key distinction to note is the difference between Server-Driven UI (SDUI) and Server-Rendered UI. While both approaches involve rendering content on the server, Server-Rendered UI relies on static templates (like traditional Blade views), where the structure and behavior of the UI are defined upfront in HTML or PHP files. In contrast, SDUI gives the server the power to dynamically generate the UI based on real-time configurations and business logic, allowing for more flexibility and reactivity without needing to modify frontend templates directly.
</Disclosure>

Use the panel builder to bring resources, custom pages, dashboards, and authentication together in an application. You can create multiple panels for different parts of the same system, such as an internal admin panel and a customer portal.

If you're already using Blade views in your Laravel app, Filament components can enhance them too. You can drop Livewire components powered by Filament into any Blade view or route, and use the same schema-based builders for forms, tables, and more, all without switching your entire stack.

## Packages

The core of Filament comprises several packages:

- `filament/filament` - The core package for building panels (e.g., admin panels). This requires all other packages since panels often use many of their features.
- `filament/tables` - A data table builder that allows you to render an interactive table with filtering, sorting, pagination, and more.
- `filament/schemas` - A package that allows you to build UIs using an array of "component" PHP objects as configuration. This is used by many features in Filament to render UI. The package includes a base set of components that allow you to render content.
- `filament/forms` - A set of `filament/schemas` components for a large variety of form inputs (fields), complete with integrated validation.
- `filament/infolists` - A set of `filament/schemas` components for rendering "description lists". An infolist consists of "entries", which are key-value UI elements that can present read-only information like text, icons, and images. The data for an infolist can be sourced from anywhere but commonly comes from an individual Eloquent record.
- `filament/actions` - Action objects encapsulate the UI for a button, an interactive modal window that can be opened by the button, and the logic that should be executed when the modal window is submitted. They can be used anywhere in the UI and are commonly used to perform one-time actions like deleting a record, sending an email, or updating data in the database based on modal form input.
- `filament/notifications` - An easy way to send notifications to users in your app's UI. You can send a "flash" notification that appears immediately after a request to the server, a "database" notification which is stored in the database and rendered in a slide-over modal the user can open on demand, or a "broadcast" notification that is sent to the user in real-time over a websocket connection.
- `filament/widgets` - A set of dashboard "widgets" that can render anything, often statistical data. Charts, numbers, tables, and completely custom widgets can be rendered in a dashboard using this package.
- `filament/support` - This package contains a set of shared UI components and utilities used by all other packages. Users don't commonly install this directly, as it is a dependency of all other packages.

## Plugins

Filament is designed to be highly extensible, allowing you to add your own UI components and features to the framework. These extensions can live inside your codebase if they're specific to your application, or be distributed as Composer packages if they're general-purpose. In the Filament ecosystem, these Composer packages are called "plugins", and hundreds are available from the community. The Filament team also officially maintains several plugins that provide integration with popular third-party packages in the Laravel ecosystem.

The vast majority of plugins in the ecosystem are open-source and free to use. Some premium plugins are available for purchase, often offering enhanced customer support and quality.

<Aside variant="warning">
    Plugins not maintained by the Filament team are created and managed by independent authors. While these plugins can enhance your experience, Filament cannot guarantee their quality, security, compatibility, or maintenance. We recommend reviewing the plugin's code, documentation, and user feedback before installation.
</Aside>

You can browse an extensive list of official and community plugins on the [Filament website](https://filamentphp.com/plugins).

## Customizing the appearance

Tailwind CSS is a utility-based CSS framework that Filament uses as a token-based design system. Although Filament does not use Tailwind CSS utility classes directly in the HTML rendered by its components, it compiles Tailwind utilities into semantic CSS. These semantic classes can be targeted by Filament users with their own CSS to modify the appearance of components, creating a thin layer of overrides on top of the default Filament design.

A simple example demonstrating the power of this system is changing the border radius of all button components in Filament. By default, the following CSS code is used in the Filament codebase to style buttons using Tailwind utility classes:

```css
.fi-btn {
    @apply rounded-lg px-3 py-2 text-sm font-medium outline-none;
}
```

To decrease the [border radius in Tailwind CSS](https://tailwindcss.com/docs/border-radius), you can apply the `rounded-sm` (small) utility class to `.fi-btn` in your own CSS file:

```css
.fi-btn {
    @apply rounded-sm;
}
```

This overrides the default `rounded-lg` class with `rounded-sm` for all buttons in Filament while preserving the other styling properties of the button. This system provides a high level of flexibility to customize the appearance of Filament components without needing to write a complete custom stylesheet or maintain copies of HTML for each component.

For more information about customizing the appearance of Filament, visit the [Customizing styling documentation](../styling).

## Testing

The core packages in Filament undergo unit testing to ensure stability across releases. As a Filament user, you can write tests for applications built with the framework. Filament provides utilities for testing both functionality and UI components, compatible with either Pest or PHPUnit test suites. Testing is particularly crucial when customizing the framework or implementing custom functionality, though it's also valuable for verifying basic functionality works as intended.

For more information about testing Filament applications, visit the [Testing documentation](../testing).

## When Filament is a good fit

Filament is a good fit for operational, data-rich applications where forms, tables, dashboards, and workflows benefit from shared components and conventions. You can use it for a single admin panel or a system with several panels serving different groups of users.

You configure most built-in features in PHP. Familiarity with Laravel and Livewire is useful when extending those features or building custom components, while custom frontend behavior may also require JavaScript and CSS.

## Considering alternatives

Choose tools based on the kind of software you're building, your preferred UI architecture, and the parts of the application you want to implement yourself. Other projects in the Laravel ecosystem offer different approaches:

### Building an admin panel with Vue.js and Inertia.js

[Laravel Nova](https://nova.laravel.com) is an admin panel built with Vue.js and Inertia.js and maintained by the Laravel team. Consider it if you prefer to use those tools when building custom components for your admin panel.

Purchasing Nova helps support the development of the Laravel framework.

### Managing content with a CMS

[Statamic](https://statamic.com) is a CMS built on Laravel. Consider it when you need a content management system rather than building your own content management features.

### Writing interfaces directly in Blade

[Flux](https://fluxui.dev) is the official Livewire UI kit, with pre-built and pre-styled Blade components. Consider it when you want to compose your interfaces directly in Blade and implement the application behavior yourself. You can also use [individual Filament components](../components) within an existing Blade application without adopting the panel builder.

Flux is maintained by the same team that maintains Livewire and Alpine.js, and purchasing it helps support the development of these projects.
