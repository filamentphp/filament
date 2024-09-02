---
title: Overview
---

## What is a panel?

In Filament, a panel is a modular section of your Laravel application that groups resources (CRUD interfaces), dashboards, and any custom pages you create. Panels help organize and manage different parts of your application, such as an admin panel, a user dashboard in a SaaS, or any other distinct area in your frontend. Each panel can have its own configuration for branding, navigation, and routing, making it easier to build and maintain complex applications.

Panels are registered in their own Laravel service provider class, which is also used to configure it. This makes it easy to add many independent panels to one Laravel installation without much complexity. Panels can even be registered within Laravel packages, making them easy to share and reuse across different projects. Panels are Livewire applications, but since they just use the standard Laravel routing system, they can live alongside any other frontend that you serve from your Laravel app, such as using Inertia.js.

## Why are there other Filament packages, such as forms and tables?

When Filament was originally released, the only option was to use a panel. The panels package contained all the functionality needed to build CRUD interfaces, including a form and table builder. However, as Filament has grown, it has become clear that many developers want to use the form and table builders, alongside other Filament components, in Blade applications that they've built from scratch. As a result, the form and table builders were extracted into their own packages, so they can be installed and used independently of a panel. All the features that you find in these packages may be used inside a panel, but they can also be used in any other Blade application.

## Should I use a panel or build my own Blade application?

If you're building a new application from scratch, you should consider using a panel. Panels are a great way to organize your application and make it easier to build and maintain. They provide a lot of functionality out of the box, such as authentication, navigation, and automatic routing. However, if you're working on an existing application, or you have specific requirements that don't fit into the panel model, including a unique UI layout or routing structure, you may want to build your own Blade application. The form and table builders, as well as other Filament components, can be used in any Blade application, so you can still take advantage of the functionality that Filament provides.
