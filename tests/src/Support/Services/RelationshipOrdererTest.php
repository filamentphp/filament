<?php

use Filament\Support\Services\RelationshipOrderer;
use Filament\Tests\Fixtures\Models\Post;
use Filament\Tests\Fixtures\Models\User;
use Filament\Tests\TestCase;
use Illuminate\Database\Connection;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Database\Query\Builder;
use Illuminate\Database\Query\Expression;
use Illuminate\Database\Query\Grammars\Grammar;
use Illuminate\Database\Query\Processors\Processor;

uses(TestCase::class);

beforeEach(function (): void {
    $this->orderer = new RelationshipOrderer;
});

describe('`orderQuery()`', function (): void {
    it('uses `orderByRelation()` provided by an unsupported Eloquent query builder', function (): void {
        $databaseConnection = Mockery::mock(Connection::class);
        $databaseConnection->shouldReceive('getDriverName')->andReturn('mongodb');

        $query = new class(new Builder($databaseConnection, new Grammar($databaseConnection), new Processor)) extends EloquentBuilder
        {
            /** @var array{relationship: string, column: string, direction: string} | null */
            public ?array $relationshipOrder = null;

            public function orderByRelation(string $relationship, string $column, string $direction): static
            {
                $this->relationshipOrder = [
                    'relationship' => $relationship,
                    'column' => $column,
                    'direction' => $direction,
                ];

                return $this;
            }
        };
        $query->setModel(new Post);

        expect($this->orderer->orderQuery($query, 'author', 'name', 'desc'))
            ->toBe($query)
            ->and($query->relationshipOrder)
            ->toBe([
                'relationship' => 'author',
                'column' => 'name',
                'direction' => 'desc',
            ]);
    });

    it('uses a relationship subquery for a supported driver when the Eloquent query builder provides `orderByRelation()`', function (): void {
        $query = new class(Post::query()->toBase()) extends EloquentBuilder
        {
            public bool $usedRelationshipOrder = false;

            public function orderByRelation(string $relationship, string $column, string $direction): static
            {
                $this->usedRelationshipOrder = true;

                return $this;
            }
        };
        $query->setModel(new Post);

        expect($this->orderer->orderQuery($query, 'author', 'name', 'desc'))
            ->toBe($query)
            ->and($query->usedRelationshipOrder)
            ->toBeFalse()
            ->and($query->getQuery()->orders[0]['column'])
            ->toBeInstanceOf(Expression::class)
            ->and($query->getQuery()->orders[0]['direction'])
            ->toBe('desc');
    });
});

describe('`buildSubquery()`', function (): void {
    it('builds a subquery for a `BelongsTo` relationship', function (): void {
        $query = Post::query();

        $subquery = $this->orderer->buildSubquery($query, 'author', 'name');

        expect($subquery)->toBeInstanceOf(Builder::class);
        expect($subquery->limit)->toBe(1);
    });

    it('builds a subquery selecting the specified column', function (): void {
        $query = Post::query();

        $subquery = $this->orderer->buildSubquery($query, 'author', 'name');

        $columns = $subquery->columns;

        expect($columns)->toHaveCount(1);
        expect($columns[0])->toContain('name');
    });

    it('builds a subquery for a `HasOne` relationship', function (): void {
        $query = User::query();

        $subquery = $this->orderer->buildSubquery($query, 'profile', 'bio');

        expect($subquery)->toBeInstanceOf(Builder::class);
        expect($subquery->limit)->toBe(1);
    });

    it('builds a subquery for a nested relationship', function (): void {
        $query = Post::query();

        $subquery = $this->orderer->buildSubquery($query, 'author.profile', 'bio');

        expect($subquery)->toBeInstanceOf(Builder::class);
        expect($subquery->limit)->toBe(1);

        // Nested relationships add joins
        expect($subquery->joins)->not->toBeEmpty();
    });

    it('builds a subquery with `whereColumn` constraints', function (): void {
        $query = Post::query();

        $subquery = $this->orderer->buildSubquery($query, 'author', 'name');

        $wheres = $subquery->wheres;

        // Should have a whereColumn linking the subquery to the parent
        $columnWheres = array_filter($wheres, static fn (array $where): bool => ($where['type'] ?? '') === 'Column');

        expect($columnWheres)->not->toBeEmpty();
    });
});

describe('validation', function (): void {
    it('throws `InvalidArgumentException` for unsupported relationship types', function (): void {
        $query = User::query();

        // `posts()` is a HasMany, which is not supported
        expect(fn () => $this->orderer->buildSubquery($query, 'posts', 'title'))
            ->toThrow(InvalidArgumentException::class, 'Nested sorting only supports');
    });
});

describe('`BelongsToThrough` relationship', function (): void {
    it('builds a subquery for a `BelongsToThrough` relationship', function (): void {
        $query = Post::query();

        $subquery = $this->orderer->buildSubquery($query, 'team', 'name');

        expect($subquery)->toBeInstanceOf(Builder::class);
        expect($subquery->limit)->toBe(1);
    });
});
