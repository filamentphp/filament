<?php

use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\RepeatableEntry\TableColumn;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Schemas\Schema;
use Filament\Support\Enums\Alignment;
use Filament\Tests\TestCase;
use Illuminate\Database\Eloquent\Model;
use Livewire\Component;

use function Filament\Tests\livewire;

uses(TestCase::class);

it('generates complete table HTML structure', function (): void {
    TestRepeatableInfolistComponent::$schema = [
        TextEntry::make('name'),
        TextEntry::make('price'),
        TextEntry::make('category'),
    ];
    TestRepeatableInfolistComponent::$table = [
        TableColumn::make('Product Name'),
        TableColumn::make('Price'),
        TableColumn::make('Category'),
    ];
    TestRepeatableInfolistComponent::$state = [
        ['name' => 'Laptop', 'price' => '$999', 'category' => 'Electronics'],
        ['name' => 'Book', 'price' => '$29', 'category' => 'Education'],
    ];

    livewire(TestRepeatableInfolistComponent::class)
        ->assertSee('fi-in-repeatable')
        ->assertSee('fi-in-repeatable-table')
        ->assertSee('fi-in-repeatable-table-element')
        ->assertSee('Product Name')
        ->assertSee('Price')
        ->assertSee('Category')
        ->assertSee('Laptop')
        ->assertSee('$999')
        ->assertSee('Electronics')
        ->assertSee('Book')
        ->assertSee('$29')
        ->assertSee('Education');
});

it('applies correct CSS classes for styling', function (): void {
    TestRepeatableInfolistComponent::$schema = [
        TextEntry::make('name'),
    ];
    TestRepeatableInfolistComponent::$table = [
        TableColumn::make('Name')->alignment(Alignment::Center),
    ];
    TestRepeatableInfolistComponent::$contained = true;
    TestRepeatableInfolistComponent::$state = [
        ['name' => 'Test Item'],
    ];

    livewire(TestRepeatableInfolistComponent::class)
        ->assertSee('fi-in-repeatable')
        ->assertSee('fi-in-repeatable-table')
        ->assertSee('fi-contained')
        ->assertSee('fi-in-repeatable-table-element')
        ->assertSee('fi-align-center');
});

it('handles complex nested data structures', function (): void {
    TestRepeatableInfolistComponent::$schema = [
        TextEntry::make('customer.name'),
        TextEntry::make('total'),
        TextEntry::make('status'),
    ];
    TestRepeatableInfolistComponent::$table = [
        TableColumn::make('Customer'),
        TableColumn::make('Total'),
        TableColumn::make('Status'),
    ];
    TestRepeatableInfolistComponent::$state = [
        [
            'customer' => ['name' => 'John Doe'],
            'total' => '$150.00',
            'status' => 'Completed',
        ],
        [
            'customer' => ['name' => 'Jane Smith'],
            'total' => '$75.50',
            'status' => 'Pending',
        ],
    ];

    livewire(TestRepeatableInfolistComponent::class)
        ->assertSee('John Doe')
        ->assertSee('$150.00')
        ->assertSee('Completed')
        ->assertSee('Jane Smith')
        ->assertSee('$75.50')
        ->assertSee('Pending');
});

it('validates HTML output structure', function (): void {
    TestRepeatableInfolistComponent::$schema = [
        TextEntry::make('name'),
        TextEntry::make('value'),
    ];
    TestRepeatableInfolistComponent::$table = [
        TableColumn::make('Name'),
        TableColumn::make('Value'),
    ];
    TestRepeatableInfolistComponent::$state = [
        ['name' => 'Item 1', 'value' => 'Value 1'],
        ['name' => 'Item 2', 'value' => 'Value 2'],
    ];

    // Instead of counting exact tags, assert key structural markers are present
    livewire(TestRepeatableInfolistComponent::class)
        ->assertSee('fi-in-repeatable-table')
        ->assertSee('<table', false)
        ->assertSee('<thead', false)
        ->assertSee('<tbody>', false)
        ->assertSee('Name')
        ->assertSee('Value');
});

it('handles edge case with empty collections', function (): void {
    TestRepeatableInfolistComponent::$schema = [
        TextEntry::make('name'),
    ];
    TestRepeatableInfolistComponent::$table = [
        TableColumn::make('Name'),
    ];
    TestRepeatableInfolistComponent::$state = [];
    TestRepeatableInfolistComponent::$placeholder = 'No data available';

    livewire(TestRepeatableInfolistComponent::class)
        ->assertSee('No data available')
        ->assertSee('<table', false)
        ->assertSee('<thead', false)
        ->assertSee('Name');
});

