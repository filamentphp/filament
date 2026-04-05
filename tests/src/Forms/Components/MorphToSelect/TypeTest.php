<?php

use Filament\Forms\Components\MorphToSelect\Type;
use Filament\Forms\Components\Select;
use Filament\Schemas\Schema;
use Filament\Tests\Fixtures\Livewire\Livewire;
use Filament\Tests\Fixtures\Models\Post;
use Filament\Tests\Fixtures\Models\User;
use Filament\Tests\TestCase;

uses(TestCase::class);

describe('construction', function (): void {
    it('can be constructed with a model class', function (): void {
        $type = Type::make(User::class);

        expect($type->getModel())->toBe(User::class);
    });

    it('returns morph class from `getAlias()`', function (): void {
        $type = Type::make(User::class);

        expect($type->getAlias())->toBeString();
        expect($type->getAlias())->not->toBeEmpty();
    });
});

describe('label', function (): void {
    it('auto-generates label from model class when not set', function (): void {
        $type = Type::make(User::class);

        expect($type->getLabel())->toBeString();
        expect($type->getLabel())->not->toBeEmpty();
    });

    it('uses custom label when set', function (): void {
        $type = Type::make(User::class)
            ->label('Custom User Type');

        expect($type->getLabel())->toBe('Custom User Type');
    });

    it('returns fluent `$this` from `label()`', function (): void {
        $type = Type::make(User::class);

        expect($type->label('Test'))->toBe($type);
    });
});

describe('title attribute', function (): void {
    it('can set `titleAttribute()`', function (): void {
        $type = Type::make(User::class)
            ->titleAttribute('name');

        expect($type->getTitleAttribute())->toBe('name');
    });

    it('throws `LogicException` when `getTitleAttribute()` is called without setting it', function (): void {
        $type = Type::make(User::class);

        $type->getTitleAttribute();
    })->throws(LogicException::class);

    it('returns fluent `$this` from `titleAttribute()`', function (): void {
        $type = Type::make(User::class);

        expect($type->titleAttribute('name'))->toBe($type);
    });
});

describe('search columns', function (): void {
    it('defaults `getSearchColumns()` to `[titleAttribute]`', function (): void {
        $type = Type::make(User::class)
            ->titleAttribute('name');

        expect($type->getSearchColumns())->toBe(['name']);
    });

    it('can set custom `searchColumns()`', function (): void {
        $type = Type::make(User::class)
            ->titleAttribute('name')
            ->searchColumns(['name', 'email']);

        expect($type->getSearchColumns())->toBe(['name', 'email']);
    });

    it('can clear `searchColumns()` with `null` to restore defaults', function (): void {
        $type = Type::make(User::class)
            ->titleAttribute('name')
            ->searchColumns(['name', 'email'])
            ->searchColumns(null);

        expect($type->getSearchColumns())->toBe(['name']);
    });
});

describe('options limit', function (): void {
    it('defaults `getOptionsLimit()` to `50`', function (): void {
        $type = Type::make(User::class);

        expect($type->getOptionsLimit())->toBe(50);
    });
});

describe('option label from record callback', function (): void {
    it('defaults `hasOptionLabelFromRecordUsingCallback()` to `false`', function (): void {
        $type = Type::make(User::class);

        expect($type->hasOptionLabelFromRecordUsingCallback())->toBeFalse();
    });

    it('returns `true` for `hasOptionLabelFromRecordUsingCallback()` when set', function (): void {
        $type = Type::make(User::class)
            ->getOptionLabelFromRecordUsing(static fn (User $record): string => $record->name);

        expect($type->hasOptionLabelFromRecordUsingCallback())->toBeTrue();
    });

    it('can clear `getOptionLabelFromRecordUsing()` with `null`', function (): void {
        $type = Type::make(User::class)
            ->getOptionLabelFromRecordUsing(static fn (User $record): string => $record->name)
            ->getOptionLabelFromRecordUsing(null);

        expect($type->hasOptionLabelFromRecordUsingCallback())->toBeFalse();
    });
});

describe('modify key select callback', function (): void {
    it('returns `null` for `getModifyKeySelectUsingCallback()` by default', function (): void {
        $type = Type::make(User::class);

        expect($type->getModifyKeySelectUsingCallback())->toBeNull();
    });

    it('can set `modifyKeySelectUsing()` callback', function (): void {
        $callback = static fn () => null;
        $type = Type::make(User::class)
            ->modifyKeySelectUsing($callback);

        expect($type->getModifyKeySelectUsingCallback())->toBe($callback);
    });

    it('can clear `modifyKeySelectUsing()` with `null`', function (): void {
        $type = Type::make(User::class)
            ->modifyKeySelectUsing(static fn () => null)
            ->modifyKeySelectUsing(null);

        expect($type->getModifyKeySelectUsingCallback())->toBeNull();
    });
});

