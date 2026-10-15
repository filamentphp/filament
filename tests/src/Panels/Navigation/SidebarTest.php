<?php

use Filament\Livewire\Sidebar;
use Filament\Livewire\Topbar;
use Filament\Tests\Panels\Navigation\TestCase;

use function Filament\Tests\livewire;

uses(TestCase::class);

it('renders close button, inert binding, and escape listener on sidebar', function (): void {
    livewire(Sidebar::class)
        ->assertSuccessful()
        ->assertSeeHtml('fi-sidebar-close-btn')
        ->assertSeeHtml('x-bind:inert="(! $store.sidebar.isOpen) && ($store.sidebar.isMobile')
        ->assertSeeHtml('x-on:keydown.window.escape="if ($store.sidebar.isMobile && $store.sidebar.isOpen) $store.sidebar.close()"');
});

it('renders inert binding on topbar when mobile sidebar is open', function (): void {
    livewire(Topbar::class)
        ->assertSuccessful()
        ->assertSeeHtml('x-bind:inert="$store.sidebar.isMobile && $store.sidebar.isOpen"');
});
