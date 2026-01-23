<?php

namespace Filament\Tests\Infolists\Components;

use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Schemas\Schema;
use Filament\Tests\Fixtures\Models\Post;
use Filament\Tests\Fixtures\Models\User;
use Filament\Tests\TestCase;
use Livewire\Component;

use function Filament\Tests\livewire;

uses(TestCase::class);

it('can render', function (): void {
    livewire(TestComponentWithRepeatableEntry::class)
        ->assertSuccessful()
        ->assertSchemaComponentStateSet('items.0.name', 'First Item', 'infolist')
        ->assertSeeText('First Item')
        ->assertSchemaComponentStateSet('items.1.name', 'Second Item', 'infolist')
        ->assertSeeText('Second Item');
});

it('can render nested entries', function (): void {
    livewire(TestComponentWithNestedRepeatableEntry::class)
        ->assertSuccessful()
        ->assertSchemaComponentStateSet('users.0.name', 'John', 'infolist')
        ->assertSeeText('John')
        ->assertSchemaComponentStateSet('users.0.email', 'john@example.com', 'infolist')
        ->assertSeeText('john@example.com')
        ->assertSchemaComponentStateSet('users.1.name', 'Jane', 'infolist')
        ->assertSeeText('Jane')
        ->assertSchemaComponentStateSet('users.1.email', 'jane@example.com', 'infolist')
        ->assertSeeText('jane@example.com');
});

class TestComponentWithRepeatableEntry extends Component implements HasSchemas
{
    use InteractsWithSchemas;

    public function infolist(Schema $schema): Schema
    {
        return $schema
            ->state([
                'items' => [
                    ['name' => 'First Item'],
                    ['name' => 'Second Item'],
                ],
            ])
            ->components([
                RepeatableEntry::make('items')
                    ->schema([
                        TextEntry::make('name'),
                    ]),
            ]);
    }

    public function render(): string
    {
        return <<<'BLADE'
            <div>
                {{ $this->infolist }}
            </div>
            BLADE;
    }
}

class TestComponentWithNestedRepeatableEntry extends Component implements HasSchemas
{
    use InteractsWithSchemas;

    public function infolist(Schema $schema): Schema
    {
        return $schema
            ->state([
                'users' => [
                    ['name' => 'John', 'email' => 'john@example.com'],
                    ['name' => 'Jane', 'email' => 'jane@example.com'],
                ],
            ])
            ->components([
                RepeatableEntry::make('users')
                    ->schema([
                        TextEntry::make('name'),
                        TextEntry::make('email'),
                    ]),
            ]);
    }

    public function render(): string
    {
        return <<<'BLADE'
            <div>
                {{ $this->infolist }}
            </div>
            BLADE;
    }
}

it('can resolve state for `RepeatableEntry` within nested sections with `statePath()`', function (): void {
    livewire(TestRepeatableEntryInNestedSections::class)
        ->assertSuccessful()
        ->assertSchemaComponentStateSet('page.block.items.0.name', 'Item One', 'infolist')
        ->assertSeeText('Item One')
        ->assertSchemaComponentStateSet('page.block.items.1.name', 'Item Two', 'infolist')
        ->assertSeeText('Item Two');
});

class TestRepeatableEntryInNestedSections extends Component implements HasSchemas
{
    use InteractsWithSchemas;

    public function infolist(Schema $schema): Schema
    {
        return $schema
            ->state([
                'page' => [
                    'block' => [
                        'items' => [
                            ['name' => 'Item One'],
                            ['name' => 'Item Two'],
                        ],
                    ],
                ],
            ])
            ->components([
                Section::make('Page')
                    ->statePath('page')
                    ->schema([
                        Section::make('Block')
                            ->statePath('block')
                            ->schema([
                                RepeatableEntry::make('items')
                                    ->schema([
                                        TextEntry::make('name'),
                                    ]),
                            ]),
                    ]),
            ]);
    }

    public function render(): string
    {
        return <<<'BLADE'
            <div>
                {{ $this->infolist }}
            </div>
            BLADE;
    }
}

it('can resolve state for nested `RepeatableEntry`', function (): void {
    livewire(TestNestedRepeatableEntryState::class)
        ->assertSuccessful()
        ->assertSchemaComponentStateSet('users.0.name', 'User Alice', 'infolist')
        ->assertSeeText('User Alice')
        ->assertSchemaComponentStateSet('users.0.comments.0.text', 'Comment from Alice: Hello', 'infolist')
        ->assertSeeText('Comment from Alice: Hello')
        ->assertSchemaComponentStateSet('users.0.comments.1.text', 'Comment from Alice: World', 'infolist')
        ->assertSeeText('Comment from Alice: World')
        ->assertSchemaComponentStateSet('users.1.name', 'User Bob', 'infolist')
        ->assertSeeText('User Bob')
        ->assertSchemaComponentStateSet('users.1.comments.0.text', 'Comment from Bob: Hi', 'infolist')
        ->assertSeeText('Comment from Bob: Hi');
});

