<?php

use function Pest\Laravel\test;

uses(\Filament\Tests\TestCase::class);

it('uses livewire:init event listener for Livewire API calls in schemas package', function (): void {
    $schemasJs = file_get_contents(__DIR__ . '/../../../packages/schemas/resources/js/index.js');

    // Verify that Livewire.interceptMessage is called within a livewire:init listener
    expect($schemasJs)
        ->toContain("document.addEventListener('livewire:init'")
        ->toContain('Livewire.interceptMessage');

    // Verify the structure: livewire:init listener should contain Livewire.interceptMessage
    // Extract the livewire:init listener block - find from livewire:init to the closing brace
    preg_match(
        "/document\.addEventListener\('livewire:init',\s*\(\)\s*=>\s*\{[\s\S]*?Livewire\.interceptMessage/",
        $schemasJs,
        $matches
    );

    expect($matches)
        ->toHaveCount(1, 'Livewire.interceptMessage should be inside livewire:init listener');
});

it('does not call Livewire API methods in alpine:init listener', function (): void {
    $schemasJs = file_get_contents(__DIR__ . '/../../../packages/schemas/resources/js/index.js');

    // Extract the alpine:init listener block
    preg_match(
        "/document\.addEventListener\('alpine:init',\s*\(\)\s*=>\s*\{([\s\S]*?)\}\)/",
        $schemasJs,
        $matches
    );

    expect($matches)
        ->toHaveCount(2, 'alpine:init event listener should exist');

    $alpineInitBlock = $matches[1];

    // Verify that Livewire API calls are NOT in the alpine:init block
    expect($alpineInitBlock)
        ->not->toContain('Livewire.interceptMessage', 'Livewire.interceptMessage should NOT be in alpine:init listener');
});

it('ensures Livewire.interceptMessage is called before Alpine data definitions', function (): void {
    $schemasJs = file_get_contents(__DIR__ . '/../../../packages/schemas/resources/js/index.js');

    // Find positions of key elements
    $alpineInitPos = strpos($schemasJs, "document.addEventListener('alpine:init'");
    $livewireInitPos = strpos($schemasJs, "document.addEventListener('livewire:init'");
    $livewireInterceptPos = strpos($schemasJs, 'Livewire.interceptMessage');

    expect($alpineInitPos)
        ->toBeLessThan($livewireInitPos, 'alpine:init should be defined before livewire:init');

    expect($livewireInitPos)
        ->toBeLessThan($livewireInterceptPos, 'livewire:init listener should be defined before Livewire.interceptMessage is called');
});
