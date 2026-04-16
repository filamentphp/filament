## Filament

- Filament is used by this application. Follow the existing conventions for how and where it is implemented.
- Filament is a Server-Driven UI (SDUI) framework for Laravel that lets you define user interfaces in PHP using structured configuration objects. Built on Livewire, Alpine.js, and Tailwind CSS.
- Use the `search-docs` tool for official documentation on Artisan commands, code examples, testing, relationships, and idiomatic practices. If `search-docs` is unavailable, refer to https://filamentphp.com/docs.

### Artisan

- Always use Filament-specific Artisan commands to create files. Find available commands with the `list-artisan-commands` tool, or run `php artisan --help`.
- Always inspect required options before running a command, and always pass `--no-interaction`.

### Patterns

Always use static `make()` methods to initialize components. Most configuration methods accept a `Closure` for dynamic values.

Use `Get $get` to read other form field values for conditional logic:

@verbatim
<code-snippet name="Conditional form field visibility" lang="php">
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Utilities\Get;

Select::make('type')
    ->options(CompanyType::class)
    ->required()
    ->live(),

TextInput::make('company_name')
    ->required()
    ->visible(fn (Get $get): bool => $get('type') === 'business'),

</code-snippet>
@endverbatim

Use `Set $set` in `afterStateUpdated` to reactively mutate another field's value:

@verbatim
<code-snippet name="Reactive field mutation with Set" lang="php">
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Utilities\Set;
use Illuminate\Support\Str;

TextInput::make('title')
    ->required()
    ->live(onBlur: true)
    ->afterStateUpdated(fn (Set $set, ?string $state) => $set(
        'slug',
        Str::slug($state ?? ''),
    )),

TextInput::make('slug')
    ->required(),

</code-snippet>
@endverbatim

Use `state()` with a `Closure` to compute derived column values:

@verbatim
<code-snippet name="Computed table column value" lang="php">
use Filament\Tables\Columns\TextColumn;

TextColumn::make('full_name')
    ->state(fn (User $record): string => "{$record->first_name} {$record->last_name}"),

</code-snippet>
@endverbatim

Use `Section` and `Grid` for layout. Always set `->columns()` on the grid and `->columnSpan()` on fields explicitly — they do not span full-width by default:

@verbatim
<code-snippet name="Section and Grid layout" lang="php">
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;

Section::make('Details')
    ->schema([
        Grid::make(2)
            ->schema([
                TextInput::make('first_name')
                    ->required()
                    ->columnSpan(1),

                TextInput::make('last_name')
                    ->required()
                    ->columnSpan(1),

                TextInput::make('bio')
                    ->columnSpanFull(),
            ]),
    ]),

</code-snippet>
@endverbatim

Use `Repeater` to manage a `HasMany` relationship inline. Always use `->schema()`, never `->fields()`:

@verbatim
<code-snippet name="Repeater for HasMany" lang="php">
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\TextInput;

Repeater::make('qualifications')
    ->relationship()
    ->schema([
        TextInput::make('institution')
            ->required(),
        TextInput::make('qualification')
            ->required(),
    ])
    ->columns(2),

</code-snippet>
@endverbatim

Use `SelectFilter` for enum or relationship-based filtering, and `Filter` with a query closure for custom logic:

@verbatim
<code-snippet name="Table filters" lang="php">
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;

SelectFilter::make('status')
    ->options(UserStatus::class),

SelectFilter::make('author')
    ->relationship('author', 'name'),

Filter::make('verified')
    ->query(fn (Builder $query) => $query->whereNotNull('email_verified_at')),

</code-snippet>
@endverbatim

Actions encapsulate a button with an optional modal form and logic:

@verbatim
<code-snippet name="Action with modal form" lang="php">
use Filament\Actions\Action;
use Filament\Forms\Components\TextInput;

Action::make('updateEmail')
    ->schema([
        TextInput::make('email')
            ->email()
            ->required(),
    ])
    ->action(fn (array $data, User $record) => $record->update($data))

</code-snippet>
@endverbatim

### Testing

Always authenticate before testing panel functionality using `actingAs()`. Filament uses Livewire, so use `livewire()` (available when `pestphp/pest-plugin-livewire` is in `composer.json`):

@verbatim
<code-snippet name="Table test" lang="php">
use function Pest\Livewire\livewire;

$user = User::factory()->create();

$this->actingAs($user);