class TestNestedRepeatableEntryState extends Component implements HasSchemas
{
    use InteractsWithSchemas;

    public function infolist(Schema $schema): Schema
    {
        return $schema
            ->state([
                'users' => [
                    [
                        'name' => 'User Alice',
                        'comments' => [
                            ['text' => 'Comment from Alice: Hello'],
                            ['text' => 'Comment from Alice: World'],
                        ],
                    ],
                    [
                        'name' => 'User Bob',
                        'comments' => [
                            ['text' => 'Comment from Bob: Hi'],
                        ],
                    ],
                ],
            ])
            ->components([
                RepeatableEntry::make('users')
                    ->schema([
                        TextEntry::make('name'),
                        RepeatableEntry::make('comments')
                            ->schema([
                                TextEntry::make('text'),
                            ]),
                    ]),
            ]);
    }

    public function render(): string
    {
        return <<<'BLADE'
            <div>
                {{ $this->infolist }}
            </div>
            BLADE;
    }
}

it('can resolve state for deeply nested `RepeatableEntry` within sections', function (): void {
    livewire(TestDeeplyNestedRepeatableEntry::class)
        ->assertSuccessful()
        ->assertSchemaComponentStateSet('store.categories.0.name', 'Category A', 'infolist')
        ->assertSeeText('Category A')
        ->assertSchemaComponentStateSet('store.categories.0.products.0.name', 'Product 1', 'infolist')
        ->assertSeeText('Product 1')
        ->assertSchemaComponentStateSet('store.categories.0.products.0.variants.0.sku', 'SKU-001', 'infolist')
        ->assertSeeText('SKU-001')
        ->assertSchemaComponentStateSet('store.categories.0.products.0.variants.1.sku', 'SKU-002', 'infolist')
        ->assertSeeText('SKU-002')
        ->assertSchemaComponentStateSet('store.categories.0.products.1.name', 'Product 2', 'infolist')
        ->assertSeeText('Product 2');
});

class TestDeeplyNestedRepeatableEntry extends Component implements HasSchemas
{
    use InteractsWithSchemas;

    public function infolist(Schema $schema): Schema
    {
        return $schema
            ->state([
                'store' => [
                    'categories' => [
                        [
                            'name' => 'Category A',
                            'products' => [
                                [
                                    'name' => 'Product 1',
                                    'variants' => [
                                        ['sku' => 'SKU-001'],
                                        ['sku' => 'SKU-002'],
                                    ],
                                ],
                                [
                                    'name' => 'Product 2',
                                    'variants' => [],
                                ],
                            ],
                        ],
                    ],
                ],
            ])
            ->components([
                Section::make('Store')
                    ->statePath('store')
                    ->schema([
                        RepeatableEntry::make('categories')
                            ->schema([
                                TextEntry::make('name'),
                                RepeatableEntry::make('products')
                                    ->schema([
                                        TextEntry::make('name'),
                                        RepeatableEntry::make('variants')
                                            ->schema([
                                                TextEntry::make('sku'),
                                            ]),
                                    ]),
                            ]),
                    ]),
            ]);
    }

    public function render(): string
    {
        return <<<'BLADE'
            <div>
                {{ $this->infolist }}
            </div>
            BLADE;
    }
}

it('can resolve state for relationship-based `RepeatableEntry`', function (): void {
    $user = User::factory()
        ->has(Post::factory()->count(2)->sequence(
            ['title' => 'First Post', 'content' => 'Content 1'],
            ['title' => 'Second Post', 'content' => 'Content 2'],
        ), 'posts')
        ->create(['name' => 'Test User']);

    livewire(TestRelationshipRepeatableEntry::class, ['user' => $user])
        ->assertSuccessful()
        ->assertSchemaComponentStateSet('name', 'Test User', 'infolist')
        ->assertSeeText('Test User')
        ->assertSchemaComponentStateSet('posts.0.title', 'First Post', 'infolist')
        ->assertSeeText('First Post')
        ->assertSchemaComponentStateSet('posts.1.title', 'Second Post', 'infolist')
        ->assertSeeText('Second Post');
});

class TestRelationshipRepeatableEntry extends Component implements HasSchemas
{
    use InteractsWithSchemas;

    public User $user;

    public function mount(User $user): void
    {
        $this->user = $user;
    }

    public function infolist(Schema $schema): Schema
    {
        return $schema
            ->record($this->user)
            ->components([
                TextEntry::make('name'),
                RepeatableEntry::make('posts')
                    ->schema([
                        TextEntry::make('title'),
                    ]),
            ]);
    }

    public function render(): string
    {
        return <<<'BLADE'
            <div>
                {{ $this->infolist }}
            </div>
            BLADE;
    }
}

