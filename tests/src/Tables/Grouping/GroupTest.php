<?php

use Filament\Tables\Grouping\Group;
use Filament\Tests\Fixtures\Models\Post;
use Filament\Tests\TestCase;
use Illuminate\Support\HtmlString;

uses(TestCase::class);

it('can be constructed with `make()`', function (): void {
    $group = Group::make('status');

    expect($group)->toBeInstanceOf(Group::class);
    expect($group->getId())->toBe('status');
});

describe('ID and column', function (): void {
    it('uses ID as column by default', function (): void {
        $group = Group::make('status');

        expect($group->getColumn())->toBe('status');
    });

    it('can set a custom `column()`', function (): void {
        $group = Group::make('status')
            ->column('custom_status');

        expect($group->getColumn())->toBe('custom_status');
    });

    it('can change `id()` after construction', function (): void {
        $group = Group::make('old');
        $group->id('new');

        expect($group->getId())->toBe('new');
    });
});

describe('label', function (): void {
    it('generates a label from the ID', function (): void {
        $group = Group::make('author_id');

        expect($group->getLabel())->toBe('Author id');
    });

    it('can set a custom `label()`', function (): void {
        $group = Group::make('status')
            ->label('Current Status');

        expect($group->getLabel())->toBe('Current Status');
    });

    it('can set `label()` with a `Closure`', function (): void {
        $group = Group::make('status')
            ->label(static fn (): string => 'Dynamic');

        expect($group->getLabel())->toBe('Dynamic');
    });

    it('can set `label()` with an `Htmlable`', function (): void {
        $htmlable = new HtmlString('<strong>Bold</strong>');
        $group = Group::make('status')
            ->label($htmlable);

        expect($group->getLabel())->toBe($htmlable);
    });
});

describe('collapsible', function (): void {
    it('defaults `isCollapsible()` to `false`', function (): void {
        $group = Group::make('status');

        expect($group->isCollapsible())->toBeFalse();
    });

    it('can set `collapsible()`', function (): void {
        $group = Group::make('status')
            ->collapsible();

        expect($group->isCollapsible())->toBeTrue();
    });

    it('can set `collapsible()` to `false`', function (): void {
        $group = Group::make('status')
            ->collapsible()
            ->collapsible(false);

        expect($group->isCollapsible())->toBeFalse();
    });
});

describe('title prefix', function (): void {
    it('defaults `isTitlePrefixedWithLabel()` to `true`', function (): void {
        $group = Group::make('status');

        expect($group->isTitlePrefixedWithLabel())->toBeTrue();
    });

    it('can set `titlePrefixedWithLabel()` to `false`', function (): void {
        $group = Group::make('status')
            ->titlePrefixedWithLabel(false);

        expect($group->isTitlePrefixedWithLabel())->toBeFalse();
    });
});

describe('date grouping', function (): void {
    it('defaults `isDate()` to `false`', function (): void {
        $group = Group::make('created_at');

        expect($group->isDate())->toBeFalse();
    });

    it('can set `date()`', function (): void {
        $group = Group::make('created_at')
            ->date();

        expect($group->isDate())->toBeTrue();
    });

    it('can set `date()` to `false`', function (): void {
        $group = Group::make('created_at')
            ->date()
            ->date(false);

        expect($group->isDate())->toBeFalse();
    });
});

describe('key from record', function (): void {
    it('gets key from record array by column name', function (): void {
        $group = Group::make('status');

        $key = $group->getKey(['status' => 'active']);

        expect($key)->toBe('active');
    });

    it('gets key from record model by attribute', function (): void {
        $post = new Post;
        $post->is_published = true;

        $group = Group::make('is_published');

        $key = $group->getKey($post);

        expect($key)->toBe(true);
    });

    it('can use custom `getKeyFromRecordUsing()` callback', function (): void {
        $group = Group::make('status')
            ->getKeyFromRecordUsing(static fn (array $record): string => strtoupper($record['status']));

        $key = $group->getKey(['status' => 'active']);

        expect($key)->toBe('ACTIVE');
    });

    it('returns `null` for `getStringKey()` when key is blank', function (): void {
        $group = Group::make('status');

        $key = $group->getStringKey(['status' => null]);

        expect($key)->toBeNull();
    });

    it('converts date key to date string format', function (): void {
        $group = Group::make('created_at')
            ->date();

        $key = $group->getStringKey(['created_at' => '2024-06-15 14:30:00']);

        expect($key)->toBe('2024-06-15');
    });
});