describe('forced case insensitive search', function (): void {
    it('defaults `isSearchForcedCaseInsensitive()` to `null`', function (): void {
        $type = Type::make(User::class);

        expect($type->isSearchForcedCaseInsensitive())->toBeNull();
    });

    it('can set `forceSearchCaseInsensitive()`', function (): void {
        $type = Type::make(User::class)
            ->forceSearchCaseInsensitive();

        expect($type->isSearchForcedCaseInsensitive())->toBeTrue();
    });

    it('can set `forceSearchCaseInsensitive()` to `false`', function (): void {
        $type = Type::make(User::class)
            ->forceSearchCaseInsensitive(false);

        expect($type->isSearchForcedCaseInsensitive())->toBeFalse();
    });

    it('can clear `forceSearchCaseInsensitive()` with `null`', function (): void {
        $type = Type::make(User::class)
            ->forceSearchCaseInsensitive()
            ->forceSearchCaseInsensitive(null);

        expect($type->isSearchForcedCaseInsensitive())->toBeNull();
    });
});

describe('fluent API', function (): void {
    it('returns fluent `$this` from `model()`', function (): void {
        $type = Type::make(User::class);

        expect($type->model(Post::class))->toBe($type);
        expect($type->getModel())->toBe(Post::class);
    });

    it('returns fluent `$this` from `modifyOptionsQueryUsing()`', function (): void {
        $type = Type::make(User::class);

        expect($type->modifyOptionsQueryUsing(static fn ($query) => $query))->toBe($type);
    });
});

describe('`getOptionsUsing` closure', function (): void {
    it('returns options keyed by ID with title attribute values', function (): void {
        $users = User::factory()->count(3)->create();

        $type = Type::make(User::class)->titleAttribute('name');

        Schema::make(Livewire::make())
            ->statePath('data')
            ->components([
                $select = Select::make('user_id')->preload(),
            ])
            ->fill();

        $options = $select->evaluate($type->getOptionsUsing);

        expect($options)->toHaveCount(3);
        expect($options[$users[0]->id])->toBe($users[0]->name);
        expect($options[$users[1]->id])->toBe($users[1]->name);
    });

    it('returns options using `getOptionLabelFromRecordUsing()` when set', function (): void {
        $users = User::factory()->count(2)->create();

        $type = Type::make(User::class)
            ->titleAttribute('name')
            ->getOptionLabelFromRecordUsing(static fn (User $record): string => "User: {$record->name}");

        Schema::make(Livewire::make())
            ->statePath('data')
            ->components([
                $select = Select::make('user_id')->preload(),
            ])
            ->fill();

        $options = $select->evaluate($type->getOptionsUsing);

        expect($options)->toHaveCount(2);
        expect($options[$users[0]->id])->toBe("User: {$users[0]->name}");
    });

    it('applies `modifyOptionsQueryUsing()` to filter options', function (): void {
        User::factory()->create(['name' => 'Alice']);
        User::factory()->create(['name' => 'Bob']);
        User::factory()->create(['name' => 'Charlie']);

        $type = Type::make(User::class)
            ->titleAttribute('name')
            ->modifyOptionsQueryUsing(static fn ($query) => $query->where('name', 'like', 'A%'));

        Schema::make(Livewire::make())
            ->statePath('data')
            ->components([
                $select = Select::make('user_id')->preload(),
            ])
            ->fill();

        $options = $select->evaluate($type->getOptionsUsing);

        expect($options)->toHaveCount(1);
        expect(array_values($options)[0])->toBe('Alice');
    });

    it('returns `null` when searchable and not preloaded', function (): void {
        User::factory()->create();

        $type = Type::make(User::class)->titleAttribute('name');

        Schema::make(Livewire::make())
            ->statePath('data')
            ->components([
                $select = Select::make('user_id')->searchable(),
            ])
            ->fill();

        $options = $select->evaluate($type->getOptionsUsing);

        expect($options)->toBeNull();
    });
});

