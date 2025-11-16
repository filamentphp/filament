---
title: Overview
---

## Introduction

All examples in this guide will be written using [Pest](https://pestphp.com). To use Pest's Livewire plugin for testing, you can follow the installation instructions in the Pest documentation on plugins: [Livewire plugin for Pest](https://pestphp.com/docs/plugins#livewire). However, you can easily adapt this to PHPUnit, mostly by switching out the `livewire()` function from Pest with the `Livewire::test()` method.

Since all Filament components are mounted to a Livewire component, we're just using Livewire testing helpers everywhere. If you've never tested Livewire components before, please read [this guide](https://livewire.laravel.com/docs/testing) from the Livewire docs.

## Testing guides

Looking for a full example on how to test a panel resource? Check out the [Testing resources](testing-resources) section.

If you could like to learn the different methods available to test tables, check out the [Testing tables](testing-tables) section.

If you need to test a schema, which encompasses both forms and infolists, check out the [Testing schemas](testing-schemas) section.

If you would like to test an action, including actions that exist in tables or in schemas, check out the [Testing actions](testing-actions) section.

If you would like to test a notification that you have sent, check out the [Testing notifications](testing-notifications) section.

If you would like to test a custom page in a panel, these are Livewire components with no special behavior, so you should visit the [testing](https://livewire.laravel.com/docs/testing) section of the Livewire documentation.

## What is a Livewire component when using Filament?

When testing Filament, it is useful to understand which components are Livewire components and which aren't. With this information, you know which classes to pass to the `livewire()` function in Pest or the `Livewire::test()` method in PHPUnit.

Some examples of Livewire components are:

- Pages in a panel, including page classes in a resource's `Pages` directory
- Relation managers in a resource
- Widgets

Some examples of classes that are not Livewire components are:

- Resource classes
- Schema components
- Actions

These classes all interact with Livewire, but they are not Livewire components themselves. You can still test them, for example, by calling various methods and using the [Pest expectation API](https://pestphp.com/docs/expectations) to assert the expected behavior. However, the most useful tests will involve Livewire components, since they provide the best end-to-end testing coverage of your users' experience.
