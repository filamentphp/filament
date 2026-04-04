<?php

use Filament\Support\EloquentSerializer\EloquentSerializer;
use Filament\Support\EloquentSerializer\Package;
use Filament\Tests\Fixtures\Models\Post;
use Filament\Tests\Fixtures\Models\User;
use Filament\Tests\TestCase;
use Illuminate\Database\Eloquent\Builder;

uses(TestCase::class);

beforeEach(function (): void {
    $this->serializer = new EloquentSerializer;
});

describe('serialize and unserialize', function (): void {
    it('can serialize a simple query', function (): void {
        $builder = Post::query();

        $serialized = $this->serializer->serialize($builder);

        expect($serialized)->toBeString();
        expect($serialized)->not->toBeEmpty();
    });

    it('can unserialize back to a `Builder`', function (): void {
        $builder = Post::query();
        $serialized = $this->serializer->serialize($builder);

        $unserialized = $this->serializer->unserialize($serialized);

        expect($unserialized)->toBeInstanceOf(Builder::class);
    });

    it('preserves the model class after round-trip', function (): void {
        $builder = Post::query();
        $serialized = $this->serializer->serialize($builder);

        $unserialized = $this->serializer->unserialize($serialized);

        expect($unserialized->getModel())->toBeInstanceOf(Post::class);
    });

    it('preserves `where` clauses after round-trip', function (): void {
        $builder = Post::query()->where('is_published', true);
        $serialized = $this->serializer->serialize($builder);

        $unserialized = $this->serializer->unserialize($serialized);
        $wheres = $unserialized->getQuery()->wheres;

        // Post uses SoftDeletes, so there's an additional `deleted_at is null` where
        $publishedWheres = array_filter($wheres, static fn (array $where): bool => ($where['column'] ?? null) === 'is_published');

        expect($publishedWheres)->toHaveCount(1);
    });

    it('preserves `orderBy` clauses after round-trip', function (): void {
        $builder = Post::query()->orderBy('title', 'desc');
        $serialized = $this->serializer->serialize($builder);

        $unserialized = $this->serializer->unserialize($serialized);
        $orders = $unserialized->getQuery()->orders;

        expect($orders)->toHaveCount(1);
        expect($orders[0]['column'])->toBe('title');
        expect($orders[0]['direction'])->toBe('desc');
    });

    it('preserves `limit` after round-trip', function (): void {
        $builder = Post::query()->limit(10);
        $serialized = $this->serializer->serialize($builder);

        $unserialized = $this->serializer->unserialize($serialized);

        expect($unserialized->getQuery()->limit)->toBe(10);
    });

    it('preserves `offset` after round-trip', function (): void {
        $builder = Post::query()->offset(20);
        $serialized = $this->serializer->serialize($builder);

        $unserialized = $this->serializer->unserialize($serialized);

        expect($unserialized->getQuery()->offset)->toBe(20);
    });

    it('preserves eager loading after round-trip', function (): void {
        $builder = Post::query()->with(['author']);
        $serialized = $this->serializer->serialize($builder);

        $unserialized = $this->serializer->unserialize($serialized);
        $eagerLoads = $unserialized->getEagerLoads();

        expect($eagerLoads)->toHaveKey('author');
    });

    it('preserves `select` columns after round-trip', function (): void {
        $builder = Post::query()->select(['id', 'title']);
        $serialized = $this->serializer->serialize($builder);

        $unserialized = $this->serializer->unserialize($serialized);
        $columns = $unserialized->getQuery()->columns;

        expect($columns)->toBe(['id', 'title']);
    });

    it('preserves multiple `where` conditions after round-trip', function (): void {
        $builder = Post::query()
            ->where('is_published', true)
            ->where('rating', '>', 5)
            ->whereNotNull('content');

        $serialized = $this->serializer->serialize($builder);
        $unserialized = $this->serializer->unserialize($serialized);

        $wheres = $unserialized->getQuery()->wheres;

        // 3 explicit wheres + SoftDeletes' `deleted_at is null`
        expect($wheres)->toHaveCount(4);
    });
});