livewire(ListUsers::class)
    ->assertCanSeeTableRecords($users)
    ->searchTable($users->first()->name)
    ->assertCanSeeTableRecords($users->take(1))
    ->assertCanNotSeeTableRecords($users->skip(1));

</code-snippet>

<code-snippet name="Create resource test" lang="php">
use function Pest\Laravel\assertDatabaseHas;
use function Pest\Livewire\livewire;

$this->actingAs(User::factory()->create());

livewire(CreateUser::class)
    ->fillForm([
        'name' => 'Test',
        'email' => 'test@example.com',
    ])
    ->call('create')
    ->assertNotified()
    ->assertRedirect();

assertDatabaseHas(User::class, [
    'name' => 'Test',
    'email' => 'test@example.com',
]);

</code-snippet>

<code-snippet name="Edit resource test" lang="php">
use function Pest\Laravel\assertDatabaseHas;
use function Pest\Livewire\livewire;

$this->actingAs(User::factory()->create());

livewire(EditUser::class, ['record' => $user->id])
    ->fillForm([
        'name' => 'Updated Name',
        'email' => 'updated@example.com',
    ])
    ->call('save')
    ->assertNotified()
    ->assertHasNoFormErrors();

assertDatabaseHas(User::class, [
    'id' => $user->id,
    'name' => 'Updated Name',
    'email' => 'updated@example.com',
]);

</code-snippet>

<code-snippet name="Testing validation" lang="php">
use function Pest\Livewire\livewire;

livewire(CreateUser::class)
    ->fillForm([
        'name' => null,
        'email' => 'invalid-email',
    ])
    ->call('create')
    ->assertHasFormErrors([
        'name' => 'required',
        'email' => 'email',
    ])
    ->assertNotNotified();

</code-snippet>

<code-snippet name="Calling actions in pages" lang="php">
use Filament\Actions\DeleteAction;
use function Pest\Livewire\livewire;

livewire(EditUser::class, ['record' => $user->id])
    ->callAction(DeleteAction::class)
    ->assertNotified()
    ->assertRedirect();

</code-snippet>

<code-snippet name="Calling actions in tables" lang="php">
use Filament\Actions\Testing\TestAction;
use function Pest\Livewire\livewire;

livewire(ListUsers::class)
    ->callAction(TestAction::make('promote')->table($user), [
        'role' => 'admin',
    ])
    ->assertNotified();

</code-snippet>
@endverbatim

### Correct Namespaces

- Form fields (`TextInput`, `Select`, `Repeater`, etc.): `Filament\Forms\Components\`
- Infolist entries (`TextEntry`, `IconEntry`, etc.): `Filament\Infolists\Components\`
- Layout components (`Grid`, `Section`, `Fieldset`, `Tabs`, `Wizard`, etc.): `Filament\Schemas\Components\`
- Schema utilities (`Get`, `Set`, etc.): `Filament\Schemas\Components\Utilities\`
- Actions (`DeleteAction`, `CreateAction`, etc.): `Filament\Actions\`. Never use `Filament\Tables\Actions\`, `Filament\Forms\Actions\`, or any other sub-namespace for actions.
- Icons: `Filament\Support\Icons\Heroicon` enum (e.g., `Heroicon::PencilSquare`)

### Common Mistakes

- **Never assume public file visibility.** File visibility is `private` by default. Always use `->visibility('public')` when public access is needed.
- **Never assume full-width layout.** `Grid`, `Section`, and `Fieldset` do not span all columns by default. Explicitly set column spans when needed.
- **Use `Select::make()->relationship()` for BelongsTo fields.** Never use a separate `BelongsToSelect` component — it does not exist in v4. The correct pattern is `Select::make('author_id')->relationship('author', 'name')`.
- **Use `->schema()` on `Repeater`, not `->fields()`.** The method `->fields()` does not exist. Always use `->schema([])`.
- **Never add `->dehydrated(false)` to fields that need to be saved.** This removes the field value from the form state and the data will not be passed to `->action()` or the save handler. Only use it for helper/UI-only fields.
- **Use correct property types when overriding Page, Resource, and Widget properties.** These properties have union types or changed modifiers that must be preserved:
    - `$navigationIcon`: `protected static string | BackedEnum | null` (not `?string`)
    - `$navigationGroup`: `protected static string | UnitEnum | null` (not `?string`)
    - `$view`: `protected string` (not `protected static string`) on Page and Widget classes
