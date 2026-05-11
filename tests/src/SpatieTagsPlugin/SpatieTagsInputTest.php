<?php

use Filament\Forms\Components\SpatieTagsInput;
use Filament\Schemas\Schema;
use Filament\SpatieLaravelTagsPlugin\Types\AllTagTypes;
use Filament\Tests\Fixtures\Livewire\Livewire;
use Filament\Tests\Fixtures\Livewire\SpatieTagsInputForm;
use Filament\Tests\Fixtures\Models\Article;
use Filament\Tests\TestCase;
use Spatie\Tags\Tag;

use function Filament\Tests\livewire;

uses(TestCase::class);

describe('type', function (): void {
    it('defaults `getType()` to `AllTagTypes`', function (): void {
        $component = SpatieTagsInput::make('tags');

        expect($component->getType())->toBeInstanceOf(AllTagTypes::class);
    });

    it('defaults `isAnyTagTypeAllowed()` to `true`', function (): void {
        $component = SpatieTagsInput::make('tags');

        expect($component->isAnyTagTypeAllowed())->toBeTrue();
    });

    it('can set `type()` with a string', function (): void {
        $component = SpatieTagsInput::make('tags')
            ->type('category');

        expect($component->getType())->toBe('category');
        expect($component->isAnyTagTypeAllowed())->toBeFalse();
    });

    it('can set `type()` with a `Closure`', function (): void {
        $component = SpatieTagsInput::make('tags')
            ->type(static fn (): string => 'dynamic');

        expect($component->getType())->toBe('dynamic');
        expect($component->isAnyTagTypeAllowed())->toBeFalse();
    });

    it('can set `type()` to `AllTagTypes` to allow any type', function (): void {
        $component = SpatieTagsInput::make('tags')
            ->type('category')
            ->type(new AllTagTypes);

        expect($component->getType())->toBeInstanceOf(AllTagTypes::class);
        expect($component->isAnyTagTypeAllowed())->toBeTrue();
    });

    it('can set `type()` to `null`', function (): void {
        $component = SpatieTagsInput::make('tags')
            ->type(null);

        expect($component->getType())->toBeNull();
        expect($component->isAnyTagTypeAllowed())->toBeFalse();
    });
});

describe('suggestions', function (): void {
    it('returns tags from the database when no custom suggestions set', function (): void {
        Tag::create(['name' => 'Laravel', 'type' => null]);
        Tag::create(['name' => 'PHP', 'type' => null]);
        Tag::create(['name' => 'JavaScript', 'type' => null]);

        $component = SpatieTagsInput::make('tags')
            ->type(null)
            ->container(Schema::make(Livewire::make()));

        $suggestions = $component->getSuggestions();

        expect($suggestions)->toContain('Laravel');
        expect($suggestions)->toContain('PHP');
        expect($suggestions)->toContain('JavaScript');
    });

    it('filters suggestions by type when a type is set', function (): void {
        Tag::create(['name' => 'Laravel', 'type' => 'framework']);
        Tag::create(['name' => 'PHP', 'type' => 'language']);
        Tag::create(['name' => 'Filament', 'type' => 'framework']);

        $component = SpatieTagsInput::make('tags')
            ->type('framework')
            ->container(Schema::make(Livewire::make()));

        $suggestions = $component->getSuggestions();

        expect($suggestions)->toContain('Laravel');
        expect($suggestions)->toContain('Filament');
        expect($suggestions)->not->toContain('PHP');
    });

    it('returns all tags when `AllTagTypes` is set', function (): void {
        Tag::create(['name' => 'Laravel', 'type' => 'framework']);
        Tag::create(['name' => 'PHP', 'type' => 'language']);
        Tag::create(['name' => 'Untyped', 'type' => null]);

        $component = SpatieTagsInput::make('tags')
            ->type(new AllTagTypes)
            ->container(Schema::make(Livewire::make()));

        $suggestions = $component->getSuggestions();

        expect($suggestions)->toContain('Laravel');
        expect($suggestions)->toContain('PHP');
        expect($suggestions)->toContain('Untyped');
    });

    it('returns only untyped tags when type is `null`', function (): void {
        Tag::create(['name' => 'Typed', 'type' => 'category']);
        Tag::create(['name' => 'Untyped', 'type' => null]);

        $component = SpatieTagsInput::make('tags')
            ->type(null)
            ->container(Schema::make(Livewire::make()));

        $suggestions = $component->getSuggestions();

        expect($suggestions)->toContain('Untyped');
        expect($suggestions)->not->toContain('Typed');
    });

    it('returns custom suggestions when set, ignoring database tags', function (): void {
        Tag::create(['name' => 'DatabaseTag', 'type' => null]);

        $component = SpatieTagsInput::make('tags')
            ->suggestions(['Custom1', 'Custom2']);

        $suggestions = $component->getSuggestions();

        expect($suggestions)->toBe(['Custom1', 'Custom2']);
        expect($suggestions)->not->toContain('DatabaseTag');
    });
});

