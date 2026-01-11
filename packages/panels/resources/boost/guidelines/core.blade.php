## Filament
- Filament is used by this application. Follow existing conventions for how and where it's implemented.
- Filament is a Server-Driven UI (SDUI) framework for Laravel that lets you define user interfaces in PHP using structured configuration objects. Built on Livewire, Alpine.js, and Tailwind CSS.
- Use the `search-docs` tool for official documentation on Artisan commands, code examples, testing, relationships, and idiomatic practices.

### Artisan
- Use Filament-specific Artisan commands to create files. Find them with `list-artisan-commands` or `php artisan --help`.
- Inspect required options and always pass `--no-interaction`.

### Features
- **Panels**: The top-level container that includes pages, resources, forms, tables, notifications, actions, infolists, and widgets.
- **Resources**: Static classes for building CRUD interfaces for Eloquent models. Located in `app/Filament/Resources`.
- **Forms**: Dynamic forms rendered in resources, action modals, table filters, and more.
- **Tables**: Interactive tables with filtering, sorting, and pagination.
- **Actions**: Buttons or links that encapsulate UI (modal windows) and logic. Used for one-time operations like deleting records, sending emails, or updating data via modal form input.
- **Infolists**: Read-only data displays.
- **Notifications**: Flash notifications for users.
- **Schemas**: Components that define UI structure and behavior for forms, tables, or lists.
- **Widgets**: Dashboard components for charts, stats, and tables.

### Patterns
Use static `make()` methods to initialize components. Most configuration methods accept a `Closure` for dynamic values:
@verbatim
<code-snippet name="Closure example" lang="php">
TextInput::make('email')
    ->required(fn () => auth()->check())
    ->visible(fn (Get $get) => $get('type') === 'contact');
</code-snippet>
@endverbatim

Use the `relationship()` method on form components when binding to Eloquent relationships:
@verbatim
<code-snippet name="Relationship example" lang="php">
Forms\Components\Select::make('user_id')
    ->label('Author')
    ->relationship('author')
    ->required(),

Repeater::make('addresses')
    ->relationship()
    ->schema([...]),
</code-snippet>
@endverbatim

### Common Mistakes

**Namespaces:**
- Layout components (Grid, Section, Fieldset, Tabs, Wizard): `Filament\Schemas\Components\`
- Form fields (TextInput, Select, etc.): `Filament\Forms\Components\`
- Table columns: `Filament\Tables\Columns\`
- Table filters: `Filament\Tables\Filters\`
- Actions: `Filament\Actions\` (no `Filament\Tables\Actions\`)
- Icons: `Filament\Support\Icons\Heroicon` enum (e.g., `Heroicon::PencilSquare`)

**Recent breaking changes to Filament:**
- File visibility is `private` by default. Use `->visibility('public')` for public access.
- `Grid`, `Section`, and `Fieldset` no longer span all columns by default.

### Testing
Authenticate before testing panel functionality. Filament uses Livewire, so use `livewire()` or `Livewire::test()`:
@verbatim
<code-snippet name="Filament Table Test" lang="php">
    livewire(ListUsers::class)
        ->assertCanSeeTableRecords($users)
        ->searchTable($users->first()->name)
        ->assertCanSeeTableRecords($users->take(1))
        ->assertCanNotSeeTableRecords($users->skip(1))
        ->searchTable($users->last()->email)
        ->assertCanSeeTableRecords($users->take(-1))
        ->assertCanNotSeeTableRecords($users->take($users->count() - 1));
</code-snippet>

<code-snippet name="Filament Create Resource Test" lang="php">
    livewire(CreateUser::class)
        ->fillForm([
            'name' => 'Howdy',
            'email' => 'howdy@example.com',
        ])
        ->call('create')
        ->assertNotified()
        ->assertRedirect();

    assertDatabaseHas(User::class, [
        'name' => 'Howdy',
        'email' => 'howdy@example.com',
    ]);
</code-snippet>

<code-snippet name="Testing Multiple Panels" lang="php">
    use Filament\Facades\Filament;

    Filament::setCurrentPanel('app');
</code-snippet>

<code-snippet name="Calling an Action" lang="php">
    livewire(EditInvoice::class, [
        'invoice' => $invoice,
    ])->callAction('send');

    expect($invoice->refresh())->isSent()->toBeTrue();
</code-snippet>
@endverbatim
