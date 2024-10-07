---
title: Introduction to Filament
---

Filament is a meta-framework for Laravel. It aims to provide a set of components and conventions that make it easier to build the frontend for your Laravel applications using Livewire, Alpine.js, and Tailwind CSS. Filament is designed to be flexible and extensible, so you can use as much or as little of it as you like and customize it to fit your needs.

Thousands of developers choose Filament to add an admin panel interface to their app. You'll be able to do this regardless of what your app's frontend is built using: it's commonly paired with tools like Inertia.js that interface Laravel with a full-fat frontend framework like Vue.js, React, or Svelte.

If you're building your app's frontend with Blade (Livewire or not), you'll be able to enrich your views with components from the framework to save yourself time. For example, the same form builder and table builder that powers our admin panel tool can be installed into a Livewire component. Livewire components can be inserted into any Blade view or rendered from a route, regardless of whether you're already using Livewire for your other Blade views or not.

The core package for building admin panels is `filament/filament`, which requires all the other packages in the Filament ecosystem. If you're building a Blade application from scratch, you might want to install the `filament/forms` and `filament/tables` packages to get access to the form and table builders.

Aside from building admin areas, Filament panels can be used to build a variety of interfaces in your app. For example, you could build a user dashboard in a SaaS app, or a CRM for a certain department in your organization. A single Laravel app can contain multiple panels, each with its own configuration for branding, navigation, and routing. To learn more about panels, see the [Panels Overview](../panels/overview) page.

Depending on where you are in the development process of your app, you should choose a quickstart guide that fits your immediate need. If you're just interested in learning about Filament, you should probably pick the "Building an admin panel" tutorial, since it covers concepts within multiple packages.

- [Building an admin panel](building-an-admin-panel)
- [Building a SaaS](building-a-saas)
- [Adding a form to a Blade view](adding-a-form-to-a-blade-view)
- [Adding a table to a Blade view](adding-a-table-to-a-blade-view)
- [Adding modals to a Blade view](adding-modals-to-a-blade-view)
- [Adding a read-only infolist to a Blade view](adding-a-read-only-infolist-to-a-blade-view)
- [Adding a chart to a Blade view](adding-a-chart-to-a-blade-view)
- [Sending flash notifications in Livewire](sending-flash-notifications-in-livewire)