describe('integration', function (): void {
    it('can load existing tags into form state', function (): void {
        $record = Article::factory()->create();
        $record->attachTags(['Laravel', 'PHP']);

        livewire(SpatieTagsInputForm::class, ['record' => $record])
            ->assertFormSet(function (array $state): array {
                expect($state['tags'])->toBeArray();
                expect($state['tags'])->toContain('Laravel');
                expect($state['tags'])->toContain('PHP');

                return [];
            });
    });

    it('loads empty state when record has no tags', function (): void {
        $record = Article::factory()->create();

        livewire(SpatieTagsInputForm::class, ['record' => $record])
            ->assertFormSet(function (array $state): array {
                expect($state['tags'])->toBeArray();
                expect($state['tags'])->toBeEmpty();

                return [];
            });
    });

    it('can save tags to the record', function (): void {
        $record = Article::factory()->create();

        livewire(SpatieTagsInputForm::class, ['record' => $record])
            ->fillForm([
                'tags' => ['Filament', 'Livewire'],
            ])
            ->call('save');

        $freshRecord = Article::with('tags')->find($record->getKey());
        $tagNames = $freshRecord->getRelationValue('tags')->pluck('name')->sort()->values()->all();

        expect($tagNames)->toBe(['Filament', 'Livewire']);
    });

    it('can replace existing tags on save', function (): void {
        $record = Article::factory()->create();
        $record->attachTags(['Old1', 'Old2']);

        livewire(SpatieTagsInputForm::class, ['record' => $record])
            ->fillForm([
                'tags' => ['New1', 'New2'],
            ])
            ->call('save');

        $freshRecord = Article::with('tags')->find($record->getKey());
        $tagNames = $freshRecord->getRelationValue('tags')->pluck('name')->sort()->values()->all();

        expect($tagNames)->toBe(['New1', 'New2']);
    });

    it('can clear all tags on save', function (): void {
        $record = Article::factory()->create();
        $record->attachTags(['Remove1', 'Remove2']);

        livewire(SpatieTagsInputForm::class, ['record' => $record])
            ->fillForm([
                'tags' => [],
            ])
            ->call('save');

        $freshRecord = Article::with('tags')->find($record->getKey());

        expect($freshRecord->getRelationValue('tags'))->toBeEmpty();
    });

    it('invalidates the cached tags relation after syncing so subsequent reads do not return stale data', function (): void {
        $record = Article::factory()->create();
        $record->attachTags(['Old1', 'Old2']);

        $component = livewire(SpatieTagsInputForm::class, ['record' => $record])
            ->fillForm(['tags' => []])
            ->call('saveOnly');

        $liveRecord = $component->instance()->record;

        expect($liveRecord->getRelationValue('tags'))->toBeEmpty();
    });

    it('can load typed tags into form state', function (): void {
        $record = Article::factory()->create();
        $record->attachTag('Laravel', 'framework');
        $record->attachTag('PHP', 'language');

        livewire(SpatieTagsInputForm::class, [
            'record' => $record,
            'tagType' => 'framework',
        ])
            ->assertFormSet(function (array $state): array {
                expect($state['tags'])->toContain('Laravel');
                expect($state['tags'])->not->toContain('PHP');

                return [];
            });
    });

    it('can save typed tags to the record', function (): void {
        $record = Article::factory()->create();

        livewire(SpatieTagsInputForm::class, [
            'record' => $record,
            'tagType' => 'framework',
        ])
            ->fillForm([
                'tags' => ['Filament', 'Livewire'],
            ])
            ->call('save');

        $freshRecord = Article::with('tags')->find($record->getKey());
        $frameworkTags = $freshRecord->getRelationValue('tags')
            ->filter(fn ($tag) => $tag->type === 'framework')
            ->pluck('name')->sort()->values()->all();

        expect($frameworkTags)->toBe(['Filament', 'Livewire']);
    });

    it('does not affect tags of other types when saving typed tags', function (): void {
        $record = Article::factory()->create();
        $record->attachTag('PHP', 'language');

        livewire(SpatieTagsInputForm::class, [
            'record' => $record,
            'tagType' => 'framework',
        ])
            ->fillForm([
                'tags' => ['Laravel'],
            ])
            ->call('save');

        $freshRecord = Article::with('tags')->find($record->getKey());
        $allTags = $freshRecord->getRelationValue('tags');

        $languageTags = $allTags->filter(fn ($tag) => $tag->type === 'language')->pluck('name')->all();
        $frameworkTags = $allTags->filter(fn ($tag) => $tag->type === 'framework')->pluck('name')->all();

        expect($languageTags)->toContain('PHP');
        expect($frameworkTags)->toContain('Laravel');
    });
});
