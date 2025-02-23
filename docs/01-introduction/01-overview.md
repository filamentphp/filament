---
title: What is Filament?
contents: false
---

Filament is a meta-framework for Laravel that provides a set of components and conventions to simplify building frontend interfaces using Livewire, Alpine.js, and Tailwind CSS. It's designed to be flexible and extensible, allowing you to use as much or as little of it as needed while customizing it to fit your requirements.

Thousands of developers choose Filament to add an admin panel interface to their applications. You can implement this regardless of your app's frontend technology stack - it's commonly paired with tools like Inertia.js that integrate Laravel with full frontend frameworks such as Vue.js, React, or Svelte.

When building your app's frontend with Blade (whether using Livewire or not), you can enhance your views with framework components to improve development efficiency. For instance, the same form builder and table builder that power our admin panel tool can be installed into a Livewire component. These Livewire components can be inserted into any Blade view or rendered from a route, regardless of whether you're using Livewire elsewhere in your application.

Beyond admin panels, Filament panels can be used to create various interfaces in your application. You might build a user dashboard for a SaaS application or develop a CRM system for a specific department in your organization. A single Laravel application can host multiple panels, each with its own distinct configuration for branding, navigation, and routing.

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

You can browse an extensive list of official and community plugins on the [Filament website](/plugins).

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

## Alternatives to Filament

If you're reading this and concluding that Filament might not be the right choice for your project, that's okay! There are many other excellent projects in the Laravel ecosystem that might be a better fit for your needs. Here are a few we really like:

### Filament sounds too complicated

[Laravel Nova](https://nova.laravel.com) is an easy way to build an admin panel for your Laravel application. It's an official project maintained by the Laravel team, and purchasing it helps support the development of the Laravel framework.

### I do not want to use Livewire to customize anything

Many parts of Filament do not require you to touch Livewire at all, but building custom components might.

[Laravel Nova](https://nova.laravel.com) is built with Vue.js and Inertia.js, which might be a better fit for your project if it requires extensive customization and you have experience with these tools.

### I need an out-of-the-box CMS

[Statamic](https://statamic.com) is a CMS built on Laravel. It's a great choice if you need a CMS that's easy to set up and use, and you don't need to build a custom admin panel.

### I just want to write Blade views and handle the backend myself

[Flux](https://fluxui.dev) is the official Livewire UI kit and ships as a set of pre-built and pre-styled Blade components. It is maintained by the same team that maintains Livewire and Alpine.js, and purchasing it helps support the development of these projects.