it('can resolve state for relationship-based `RepeatableEntry` within nested sections', function (): void {
    $user = User::factory()
        ->has(Post::factory()->count(2)->sequence(
            ['title' => 'Post A'],
            ['title' => 'Post B'],
        ), 'posts')
        ->create();

    livewire(TestRelationshipRepeatableEntryInSection::class, ['user' => $user])
        ->assertSuccessful()
        ->assertSchemaComponentStateSet('posts.0.title', 'Post A', 'infolist')
        ->assertSeeText('Post A')
        ->assertSchemaComponentStateSet('posts.1.title', 'Post B', 'infolist')
        ->assertSeeText('Post B');
});

class TestRelationshipRepeatableEntryInSection extends Component implements HasSchemas
{
    use InteractsWithSchemas;

    public User $user;

    public function mount(User $user): void
    {
        $this->user = $user;
    }

    public function infolist(Schema $schema): Schema
    {
        return $schema
            ->record($this->user)
            ->components([
                Section::make('User Details')
                    ->schema([
                        TextEntry::make('name'),
                        Section::make('Posts')
                            ->schema([
                                RepeatableEntry::make('posts')
                                    ->schema([
                                        TextEntry::make('title'),
                                    ]),
                            ]),
                    ]),
            ]);
    }

    public function render(): string
    {
        return <<<'BLADE'
            <div>
                {{ $this->infolist }}
            </div>
            BLADE;
    }
}

it('correctly asserts entry state within `RepeatableEntry` using `assertSchemaComponentStateSet()`', function (): void {
    livewire(TestRepeatableEntryStateAssertions::class)
        ->assertSuccessful()
        ->assertSchemaComponentStateSet('items.0.name', 'First', 'infolist')
        ->assertSeeText('First')
        ->assertSchemaComponentStateSet('items.1.name', 'Second', 'infolist')
        ->assertSeeText('Second');
});

class TestRepeatableEntryStateAssertions extends Component implements HasSchemas
{
    use InteractsWithSchemas;

    public function infolist(Schema $schema): Schema
    {
        return $schema
            ->state([
                'items' => [
                    ['name' => 'First'],
                    ['name' => 'Second'],
                ],
            ])
            ->components([
                RepeatableEntry::make('items')
                    ->schema([
                        TextEntry::make('name'),
                    ]),
            ]);
    }

    public function render(): string
    {
        return <<<'BLADE'
            <div>
                {{ $this->infolist }}
            </div>
            BLADE;
    }
}

it('correctly asserts nested `RepeatableEntry` state using `assertSchemaComponentStateSet()`', function (): void {
    livewire(TestNestedRepeatableEntryStateAssertions::class)
        ->assertSuccessful()
        ->assertSchemaComponentStateSet('users.0.name', 'Alice', 'infolist')
        ->assertSeeText('Alice')
        ->assertSchemaComponentStateSet('users.0.posts.0.title', 'Post A', 'infolist')
        ->assertSeeText('Post A')
        ->assertSchemaComponentStateSet('users.0.posts.1.title', 'Post B', 'infolist')
        ->assertSeeText('Post B');
});

class TestNestedRepeatableEntryStateAssertions extends Component implements HasSchemas
{
    use InteractsWithSchemas;

    public function infolist(Schema $schema): Schema
    {
        return $schema
            ->state([
                'users' => [
                    [
                        'name' => 'Alice',
                        'posts' => [
                            ['title' => 'Post A'],
                            ['title' => 'Post B'],
                        ],
                    ],
                ],
            ])
            ->components([
                RepeatableEntry::make('users')
                    ->schema([
                        TextEntry::make('name'),
                        RepeatableEntry::make('posts')
                            ->schema([
                                TextEntry::make('title'),
                            ]),
                    ]),
            ]);
    }

    public function render(): string
    {
        return <<<'BLADE'
            <div>
                {{ $this->infolist }}
            </div>
            BLADE;
    }
}

it('can render `RepeatableEntry` within form schema', function (): void {
    livewire(TestRepeatableEntryInForm::class)
        ->assertSuccessful()
        ->assertSchemaComponentStateSet('tags.0.label', 'Tag One', 'form')
        ->assertSeeText('Tag One')
        ->assertSchemaComponentStateSet('tags.1.label', 'Tag Two', 'form')
        ->assertSeeText('Tag Two');
});

class TestRepeatableEntryInForm extends Component implements HasSchemas
{
    use InteractsWithSchemas;

    public array $data = [];

    public function mount(): void
    {
        $this->form->fill([
            'name' => 'Test Record',
        ]);
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->statePath('data')
            ->components([
                TextInput::make('name'),
                Section::make('Tags')
                    ->schema([
                        RepeatableEntry::make('tags')
                            ->state([
                                ['label' => 'Tag One'],
                                ['label' => 'Tag Two'],
                            ])
                            ->schema([
                                TextEntry::make('label'),
                            ]),
                    ]),
            ]);
    }

    public function render(): string
    {
        return <<<'BLADE'
            <div>
                {{ $this->form }}
            </div>
            BLADE;
    }
}