describe('`getOptionLabelUsing` closure', function (): void {
    it('resolves label from title attribute for a given value', function (): void {
        $user = User::factory()->create(['name' => 'Jane Doe']);

        $type = Type::make(User::class)->titleAttribute('name');

        Schema::make(Livewire::make())
            ->statePath('data')
            ->components([
                $select = Select::make('user_id'),
            ])
            ->fill();

        $label = $select->evaluate($type->getOptionLabelUsing, ['value' => $user->id]);

        expect($label)->toBe('Jane Doe');
    });

    it('returns `null` when record does not exist', function (): void {
        $type = Type::make(User::class)->titleAttribute('name');

        Schema::make(Livewire::make())
            ->statePath('data')
            ->components([
                $select = Select::make('user_id'),
            ])
            ->fill();

        $label = $select->evaluate($type->getOptionLabelUsing, ['value' => 99999]);

        expect($label)->toBeNull();
    });

    it('uses `getOptionLabelFromRecordUsing()` when set', function (): void {
        $user = User::factory()->create(['name' => 'Custom User']);

        $type = Type::make(User::class)
            ->titleAttribute('name')
            ->getOptionLabelFromRecordUsing(static fn (User $record): string => "User: {$record->name}");

        Schema::make(Livewire::make())
            ->statePath('data')
            ->components([
                $select = Select::make('user_id'),
            ])
            ->fill();

        $label = $select->evaluate($type->getOptionLabelUsing, ['value' => $user->id]);

        expect($label)->toBe("User: {$user->name}");
    });

    it('applies `modifyOptionsQueryUsing()` to label lookup', function (): void {
        $user = User::factory()->create(['name' => 'Test User']);

        $type = Type::make(User::class)
            ->titleAttribute('name')
            ->modifyOptionsQueryUsing(static fn ($query) => $query->where('name', 'like', 'Nonexistent%'));

        Schema::make(Livewire::make())
            ->statePath('data')
            ->components([
                $select = Select::make('user_id'),
            ])
            ->fill();

        // modifyOptionsQueryUsing filters out the record
        $label = $select->evaluate($type->getOptionLabelUsing, ['value' => $user->id]);

        expect($label)->toBeNull();
    });
});

describe('`getSearchResultsUsing` closure', function (): void {
    it('returns matching records by title attribute', function (): void {
        User::factory()->create(['name' => 'Alice Smith']);
        User::factory()->create(['name' => 'Bob Jones']);
        User::factory()->create(['name' => 'Alice Johnson']);

        $type = Type::make(User::class)->titleAttribute('name');

        Schema::make(Livewire::make())
            ->statePath('data')
            ->components([
                $select = Select::make('user_id')->searchable(),
            ])
            ->fill();

        $results = $select->evaluate($type->getSearchResultsUsing, ['search' => 'Alice']);

        expect($results)->toHaveCount(2);
        expect(array_values($results))->each->toContain('Alice');
    });

    it('searches across multiple columns when `searchColumns()` is set', function (): void {
        User::factory()->create(['name' => 'Alice', 'email' => 'alice@example.com']);
        User::factory()->create(['name' => 'Bob', 'email' => 'bob@example.com']);

        $type = Type::make(User::class)
            ->titleAttribute('name')
            ->searchColumns(['name', 'email']);

        Schema::make(Livewire::make())
            ->statePath('data')
            ->components([
                $select = Select::make('user_id')->searchable(),
            ])
            ->fill();

        $results = $select->evaluate($type->getSearchResultsUsing, ['search' => 'bob@']);

        expect($results)->toHaveCount(1);
        expect(array_values($results)[0])->toBe('Bob');
    });

    it('uses `getOptionLabelFromRecordUsing()` for search result labels', function (): void {
        User::factory()->create(['name' => 'Alice']);

        $type = Type::make(User::class)
            ->titleAttribute('name')
            ->getOptionLabelFromRecordUsing(static fn (User $record): string => "Custom: {$record->name}");

        Schema::make(Livewire::make())
            ->statePath('data')
            ->components([
                $select = Select::make('user_id')->searchable(),
            ])
            ->fill();

        $results = $select->evaluate($type->getSearchResultsUsing, ['search' => 'Alice']);

        expect($results)->toHaveCount(1);
        expect(array_values($results)[0])->toBe('Custom: Alice');
    });

    it('applies `modifyOptionsQueryUsing()` to search query', function (): void {
        User::factory()->create(['name' => 'Alice Smith']);
        User::factory()->create(['name' => 'Bob Jones']);

        $type = Type::make(User::class)
            ->titleAttribute('name')
            ->modifyOptionsQueryUsing(static fn ($query) => $query->where('name', 'Alice Smith'));

        Schema::make(Livewire::make())
            ->statePath('data')
            ->components([
                $select = Select::make('user_id')->searchable(),
            ])
            ->fill();

        // Search for "Smith" — without modifyOptionsQueryUsing would return 0 (no "Smith" alone),
        // but the query modification pre-filters to "Alice Smith", then search finds "Smith" in it
        $results = $select->evaluate($type->getSearchResultsUsing, ['search' => 'Smith']);

        expect($results)->toHaveCount(1);
        expect(array_values($results)[0])->toBe('Alice Smith');
    });
});