it('handles edge case with null state', function (): void {
    TestRepeatableInfolistComponent::$schema = [
        TextEntry::make('name'),
    ];
    TestRepeatableInfolistComponent::$table = [
        TableColumn::make('Name'),
    ];
    TestRepeatableInfolistComponent::$state = null;

    livewire(TestRepeatableInfolistComponent::class)
        ->assertSee('<table', false)
        ->assertSee('Name');
});

it('handles mismatched schema and column counts', function (): void {
    TestRepeatableInfolistComponent::$schema = [
        TextEntry::make('name'),
        TextEntry::make('email'),
        TextEntry::make('phone'),
    ];
    TestRepeatableInfolistComponent::$table = [
        TableColumn::make('Name'),
        TableColumn::make('Email'),
        // Missing phone column
    ];
    TestRepeatableInfolistComponent::$state = [
        ['name' => 'John', 'email' => 'john@example.com', 'phone' => '123-456-7890'],
    ];

    livewire(TestRepeatableInfolistComponent::class)
        ->assertSee('Name')
        ->assertSee('Email')
        ->assertSee('John')
        ->assertSee('john@example.com');
});

it('maintains accessibility with screen reader support', function (): void {
    TestRepeatableInfolistComponent::$schema = [
        TextEntry::make('name'),
        TextEntry::make('actions'),
    ];
    TestRepeatableInfolistComponent::$table = [
        TableColumn::make('Name'),
        TableColumn::make('Actions')->hiddenHeaderLabel(),
    ];
    TestRepeatableInfolistComponent::$state = [
        ['name' => 'Item 1', 'actions' => 'Edit | Delete'],
    ];

    livewire(TestRepeatableInfolistComponent::class)
        ->assertSee('fi-sr-only')
        ->assertSee('Actions');
});

it('handles special characters and HTML entities', function (): void {
    TestRepeatableInfolistComponent::$schema = [
        TextEntry::make('name'),
        TextEntry::make('description'),
    ];
    TestRepeatableInfolistComponent::$table = [
        TableColumn::make('Name'),
        TableColumn::make('Description'),
    ];
    TestRepeatableInfolistComponent::$state = [
        [
            'name' => 'Item with <script>',
            'description' => 'Description with "quotes" & ampersands',
        ],
    ];

    livewire(TestRepeatableInfolistComponent::class)
        ->assertSee('&lt;script&gt;', false)
        ->assertSee('&quot;quotes&quot;', false)
        ->assertSee('&amp; ampersands', false);
});

it('works with dynamic column configuration', function (): void {
    $showEmail = true;

    TestRepeatableInfolistComponent::$schema = [
        TextEntry::make('name'),
        TextEntry::make('email'),
    ];
    TestRepeatableInfolistComponent::$table = function () use ($showEmail) {
        $columns = [TableColumn::make('Name')];

        if ($showEmail) {
            $columns[] = TableColumn::make('Email');
        }

        return $columns;
    };
    TestRepeatableInfolistComponent::$state = [
        ['name' => 'John Doe', 'email' => 'john@example.com'],
    ];

    livewire(TestRepeatableInfolistComponent::class)
        ->assertSee('Name')
        ->assertSee('Email')
        ->assertSee('John Doe')
        ->assertSee('john@example.com');
});

it('renders table with array data', function (): void {
    TestRepeatableInfolistComponent::$schema = [
        TextEntry::make('name'),
        TextEntry::make('email'),
        TextEntry::make('role'),
    ];
    TestRepeatableInfolistComponent::$table = [
        TableColumn::make('Name'),
        TableColumn::make('Email'),
        TableColumn::make('Role'),
    ];
    TestRepeatableInfolistComponent::$state = [
        ['name' => 'John Doe', 'email' => 'john@example.com', 'role' => 'Admin'],
        ['name' => 'Jane Smith', 'email' => 'jane@example.com', 'role' => 'User'],
        ['name' => 'Bob Johnson', 'email' => 'bob@example.com', 'role' => 'Editor'],
    ];

    livewire(TestRepeatableInfolistComponent::class)
        ->assertSee('John Doe')
        ->assertSee('john@example.com')
        ->assertSee('Admin')
        ->assertSee('Jane Smith')
        ->assertSee('jane@example.com')
        ->assertSee('User')
        ->assertSee('Bob Johnson')
        ->assertSee('bob@example.com')
        ->assertSee('Editor');
});

