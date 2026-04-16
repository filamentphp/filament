---
name: filament-development
description: "Use for any Filament development task. Activate when user mentions Filament, Resources, Pages, Widgets, Forms, Tables, Actions, Notifications, or admin panels. Covers CRUD resources, panels, forms, tables, actions, notifications, widgets, and testing. Do not use for non-Filament Laravel UI tasks."
license: MIT
metadata:
  author: filamentphp
---
@php
/** @var \Laravel\Boost\Install\GuidelineAssist $assist */
@endphp
# Filament Development (v3)

## Documentation

Use `search-docs` for detailed Filament v3 patterns and API documentation.

## Overview

Filament is a Server-Driven UI (SDUI) framework for Laravel built on Livewire, Alpine.js, and Tailwind CSS. Components use fluent APIs with static `make()` constructors.

## Artisan Commands

Always use Filament-specific Artisan commands:

- `{{ $assist->artisanCommand('make:filament-resource [Post] --generate') }}` – Resource with form, table, pages
- `{{ $assist->artisanCommand('make:filament-page [Settings]') }}` – Custom page
- `{{ $assist->artisanCommand('make:filament-widget [StatsOverview] --stats-overview') }}` – Stats widget
- `{{ $assist->artisanCommand('make:filament-widget [LatestOrders] --table') }}` – Table widget

## Panel Setup

@boostsnippet("Register Panel in Service Provider", "php")
class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->login()
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets');
    }
}
@endboostsnippet

## Resources

Resources are static classes for CRUD interfaces, living in `app/Filament/Resources`:

@boostsnippet("Resource Form", "php")
public static function form(Form $form): Form
{
    return $form->schema([
        TextInput::make('title')->required()->maxLength(255),
        RichEditor::make('content')->columnSpanFull(),
        Select::make('category_id')->relationship('category', 'name'),
    ]);
}
@endboostsnippet

@boostsnippet("Resource Table", "php")
public static function table(Table $table): Table
{
    return $table
        ->columns([
            TextColumn::make('title')->searchable()->sortable(),
            TextColumn::make('created_at')->dateTime()->sortable(),
        ])
        ->filters([
            SelectFilter::make('category')->relationship('category', 'name'),
        ])
        ->actions([EditAction::make(), DeleteAction::make()])
        ->bulkActions([DeleteBulkAction::make()]);
}
@endboostsnippet

## Forms

Use `relationship()` when options come from a relationship:

@boostsnippet("Form with Relationship", "php")
Select::make('user_id')
    ->label('Author')
    ->relationship('author', 'name')
    ->required(),
@endboostsnippet

## Actions

@boostsnippet("Action with Confirmation", "php")
Action::make('approve')
    ->color('success')
    ->requiresConfirmation()
    ->action(fn (Post $record) => $record->approve()),
@endboostsnippet

@boostsnippet("Action with Form Modal", "php")
Action::make('reject')
    ->color('danger')
    ->form([Textarea::make('reason')->required()])
    ->action(fn (Post $record, array $data) => $record->reject($data['reason'])),
@endboostsnippet

## Notifications

@boostsnippet("Send Notification", "php")
Notification::make()
    ->title('Saved successfully')
    ->success()
    ->send();
@endboostsnippet

## Widgets

@boostsnippet("Stats Overview Widget", "php")
class StatsOverviewWidget extends StatsOverviewWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Total Users', User::count()),
            Stat::make('Active Users', User::where('is_active', true)->count())
                ->color('success'),
        ];
    }
}
@endboostsnippet

## Testing

Always authenticate. Use `livewire()` for Filament component tests:

@boostsnippet("Test Listing Records", "php")
it('can list posts', function () {
    $this->actingAs(User::factory()->create());
    $posts = Post::factory()->count(5)->create();

    livewire(PostResource\Pages\ListPosts::class)
        ->assertCanSeeTableRecords($posts);
});
@endboostsnippet

@boostsnippet("Test Creating a Record", "php")
it('can create a post', function () {
    $this->actingAs(User::factory()->create());

    livewire(PostResource\Pages\CreatePost::class)
        ->fillForm(['title' => 'Hello World'])
        ->call('create')
        ->assertHasNoFormErrors();

    assertDatabaseHas(Post::class, ['title' => 'Hello World']);
});
@endboostsnippet

@boostsnippet("Test Calling an Action", "php")
it('can approve a post', function () {
    $this->actingAs(User::factory()->create());
    $post = Post::factory()->create();

    livewire(PostResource\Pages\ListPosts::class)
        ->callTableAction('approve', $post);

    expect($post->refresh()->status)->toBe('approved');
});
@endboostsnippet

## Common Pitfalls

- Not using Filament Artisan commands (use `make:filament-resource` not `make:model`)
- Forgetting to register the panel provider in `bootstrap/providers.php`
- Using Eloquent queries instead of the `relationship()` method on form components
- Not adding `->searchable()` or `->sortable()` on table columns that need it
- Forgetting authentication in Filament tests
