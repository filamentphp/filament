<?php

use Filament\Forms\Components\RichEditor\MentionProvider;
use Filament\Tests\TestCase;

uses(TestCase::class);

describe('construction', function (): void {
    it('can be constructed with a trigger character', function (): void {
        $provider = MentionProvider::make('@');

        expect($provider->getChar())->toBe('@');
    });
});

describe('search results', function (): void {
    it('filters items case-insensitively when no custom search callback', function (): void {
        $provider = MentionProvider::make('@')
            ->items(['alice' => 'Alice Smith', 'bob' => 'Bob Jones', 'carol' => 'Carol Smith']);

        $results = $provider->getSearchResults('smith');

        expect($results)->toHaveCount(2);
        expect($results)->toHaveKey('alice');
        expect($results)->toHaveKey('carol');
    });

    it('returns all items when search is blank', function (): void {
        $provider = MentionProvider::make('@')
            ->items(['a' => 'Alice', 'b' => 'Bob']);

        $results = $provider->getSearchResults('');

        expect($results)->toHaveCount(2);
    });

    it('uses custom `getSearchResultsUsing()` callback when set', function (): void {
        $provider = MentionProvider::make('@')
            ->getSearchResultsUsing(static fn (string $search): array => ['custom' => "Found: {$search}"]);

        $results = $provider->getSearchResults('test');

        expect($results)->toBe(['custom' => 'Found: test']);
    });

    it('normalizes result values to strings', function (): void {
        $provider = MentionProvider::make('@')
            ->items(['a' => 'Alice', 'b' => 'Bob']);

        $results = $provider->getSearchResults('');

        expect($results)->toHaveCount(2);
        expect($results['a'])->toBeString();
        expect($results['b'])->toBeString();
    });
});

describe('labels', function (): void {
    it('returns labels from items when no custom callback', function (): void {
        $provider = MentionProvider::make('@')
            ->items(['alice' => 'Alice', 'bob' => 'Bob', 'carol' => 'Carol']);

        $labels = $provider->getLabels(['alice', 'carol']);

        expect($labels)->toBe(['alice' => 'Alice', 'carol' => 'Carol']);
    });

    it('uses custom `getLabelsUsing()` callback when set', function (): void {
        $provider = MentionProvider::make('@')
            ->getLabelsUsing(static fn (array $ids): array => array_combine($ids, array_map(
                static fn (string $id): string => "User #{$id}",
                $ids,
            )));

        $labels = $provider->getLabels(['1', '2']);

        expect($labels)->toBe(['1' => 'User #1', '2' => 'User #2']);
    });
});

describe('URL', function (): void {
    it('returns `null` from `getUrl()` when no callback set', function (): void {
        $provider = MentionProvider::make('@');

        expect($provider->getUrl('1', 'Alice'))->toBeNull();
    });

    it('returns `false` for `hasUrl()` when no callback set', function (): void {
        $provider = MentionProvider::make('@');

        expect($provider->hasUrl())->toBeFalse();
    });

    it('can set `url()` callback and resolve URLs', function (): void {
        $provider = MentionProvider::make('@')
            ->url(static fn (string $id, string $label): string => "/users/{$id}");

        expect($provider->hasUrl())->toBeTrue();
        expect($provider->getUrl('42', 'Alice'))->toBe('/users/42');
    });
});

describe('items', function (): void {
    it('returns empty array for `getItems()` by default', function (): void {
        $provider = MentionProvider::make('@');

        expect($provider->getItems())->toBe([]);
    });

    it('returns `false` for `hasItems()` by default', function (): void {
        $provider = MentionProvider::make('@');

        expect($provider->hasItems())->toBeFalse();
    });

    it('can set `items()` with an array', function (): void {
        $provider = MentionProvider::make('@')
            ->items(['a' => 'Alice', 'b' => 'Bob']);

        expect($provider->hasItems())->toBeTrue();
        expect($provider->getItems())->toBe(['a' => 'Alice', 'b' => 'Bob']);
    });

    it('can set `items()` with a `Closure`', function (): void {
        $provider = MentionProvider::make('@')
            ->items(static fn (): array => ['x' => 'Dynamic']);

        expect($provider->getItems())->toBe(['x' => 'Dynamic']);
    });
});

describe('extra attributes', function (): void {
    it('returns empty array for `getExtraAttributes()` by default', function (): void {
        $provider = MentionProvider::make('@');

        expect($provider->getExtraAttributes())->toBe([]);
    });

    it('can set `extraAttributes()` with an array', function (): void {
        $provider = MentionProvider::make('@')
            ->extraAttributes(['data-type' => 'user']);

        expect($provider->getExtraAttributes())->toBe(['data-type' => 'user']);
    });

    it('can set `extraAttributes()` with a `Closure`', function (): void {
        $provider = MentionProvider::make('@')
            ->extraAttributes(static fn (): array => ['data-dynamic' => 'yes']);

        expect($provider->getExtraAttributes())->toBe(['data-dynamic' => 'yes']);
    });
});

describe('messages', function (): void {
    it('returns default translations for messages when not customized', function (): void {
        $provider = MentionProvider::make('@');

        expect($provider->getNoItemsMessage())->toBeString()->not->toBeEmpty();
        expect($provider->getNoSearchResultsMessage())->toBeString()->not->toBeEmpty();
        expect($provider->getSearchingMessage())->toBeString()->not->toBeEmpty();
        expect($provider->getSearchPrompt())->toBeString()->not->toBeEmpty();
    });

    it('can set custom messages', function (): void {
        $provider = MentionProvider::make('@')
            ->noItemsMessage('No users')
            ->noSearchResultsMessage('None found')
            ->searchingMessage('Looking...')
            ->searchPrompt('Type name');

        expect($provider->getNoItemsMessage())->toBe('No users');
        expect($provider->getNoSearchResultsMessage())->toBe('None found');
        expect($provider->getSearchingMessage())->toBe('Looking...');
        expect($provider->getSearchPrompt())->toBe('Type name');
    });
});

it('returns `false` for `hasSearchResultsUsing()` by default', function (): void {
    $provider = MentionProvider::make('@');

    expect($provider->hasSearchResultsUsing())->toBeFalse();
});

it('returns `true` for `hasSearchResultsUsing()` when callback set', function (): void {
    $provider = MentionProvider::make('@')
        ->getSearchResultsUsing(static fn (): array => []);

    expect($provider->hasSearchResultsUsing())->toBeTrue();
});