describe('SQL equivalence', function (): void {
    it('preserves SQL and bindings after round-trip for a simple `where`', function (): void {
        $original = Post::query()->where('is_published', true);
        $serialized = $this->serializer->serialize($original);

        $unserialized = $this->serializer->unserialize($serialized);

        expect($unserialized->toSql())->toBe($original->toSql());
        expect($unserialized->getBindings())->toBe($original->getBindings());
    });

    it('preserves SQL and bindings after round-trip for `orderBy` and `limit`', function (): void {
        $original = Post::query()->orderBy('created_at', 'desc')->limit(25);
        $serialized = $this->serializer->serialize($original);

        $unserialized = $this->serializer->unserialize($serialized);

        expect($unserialized->toSql())->toBe($original->toSql());
        expect($unserialized->getBindings())->toBe($original->getBindings());
    });

    it('preserves SQL and bindings after round-trip for `select`', function (): void {
        $original = Post::query()->select(['id', 'title', 'content']);
        $serialized = $this->serializer->serialize($original);

        $unserialized = $this->serializer->unserialize($serialized);

        expect($unserialized->toSql())->toBe($original->toSql());
        expect($unserialized->getBindings())->toBe($original->getBindings());
    });

    it('preserves SQL and bindings after round-trip for `whereIn`', function (): void {
        $original = Post::query()->whereIn('id', [1, 2, 3]);
        $serialized = $this->serializer->serialize($original);

        $unserialized = $this->serializer->unserialize($serialized);

        expect($unserialized->toSql())->toBe($original->toSql());
        expect($unserialized->getBindings())->toBe($original->getBindings());
    });

    it('preserves SQL and bindings after round-trip for `orWhere`', function (): void {
        $original = Post::query()
            ->where('is_published', true)
            ->orWhere('rating', '>', 5);
        $serialized = $this->serializer->serialize($original);

        $unserialized = $this->serializer->unserialize($serialized);

        expect($unserialized->toSql())->toBe($original->toSql());
        expect($unserialized->getBindings())->toBe($original->getBindings());
    });

    it('preserves SQL and bindings after round-trip for a complex query', function (): void {
        $original = Post::query()
            ->select(['id', 'title'])
            ->where('is_published', true)
            ->where('rating', '>', 3)
            ->whereNotNull('content')
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->offset(20);
        $serialized = $this->serializer->serialize($original);

        $unserialized = $this->serializer->unserialize($serialized);

        expect($unserialized->toSql())->toBe($original->toSql());
        expect($unserialized->getBindings())->toBe($original->getBindings());
    });
});

describe('`Package` structure', function (): void {
    it('contains the model class in the `Package`', function (): void {
        $builder = Post::query()->where('is_published', true);
        $serialized = $this->serializer->serialize($builder);

        $package = unserialize($serialized);

        expect($package)->toBeInstanceOf(Package::class);
        expect($package->get('model'))->toBe(Post::class);
    });

    it('contains the query `from` table in the `Package`', function (): void {
        $builder = Post::query();
        $serialized = $this->serializer->serialize($builder);

        $package = unserialize($serialized);

        expect($package->get('query'))->toHaveKey('from', 'posts');
    });

    it('preserves model casts after round-trip', function (): void {
        $original = Post::query();
        $serialized = $this->serializer->serialize($original);

        $unserialized = $this->serializer->unserialize($serialized);

        expect($unserialized->getModel()->getCasts())->toHaveKey('is_published');
        expect($unserialized->getModel()->getCasts())->toHaveKey('tags');
    });
});

describe('relationship serialization', function (): void {
    it('can serialize a `HasMany` relationship', function (): void {
        $user = new User;
        $user->id = 1;

        $builder = $user->posts();
        $serialized = $this->serializer->serialize($builder);

        expect($serialized)->toBeString();

        $unserialized = $this->serializer->unserialize($serialized);

        expect($unserialized)->toBeInstanceOf(Builder::class);
    });

    it('can serialize a `BelongsTo` relationship', function (): void {
        $post = new Post;
        $post->author_id = 1;

        $builder = $post->author();
        $serialized = $this->serializer->serialize($builder);

        expect($serialized)->toBeString();

        $unserialized = $this->serializer->unserialize($serialized);

        expect($unserialized)->toBeInstanceOf(Builder::class);
    });
});

describe('error handling', function (): void {
    it('throws when unserializing invalid data', function (): void {
        $this->expectException(\Throwable::class);

        $this->serializer->unserialize('invalid');
    });

    it('can accept a `Package` instance directly in `unserialize()`', function (): void {
        $builder = Post::query();
        $serialized = $this->serializer->serialize($builder);
        $package = unserialize($serialized);

        expect($package)->toBeInstanceOf(Package::class);

        $unserialized = $this->serializer->unserialize($package);

        expect($unserialized)->toBeInstanceOf(Builder::class);
    });
});

describe('`Package` class', function (): void {
    it('can `get()` all data', function (): void {
        $package = new Package(['key' => 'value', 'other' => 123]);

        expect($package->get())->toBe(['key' => 'value', 'other' => 123]);
    });

    it('can `get()` a specific key', function (): void {
        $package = new Package(['key' => 'value']);

        expect($package->get('key'))->toBe('value');
    });

    it('returns `null` for a missing key', function (): void {
        $package = new Package(['key' => 'value']);

        expect($package->get('missing'))->toBeNull();
    });
});
