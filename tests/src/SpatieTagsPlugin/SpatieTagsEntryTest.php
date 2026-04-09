<?php

use Filament\Infolists\Components\SpatieTagsEntry;
use Filament\Schemas\Schema;
use Filament\SpatieLaravelTagsPlugin\Types\AllTagTypes;
use Filament\Tests\Fixtures\Livewire\Livewire;
use Filament\Tests\Fixtures\Models\Article;
use Filament\Tests\TestCase;

uses(TestCase::class);

describe('type', function (): void {
    it('defaults `getType()` to `AllTagTypes`', function (): void {
        $entry = SpatieTagsEntry::make('tags');

        expect($entry->getType())->toBeInstanceOf(AllTagTypes::class);
    });

    it('defaults `isAnyTagTypeAllowed()` to `true`', function (): void {
        $entry = SpatieTagsEntry::make('tags');

        expect($entry->isAnyTagTypeAllowed())->toBeTrue();
    });

    it('can set `type()` with a string', function (): void {
        $entry = SpatieTagsEntry::make('tags')
            ->type('category');

        expect($entry->getType())->toBe('category');
        expect($entry->isAnyTagTypeAllowed())->toBeFalse();
    });

    it('can set `type()` with a `Closure`', function (): void {
        $entry = SpatieTagsEntry::make('tags')
            ->type(static fn (): string => 'dynamic');

        expect($entry->getType())->toBe('dynamic');
        expect($entry->isAnyTagTypeAllowed())->toBeFalse();
    });

    it('can set `type()` to `AllTagTypes` to allow any type', function (): void {
        $entry = SpatieTagsEntry::make('tags')
            ->type('category')
            ->type(new AllTagTypes);

        expect($entry->getType())->toBeInstanceOf(AllTagTypes::class);
        expect($entry->isAnyTagTypeAllowed())->toBeTrue();
    });

    it('can set `type()` to `null`', function (): void {
        $entry = SpatieTagsEntry::make('tags')
            ->type(null);

        expect($entry->getType())->toBeNull();
        expect($entry->isAnyTagTypeAllowed())->toBeFalse();
    });
});

it('is configured as a badge by default', function (): void {
    $entry = SpatieTagsEntry::make('tags');

    expect($entry->isBadge())->toBeTrue();
});

describe('state from tags', function (): void {
    it('can retrieve tag names as state', function (): void {
        $record = Article::factory()->create();
        $record->attachTags(['Laravel', 'PHP']);
        $record->load('tags');

        $entry = SpatieTagsEntry::make('tags')
            ->container(
                Schema::make(Livewire::make())
                    ->record($record)
            );

        $state = $entry->getState();

        expect($state)->toContain('Laravel');
        expect($state)->toContain('PHP');
        expect($state)->toHaveCount(2);
    });

    it('returns only tags of the specified type', function (): void {
        $record = Article::factory()->create();
        $record->attachTag('Laravel', 'framework');
        $record->attachTag('PHP', 'language');
        $record->load('tags');

        $entry = SpatieTagsEntry::make('tags')
            ->type('framework')
            ->container(
                Schema::make(Livewire::make())
                    ->record($record)
            );

        $state = $entry->getState();

        expect($state)->toContain('Laravel');
        expect($state)->not->toContain('PHP');
        expect($state)->toHaveCount(1);
    });

    it('returns tags of all types when `AllTagTypes` is set', function (): void {
        $record = Article::factory()->create();
        $record->attachTag('Laravel', 'framework');
        $record->attachTag('PHP', 'language');
        $record->attachTag('Untyped');
        $record->load('tags');

        $entry = SpatieTagsEntry::make('tags')
            ->type(new AllTagTypes)
            ->container(
                Schema::make(Livewire::make())
                    ->record($record)
            );

        $state = $entry->getState();

        expect($state)->toContain('Laravel');
        expect($state)->toContain('PHP');
        expect($state)->toContain('Untyped');
        expect($state)->toHaveCount(3);
    });

    it('returns empty array when record has no tags', function (): void {
        $record = Article::factory()->create();
        $record->load('tags');

        $entry = SpatieTagsEntry::make('tags')
            ->container(
                Schema::make(Livewire::make())
                    ->record($record)
            );

        $state = $entry->getState();

        expect($state)->toBe([]);
    });

    it('deduplicates tag names in state', function (): void {
        $record = Article::factory()->create();
        $record->attachTags(['Laravel', 'PHP']);
        $record->load('tags');

        $entry = SpatieTagsEntry::make('tags')
            ->container(
                Schema::make(Livewire::make())
                    ->record($record)
            );

        $state = $entry->getState();

        expect($state)->toHaveCount(2);
        expect(count($state))->toBe(count(array_unique($state)));
    });
});
