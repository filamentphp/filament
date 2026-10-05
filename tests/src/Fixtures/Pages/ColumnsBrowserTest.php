<?php

namespace Filament\Tests\Fixtures\Pages;

use BackedEnum;
use Closure;
use Filament\Actions\Action;
use Filament\Pages\Page;
use Filament\Schemas\Components\EmbeddedTable;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Filament\Tests\Fixtures\Enums\NavigationGroupEnum;
use Filament\Tests\Fixtures\Models\Post;
use Filament\Tests\Fixtures\Models\Team;
use Filament\Tests\Fixtures\Models\User;
use Illuminate\Support\HtmlString;
use Livewire\Attributes\Url;

class ColumnsBrowserTest extends Page implements HasTable
{
    use Tables\Concerns\InteractsWithTable;

    protected static string | BackedEnum | null $navigationIcon = Heroicon::OutlinedTableCells;

    protected static ?int $navigationSort = 20;

    protected static bool $shouldRegisterNavigation = false;

    #[Url]
    public bool $relatedRecordLayout = false;

    public bool $hasRejectedCheckboxUpdate = false;

    public bool $hasRejectedToggleUpdate = false;

    public function table(Table $table): Table
    {
        if ($this->relatedRecordLayout) {
            return $table->query(Post::query())->columns([
                Tables\Columns\Layout\Stack::make([
                    Tables\Columns\TextColumn::make('author.name')
                        ->url(static fn (User $relatedRecord): string => "/authors/{$relatedRecord->getKey()}"),
                    Tables\Columns\TextColumn::make('author.teams.name')
                        ->listWithLineBreaks()
                        ->url(static fn (string $state, Post $record, Team $relatedRecord): string => "/posts/{$record->getKey()}/teams/{$relatedRecord->getKey()}/" . rawurlencode($state)),
                ])->extraAttributes(['data-testid' => 'related-record-layout']),
            ]);
        }

        return $table
            ->query(Post::query())
            ->reorderable('id')
            ->reorderRecordsTriggerAction(static fn (Action $action): Action => $action->extraAttributes(['data-testid' => 'toggle-reordering']))
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->label('Title')
                    ->url(static fn (Post $record): string => "/posts/{$record->getKey()}")
                    ->extraCellAttributes(['data-testid' => 'linked-column']),
                Tables\Columns\TextColumn::make('content')
                    ->label('Content')
                    ->limit(50)
                    ->url('/posts')
                    ->disabledClick()
                    ->extraCellAttributes(['data-testid' => 'disabled-column']),
                Tables\Columns\TextColumn::make('enum_label')
                    ->state(NavigationGroupEnum::Users)
                    ->html()
                    ->extraAttributes(['data-testid' => 'enum-label-column']),
                Tables\Columns\IconColumn::make('is_published')
                    ->label('Published')
                    ->boolean(),
                Tables\Columns\CheckboxColumn::make('author.json.is_active')
                    ->label('Author active')
                    ->tooltip(static fn (bool $state, User $relatedRecord): HtmlString => new HtmlString('<strong>Update activity for ' . e($relatedRecord->name) . '</strong>'))
                    ->rules([
                        'boolean',
                        function (string $attribute, mixed $value, Closure $fail): void {
                            if (! $this->hasRejectedCheckboxUpdate) {
                                $this->hasRejectedCheckboxUpdate = true;

                                $fail('Approval <em>required</em>.');
                            }
                        },
                    ])
                    ->extraInputAttributes(['data-testid' => 'author-active-checkbox']),
                Tables\Columns\ToggleColumn::make('author.json.is_subscribed')
                    ->label('Author subscribed')
                    ->tooltip(static fn (bool $state, User $relatedRecord): HtmlString => new HtmlString('<strong>Update subscription for ' . e($relatedRecord->name) . '</strong>'))
                    ->rules([
                        'boolean',
                        function (string $attribute, mixed $value, Closure $fail): void {
                            if (! $this->hasRejectedToggleUpdate) {
                                $this->hasRejectedToggleUpdate = true;

                                $fail('Approval <em>required</em>.');
                            }
                        },
                    ])
                    ->extraAttributes(['data-testid' => 'author-subscribed-toggle']),
                Tables\Columns\TextInputColumn::make('author.json.display_name')
                    ->label('Display name')
                    ->tooltip(static fn (User $relatedRecord): HtmlString => new HtmlString('<strong>Update name for ' . e($relatedRecord->name) . '</strong>'))
                    ->rules(['in:Approved'])
                    ->validationMessages(['in' => 'Approval <em>required</em>.'])
                    ->extraAttributes(['data-testid' => 'author-name-input']),
                ...array_map(static fn (string $mode): Tables\Columns\SelectColumn => Tables\Columns\SelectColumn::make("author.json.{$mode}_status")
                    ->label(ucfirst($mode) . ' status')
                    ->options(['Pending' => 'Pending', 'Rejected' => 'Rejected', 'Approved' => 'Approved'])
                    ->native($mode === 'native')
                    ->searchableOptions($mode === 'searchable')
                    ->tooltip(static fn (User $relatedRecord): HtmlString => new HtmlString('<strong>Update status for ' . e($relatedRecord->name) . '</strong>'))
                    ->rules(['in:Approved'])
                    ->validationMessages(['in' => 'Approval <em>required</em>.'])
                    ->extraAttributes(['data-testid' => "author-{$mode}-select"]), ['native', 'custom', 'searchable']),
                Tables\Columns\TextColumn::make('rating')
                    ->label('Rating')
                    ->badge(),
                Tables\Columns\TextColumn::make('tags')
                    ->label('Tags')
                    ->badge()
                    ->separator(','),
            ]);
    }

    public function content(Schema $schema): Schema
    {
        return $schema
            ->components([
                EmbeddedTable::make(),
            ]);
    }
}