it('renders table with model data', function (): void {
    $users = collect([
        new TestUser(['name' => 'John Doe', 'email' => 'john@example.com']),
        new TestUser(['name' => 'Jane Smith', 'email' => 'jane@example.com']),
    ]);

    TestRepeatableInfolistComponent::$schema = [
        TextEntry::make('name'),
        TextEntry::make('email'),
    ];
    TestRepeatableInfolistComponent::$table = [
        TableColumn::make('Name'),
        TableColumn::make('Email'),
    ];
    TestRepeatableInfolistComponent::$state = $users->all();

    livewire(TestRepeatableInfolistComponent::class)
        ->assertSee('John Doe')
        ->assertSee('john@example.com')
        ->assertSee('Jane Smith')
        ->assertSee('jane@example.com');
});

it('handles empty state in table mode', function (): void {
    TestRepeatableInfolistComponent::$schema = [
        TextEntry::make('name'),
    ];
    TestRepeatableInfolistComponent::$table = [
        TableColumn::make('Name'),
    ];
    TestRepeatableInfolistComponent::$state = [];
    TestRepeatableInfolistComponent::$placeholder = 'No items found';

    livewire(TestRepeatableInfolistComponent::class)
        ->assertSee('No items found')
        ->assertSee('<table', false)
        ->assertSee('<thead', false)
        ->assertSee('Name');
});

it('applies column alignment in table', function (): void {
    TestRepeatableInfolistComponent::$schema = [
        TextEntry::make('name'),
        TextEntry::make('amount'),
    ];
    TestRepeatableInfolistComponent::$table = [
        TableColumn::make('Name')->alignment(Alignment::Left),
        TableColumn::make('Amount')->alignment(Alignment::Right),
    ];
    TestRepeatableInfolistComponent::$state = [
        ['name' => 'Item 1', 'amount' => '$100'],
    ];

    livewire(TestRepeatableInfolistComponent::class)
        ->assertSee('fi-align-left')
        ->assertSee('fi-align-right');
});

it('applies column width in table', function (): void {
    TestRepeatableInfolistComponent::$schema = [
        TextEntry::make('name'),
        TextEntry::make('description'),
    ];
    TestRepeatableInfolistComponent::$table = [
        TableColumn::make('Name')->width('200px'),
        TableColumn::make('Description')->width('auto'),
    ];
    TestRepeatableInfolistComponent::$state = [
        ['name' => 'Item 1', 'description' => 'A test item'],
    ];

    livewire(TestRepeatableInfolistComponent::class)
        ->assertSee('width: 200px')
        ->assertSee('width: auto');
});

it('hides header labels when configured', function (): void {
    TestRepeatableInfolistComponent::$schema = [
        TextEntry::make('name'),
        TextEntry::make('secret'),
    ];
    TestRepeatableInfolistComponent::$table = [
        TableColumn::make('Name'),
        TableColumn::make('Secret')->hiddenHeaderLabel(),
    ];
    TestRepeatableInfolistComponent::$state = [
        ['name' => 'Item 1', 'secret' => 'hidden'],
    ];

    livewire(TestRepeatableInfolistComponent::class)
        ->assertSee('Name')
        ->assertSee('fi-sr-only')
        ->assertSee('Secret');
});

class TestUser extends Model
{
    protected $fillable = ['name', 'email'];

    public function attributesToArray()
    {
        return $this->attributes;
    }
}

class TestRepeatableInfolistComponent extends Component implements HasSchemas
{
    use InteractsWithSchemas;

    public static ?array $state = null;

    public static array $schema = [];

    public static $table = null;

    public static ?string $placeholder = null;

    public static bool $contained = false;

    public function infolist(Schema $schema): Schema
    {
        $entry = RepeatableEntry::make('items')
            ->schema(static::$schema);

        if (static::$table !== null) {
            $entry->table(static::$table);
        }

        if (static::$contained) {
            $entry->contained();
        }

        if (static::$placeholder !== null) {
            $entry->placeholder(static::$placeholder);
        }

        if (! is_null(static::$state)) {
            $entry->state(static::$state);
        }

        return $schema
            ->state([])
            ->components([
                $entry,
            ]);
    }

    public function render(): string
    {
        return '{{ $this->infolist }}';
    }
}
