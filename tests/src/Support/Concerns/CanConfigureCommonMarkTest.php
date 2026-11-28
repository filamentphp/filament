<?php

use Filament\Forms\Components\MarkdownEditor;
use Filament\Infolists\Components\TextEntry;
use Filament\Tables\Columns\Summarizers\Summarizer;
use Filament\Tables\Columns\TextColumn;
use Filament\Tests\TestCase;

uses(TestCase::class);

it('defaults `max_nesting_level` without replacing other CommonMark options', function (string $componentClass): void {
    $component = $componentClass::make('content');

    expect($component->getCommonMarkOptions())->toBe(['max_nesting_level' => 100]);

    foreach ([null, [], static fn (): ?array => null] as $options) {
        $component->commonMarkOptions($options);

        expect($component->getCommonMarkOptions())->toBe(['max_nesting_level' => 100]);
    }

    foreach ([['html_input' => 'strip'], static fn (string $state): array => ['html_input' => $state]] as $options) {
        $component->commonMarkOptions($options);

        expect($component->getCommonMarkOptions('strip'))
            ->toMatchArray(['max_nesting_level' => 100, 'html_input' => 'strip']);
    }
})->with([MarkdownEditor::class, TextColumn::class, TextEntry::class, Summarizer::class]);

it('preserves explicit `max_nesting_level` overrides and restores the default when cleared', function (string $componentClass): void {
    $component = $componentClass::make('content');

    foreach ([0, 3, 150, PHP_INT_MAX] as $limit) {
        foreach ([['max_nesting_level' => $limit], static fn (int $state): array => ['max_nesting_level' => $state]] as $options) {
            $component->commonMarkOptions($options);

            expect($component->getCommonMarkOptions($limit))->toBe(['max_nesting_level' => $limit]);
        }
    }

    $component->commonMarkOptions(null);

    expect($component->getCommonMarkOptions())->toBe(['max_nesting_level' => 100]);
})->with([MarkdownEditor::class, TextColumn::class, TextEntry::class, Summarizer::class]);