describe('title from record', function (): void {
    it('gets title from record array by column name', function (): void {
        $group = Group::make('status');

        $title = $group->getTitle(['status' => 'Active']);

        expect($title)->toBe('Active');
    });

    it('can use custom `getTitleFromRecordUsing()` callback', function (): void {
        $group = Group::make('status')
            ->getTitleFromRecordUsing(static fn (array $record): string => "Status: {$record['status']}");

        $title = $group->getTitle(['status' => 'active']);

        expect($title)->toBe('Status: active');
    });
});

describe('description', function (): void {
    it('returns `null` for `getDescription()` by default', function (): void {
        $group = Group::make('status');

        $description = $group->getDescription(['status' => 'active'], 'Active');

        expect($description)->toBeNull();
    });

    it('can use `getDescriptionFromRecordUsing()` callback', function (): void {
        $group = Group::make('status')
            ->getDescriptionFromRecordUsing(static fn (array $record): string => "Count: {$record['count']}");

        $description = $group->getDescription(['status' => 'active', 'count' => 5], 'Active');

        expect($description)->toBe('Count: 5');
    });
});

describe('relationships', function (): void {
    it('returns `null` for `getRelationshipName()` when column has no dot', function (): void {
        $group = Group::make('status');

        expect($group->getRelationshipName())->toBeNull();
    });

    it('extracts relationship name from dotted column', function (): void {
        $group = Group::make('author.name');

        expect($group->getRelationshipName())->toBe('author');
    });

    it('extracts attribute from dotted column', function (): void {
        $group = Group::make('author.name');

        expect($group->getRelationshipAttribute())->toBe('name');
    });

    it('returns full column as attribute when no dot', function (): void {
        $group = Group::make('status');

        expect($group->getRelationshipAttribute())->toBe('status');
    });

    it('returns `null` for `getRelationship()` when column has no dot', function (): void {
        $group = Group::make('title');

        $post = new Post;

        expect($group->getRelationship($post))->toBeNull();
    });

    it('returns a relationship for a dotted column', function (): void {
        $group = Group::make('author.name');

        $post = new Post;
        $relationship = $group->getRelationship($post);

        expect($relationship)->not->toBeNull();
    });
});

describe('callback setters', function (): void {
    it('returns fluent `$this` from `groupQueryUsing()`', function (): void {
        $group = Group::make('status');

        expect($group->groupQueryUsing(static fn () => null))->toBe($group);
    });

    it('returns fluent `$this` from `orderQueryUsing()`', function (): void {
        $group = Group::make('status');

        expect($group->orderQueryUsing(static fn () => null))->toBe($group);
    });

    it('returns fluent `$this` from `scopeQueryUsing()`', function (): void {
        $group = Group::make('status');

        expect($group->scopeQueryUsing(static fn () => null))->toBe($group);
    });

    it('returns fluent `$this` from `scopeQueryByKeyUsing()`', function (): void {
        $group = Group::make('status');

        expect($group->scopeQueryByKeyUsing(static fn () => null))->toBe($group);
    });

    it('can clear callbacks with `null`', function (): void {
        $group = Group::make('status')
            ->groupQueryUsing(static fn () => null)
            ->groupQueryUsing(null)
            ->orderQueryUsing(static fn () => null)
            ->orderQueryUsing(null)
            ->scopeQueryUsing(static fn () => null)
            ->scopeQueryUsing(null);

        expect($group)->toBeInstanceOf(Group::class);
    });
});
