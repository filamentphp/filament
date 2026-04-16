---
name: filament-development
description: "Activate when working with Filament admin panels, Resources, Forms, Tables, Actions, Notifications, or Widgets. Covers panel setup, scaffolding, common patterns, and Pest testing. Do not activate for plain Laravel or Livewire tasks unrelated to Filament."
license: MIT
metadata:
  author: filamentphp
---

## Documentation
Use `search-docs` to look up specific Filament v3 APIs and configuration options.

## Overview
Filament v3 is a server-driven UI toolkit for Laravel built on Livewire v3, Alpine.js, and Tailwind CSS. All components use a fluent, static `make()` API - never instantiate them with `new`.

## Scaffolding
Always generate Filament files through Artisan, never manually:

- `{{ $assist->artisanCommand('make:filament-resource Post --generate') }}` - Resource with form, table, and CRUD pages
- `{{ $assist->artisanCommand('make:filament-page Settings --resource=PostResource') }}` - Custom page attached to a resource
- `{{ $assist->artisanCommand('make:filament-widget StatsOverview --stats-overview') }}` - Stats overview widget
- `{{ $assist->artisanCommand('make:filament-widget LatestOrders --table') }}` - Table widget

## Panel Setup
Panels are configured in a service provider that extends `PanelProvider`. Register it in `bootstrap/providers.php` - not in `config/app.php`.

@boostsnippet("Panel Provider", "php")
class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->login()
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\Filament\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\Filament\Pages')
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\Filament\Widgets');
    }
}
@endboostsnippet

## Resources
Resources are static classes living in `app/Filament/Resources`. The `form()` and `table()` methods define the schema:

@boostsnippet("Resource Form", "php")
public static function form(Form $form): Form
{
    return $form->schema([
        TextInput::make('title')
            ->required()
            ->maxLength(255),
        RichEditor::make('content')
            ->columnSpanFull(),
        Select::make('status')
            ->options(['draft' => 'Draft', 'published' => 'Published'])
            ->default('draft'),
        Select::make('category_id')
            ->relationship('category', 'name')
            ->searchable()
            ->preload(),
    ]);
}
@endboostsnippet

@boostsnippet("Resource Table", "php")
public static function table(Table $table): Table
{
    return $table
        ->columns([
            TextColumn::make('title')->searchable()->sortable(),
            TextColumn::make('status')->badge(),
            TextColumn::make('created_at')
                ->dateTime()
                ->sortable()
                ->toggleable(isToggledHiddenByDefault: true),
        ])
        ->filters([
            SelectFilter::make('status')
                ->options(['draft' => 'Draft', 'published' => 'Published']),
        ])
        ->actions([EditAction::make(), DeleteAction::make()])
        ->bulkActions([DeleteBulkAction::make()]);
}
@endboostsnippet


## Forms
Use `relationship()` on `Select` instead of loading options manually. For uploads, use `FileUpload` not a text field:

@boostsnippet("Relationship Select", "php")
Select::make('author_id')
    ->relationship('author', 'name')
    ->searchable()
    ->preload()
    ->required(),
@endboostsnippet

@boostsnippet("File Upload", "php")
FileUpload::make('cover_image')
    ->image()
    ->disk('public')
    ->directory('covers'),
@endboostsnippet

## Actions
Actions can live on table rows, form headers, page headers, or inside modals:

@boostsnippet("Action with Confirmation", "php")
Action::make('publish')
    ->color('success')
    ->icon('heroicon-o-check-circle')
    ->requiresConfirmation()
    ->action(fn (Post $record) => $record->publish()),
@endboostsnippet

@boostsnippet("Action with Form Modal", "php")
Action::make('reject')
    ->color('danger')
    ->form([
        Textarea::make('reason')->required(),
    ])
    ->action(fn (Post $record, array $data) => $record->reject($data['reason'])),
@endboostsnippet

## Notifications
Send flash-style notifications from actions or Livewire component methods:

@boostsnippet("Success Notification", "php")
Notification::make()
    ->title('Changes saved')
    ->success()
    ->send();
@endboostsnippet

@boostsnippet("Database Notification", "php")
Notification::make()
    ->title('Your export is ready')
    ->body('Click to download.')
    ->success()
    ->sendToDatabase($user);
@endboostsnippet

## Widgets
Attach widgets to a panel or resource page via `getHeaderWidgets()` or `getFooterWidgets()`:

@boostsnippet("Stats Overview Widget", "php")
class PostStatsWidget extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Total', Post::count()),
            Stat::make('Published', Post::published()->count())
                ->color('success')
                ->chart([3, 7, 4, 9, 12]),
            Stat::make('Drafts', Post::draft()->count())
                ->color('warning'),
        ];
    }
}
@endboostsnippet


## Testing
All Filament tests require authentication. Use `livewire()` to drive Filament pages - do not call page URLs directly:

@boostsnippet("List records", "php")
it('lists posts', function () {
    actingAs(User::factory()->create());

    $posts = Post::factory()->count(5)->create();

    livewire(PostResource\Pages\ListPosts::class)
        ->assertCanSeeTableRecords($posts);
});
@endboostsnippet

@boostsnippet("Create a record", "php")
it('creates a post', function () {
    actingAs(User::factory()->create());

    livewire(PostResource\Pages\CreatePost::class)
        ->fillForm(['title' => 'Hello Filament'])
        ->call('create')
        ->assertHasNoFormErrors();

    assertDatabaseHas('posts', ['title' => 'Hello Filament']);
});
@endboostsnippet

@boostsnippet("Call a table action", "php")
it('publishes a post', function () {
    actingAs(User::factory()->create());
    $post = Post::factory()->create();

    livewire(PostResource\Pages\ListPosts::class)
        ->callTableAction('publish', $post);

    expect($post->refresh()->status)->toBe('published');
});
@endboostsnippet

## Things to avoid
- Instantiating components with `new` - always use the static `make()` constructor
- Loading relationship options manually - use `->relationship('relation', 'column')` instead
- Forgetting to register the panel provider in `bootstrap/providers.php`
- Skipping `->searchable()` and `->sortable()` on columns users will want to filter or sort
- Omitting `actingAs()` in tests - Filament enforces authentication by default


