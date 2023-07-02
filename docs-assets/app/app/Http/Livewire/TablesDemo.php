<?php

namespace App\Http\Livewire;

use App\Models\Post;
use App\Models\User;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\CreateAction;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\CheckboxColumn;
use Filament\Tables\Columns\ColorColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\SelectColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\TextInputColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Grouping\Group;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Component;

class TablesDemo extends Component implements HasForms, HasTable
{
    use InteractsWithForms;
    use InteractsWithTable;

    public string $tableConfiguration;

    public function mount(): void
    {
        $this->tableConfiguration = request()->get('table');
    }

    public function example(Table $table): Table
    {
        return $this->usersTable($table)
            ->heading('Users')
            ->description('Individuals who have registered for the application.')
            ->columns([
                TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('email')
                    ->label('Email address')
                    ->searchable(),
                IconColumn::make('email_verified_at')
                    ->label('Verified')
                    ->boolean()
                    ->getStateUsing(fn ($record) => filled($record->email_verified_at))
                    ->toggleable(),
            ])
            ->filters([
                Filter::make('verified'),
            ])
            ->groups([
                Group::make('verified'),
            ])
            ->headerActions([
                CreateAction::make(),
            ])
            ->actions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public function gettingStartedColumns(Table $table): Table
    {
        return $this->postsTable($table)
            ->columns([
                TextColumn::make('title'),
                TextColumn::make('slug'),
                IconColumn::make('is_featured')
                    ->boolean(),
            ]);
    }

    public function gettingStartedSearchableColumns(Table $table): Table
    {
        return $this->postsTable($table)
            ->columns([
                TextColumn::make('title')
                    ->searchable(),
                TextColumn::make('slug'),
                IconColumn::make('is_featured')
                    ->boolean(),
            ]);
    }

    public function gettingStartedSortableColumns(Table $table): Table
    {
        return $this->postsTable($table)
            ->columns([
                TextColumn::make('title')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('slug'),
                IconColumn::make('is_featured')
                    ->boolean(),
            ]);
    }

    public function gettingStartedRelationshipColumns(Table $table): Table
    {
        return $this->postsTable($table)
            ->columns([
                TextColumn::make('title')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('slug'),
                IconColumn::make('is_featured')
                    ->boolean(),
                TextColumn::make('author.name'),
            ]);
    }

    public function gettingStartedFilters(Table $table): Table
    {
        return $this->gettingStartedRelationshipColumns($table)
            ->filters([
                Filter::make('is_featured')
                    ->query(fn (Builder $query) => $query->where('is_featured', true)),
                SelectFilter::make('status')
                    ->options([
                        'draft' => 'Draft',
                        'reviewing' => 'Reviewing',
                        'published' => 'Published',
                    ]),
            ]);
    }

    public function gettingStartedActions(Table $table): Table
    {
        return $this->gettingStartedFilters($table)
            ->actions([
                Action::make('feature')
                    ->action(function (Post $record) {
                        $record->is_featured = true;
                        $record->save();
                    })
                    ->hidden(fn (Post $record): bool => $record->is_featured),
                Action::make('unfeature')
                    ->action(function (Post $record) {
                        $record->is_featured = false;
                        $record->save();
                    })
                    ->visible(fn (Post $record): bool => $record->is_featured),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public function sortableColumns(Table $table): Table
    {
        return $this->usersTable($table)
            ->columns([
                TextColumn::make('name')
                    ->sortable(),
                TextColumn::make('email')
                    ->label('Email address'),
                IconColumn::make('email_verified_at')
                    ->label('Verified')
                    ->boolean()
                    ->getStateUsing(fn ($record) => filled($record->email_verified_at)),
            ]);
    }

    public function searchableColumns(Table $table): Table
    {
        return $this->usersTable($table)
            ->columns([
                TextColumn::make('name')
                    ->searchable(),
                TextColumn::make('email')
                    ->label('Email address'),
                IconColumn::make('email_verified_at')
                    ->label('Verified')
                    ->boolean()
                    ->getStateUsing(fn ($record) => filled($record->email_verified_at)),
            ]);
    }

    public function individuallySearchableColumns(Table $table): Table
    {
        return $this->usersTable($table)
            ->columns([
                TextColumn::make('name')
                    ->searchable(isIndividual: true),
                TextColumn::make('email')
                    ->label('Email address')
                    ->searchable(isIndividual: true),
                IconColumn::make('email_verified_at')
                    ->label('Verified')
                    ->boolean()
                    ->getStateUsing(fn ($record) => filled($record->email_verified_at)),
            ]);
    }

    public function toggleableColumns(Table $table): Table
    {
        return $this->usersTable($table)
            ->columns([
                TextColumn::make('name'),
                TextColumn::make('email')
                    ->label('Email address')
                    ->toggleable(),
                IconColumn::make('email_verified_at')
                    ->label('Verified')
                    ->boolean()
                    ->getStateUsing(fn ($record) => filled($record->email_verified_at))
                    ->toggleable(),
            ]);
    }

    public function columnTooltips(Table $table): Table
    {
        return $this->usersTable($table)
            ->columns([
                TextColumn::make('name'),
                TextColumn::make('email')
                    ->label('Email address'),
                IconColumn::make('email_verified_at')
                    ->label('Verified')
                    ->boolean()
                    ->getStateUsing(fn ($record) => filled($record->email_verified_at))
                    ->tooltip(fn ($record) => $record->email_verified_at?->toFormattedDateString()),
            ]);
    }

    public function columnAlignment(Table $table): Table
    {
        return $this->usersTable($table)
            ->columns([
                TextColumn::make('name'),
                TextColumn::make('email')
                    ->label('Email address')
                    ->alignEnd(),
                IconColumn::make('email_verified_at')
                    ->label('Verified')
                    ->boolean()
                    ->getStateUsing(fn ($record) => filled($record->email_verified_at)),
            ]);
    }

    public function textColumn(Table $table): Table
    {
        return $this->postsTable($table)
            ->columns([
                TextColumn::make('title'),
            ]);
    }

    public function textColumnBadge(Table $table): Table
    {
        return $this->postsTable($table)
            ->columns([
                TextColumn::make('title'),
                TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'draft' => 'gray',
                        'reviewing' => 'warning',
                        'published' => 'success',
                        'rejected' => 'danger',
                    }),
            ]);
    }

    public function textColumnDescription(Table $table): Table
    {
        return $this->postsTable($table)
            ->columns([
                TextColumn::make('title')
                    ->description(fn (Post $record): string => $record->description),
            ]);
    }

    public function textColumnDescriptionAbove(Table $table): Table
    {
        return $this->postsTable($table)
            ->columns([
                TextColumn::make('title')
                    ->description(fn (Post $record): string => $record->description, position: 'above'),
            ]);
    }

    public function textColumnColor(Table $table): Table
    {
        return $this->postsTable($table)
            ->columns([
                TextColumn::make('title'),
                TextColumn::make('status')
                    ->color('primary'),
            ]);
    }

    public function textColumnIcon(Table $table): Table
    {
        return $this->usersTable($table)
            ->columns([
                TextColumn::make('name'),
                TextColumn::make('email')
                    ->icon('heroicon-m-envelope'),
            ]);
    }

    public function textColumnIconAfter(Table $table): Table
    {
        return $this->usersTable($table)
            ->columns([
                TextColumn::make('name'),
                TextColumn::make('email')
                    ->icon('heroicon-m-envelope')
                    ->iconPosition('after'),
            ]);
    }

    public function textColumnLarge(Table $table): Table
    {
        return $this->postsTable($table)
            ->columns([
                TextColumn::make('title')
                    ->size('lg'),
            ]);
    }

    public function textColumnBold(Table $table): Table
    {
        return $this->postsTable($table)
            ->columns([
                TextColumn::make('title')
                    ->weight('bold'),
            ]);
    }

    public function textColumnMono(Table $table): Table
    {
        return $this->usersTable($table)
            ->columns([
                TextColumn::make('name'),
                TextColumn::make('email')
                    ->fontFamily('mono'),
            ]);
    }

    public function textColumnCopyable(Table $table): Table
    {
        return $this->usersTable($table)
            ->columns([
                TextColumn::make('name'),
                TextColumn::make('email')
                    ->copyable()
                    ->copyMessage('Email address copied')
                    ->copyMessageDuration(1500),
            ]);
    }

    public function iconColumn(Table $table): Table
    {
        return $this->postsTable($table)
            ->columns([
                TextColumn::make('title'),
                IconColumn::make('status')
                    ->icon(fn (string $state): string => match ($state) {
                        'draft' => 'heroicon-o-pencil',
                        'reviewing' => 'heroicon-o-clock',
                        'published' => 'heroicon-o-check-circle',
                    }),
            ]);
    }

    public function iconColumnColor(Table $table): Table
    {
        return $this->postsTable($table)
            ->columns([
                TextColumn::make('title'),
                IconColumn::make('status')
                    ->icon(fn (string $state): string => match ($state) {
                        'draft' => 'heroicon-o-pencil',
                        'reviewing' => 'heroicon-o-clock',
                        'published' => 'heroicon-o-check-circle',
                    })
                    ->color(fn (string $state): string => match ($state) {
                        'draft' => 'info',
                        'reviewing' => 'warning',
                        'published' => 'success',
                        default => 'gray',
                    }),
            ]);
    }

    public function iconColumnMedium(Table $table): Table
    {
        return $this->postsTable($table)
            ->columns([
                TextColumn::make('title'),
                IconColumn::make('status')
                    ->icon(fn (string $state): string => match ($state) {
                        'draft' => 'heroicon-o-pencil',
                        'reviewing' => 'heroicon-o-clock',
                        'published' => 'heroicon-o-check-circle',
                    })
                    ->size('md'),
            ]);
    }

    public function iconColumnBoolean(Table $table): Table
    {
        return $this->postsTable($table)
            ->columns([
                TextColumn::make('title'),
                IconColumn::make('is_featured')
                    ->boolean(),
            ]);
    }

    public function iconColumnBooleanIcon(Table $table): Table
    {
        return $this->postsTable($table)
            ->columns([
                TextColumn::make('title'),
                IconColumn::make('is_featured')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-badge')
                    ->falseIcon('heroicon-o-x-mark'),
            ]);
    }

    public function iconColumnBooleanColor(Table $table): Table
    {
        return $this->postsTable($table)
            ->columns([
                TextColumn::make('title'),
                IconColumn::make('is_featured')
                    ->boolean()
                    ->trueColor('info')
                    ->falseColor('warning'),
            ]);
    }

    public function imageColumn(Table $table): Table
    {
        return $this->usersTable($table)
            ->columns([
                TextColumn::make('name'),
                ImageColumn::make('avatar'),
            ]);
    }

    public function imageColumnSquare(Table $table): Table
    {
        return $this->usersTable($table)
            ->columns([
                TextColumn::make('name'),
                ImageColumn::make('avatar')
                    ->square(),
            ]);
    }

    public function imageColumnCircular(Table $table): Table
    {
        return $this->usersTable($table)
            ->columns([
                TextColumn::make('name'),
                ImageColumn::make('avatar')
                    ->circular(),
            ]);
    }

    public function colorColumn(Table $table): Table
    {
        return $this->postsTable($table)
            ->columns([
                TextColumn::make('title'),
                ColorColumn::make('color')
                    ->getStateUsing(fn ($rowLoop): string => match ($rowLoop->index) {
                        0 => '#ef4444',
                        1 => '#fde047',
                        2 => '#22c55e',
                        3 => '#0ea5e9',
                        default => '#8b5cf6',
                    }),
            ]);
    }

    public function colorColumnCopyable(Table $table): Table
    {
        return $this->postsTable($table)
            ->columns([
                TextColumn::make('title'),
                ColorColumn::make('color')
                    ->getStateUsing(fn ($rowLoop): string => match ($rowLoop->index) {
                        0 => '#ef4444',
                        1 => '#fde047',
                        2 => '#22c55e',
                        3 => '#0ea5e9',
                        default => '#8b5cf6',
                    })
                    ->copyable()
                    ->copyMessage('Color code copied')
                    ->copyMessageDuration(1500),
            ]);
    }

    public function selectColumn(Table $table): Table
    {
        return $this->postsTable($table)
            ->columns([
                TextColumn::make('title'),
                SelectColumn::make('status')
                    ->options([
                        'draft' => 'Draft',
                        'reviewing' => 'Reviewing',
                        'published' => 'Published',
                    ]),
            ]);
    }

    public function toggleColumn(Table $table): Table
    {
        return $this->usersTable($table)
            ->columns([
                TextColumn::make('name'),
                ToggleColumn::make('is_admin')
                    ->getStateUsing(fn ($rowLoop): bool => $rowLoop->index < 2),
            ]);
    }

    public function textInputColumn(Table $table): Table
    {
        return $this->usersTable($table)
            ->columns([
                TextColumn::make('name'),
                TextInputColumn::make('email'),
            ]);
    }

    public function checkboxColumn(Table $table): Table
    {
        return $this->usersTable($table)
            ->columns([
                TextColumn::make('name'),
                CheckboxColumn::make('is_admin')
                    ->getStateUsing(fn ($rowLoop): bool => $rowLoop->index < 2),
            ]);
    }

    public function postsTable(Table $table): Table
    {
        User::truncate();
        Post::truncate();
        Post::insert([
            [
                'title' => 'What is Filament?',
                'slug' => 'what-is-filament',
                'description' => 'Find out what Filament is and how it can help you build your next project.',
                'is_featured' => true,
                'status' => 'published',
                'author_id' => User::factory()->create(['name' => 'Dan Harrin'])->id,
            ],
            [
                'title' => 'Top 5 best features of Filament',
                'slug' => 'top-5-features',
                'description' => 'Discover the top 5 best features of Filament and how they can help you build your next project.',
                'is_featured' => false,
                'status' => 'reviewing',
                'author_id' => User::factory()->create(['name' => 'Ryan Chandler'])->id,
            ],
            [
                'title' => 'Tips for building a great Filament plugin',
                'slug' => 'plugin-tips',
                'description' => 'Learn how to build a great Filament plugin and get it featured in the official plugin directory.',
                'is_featured' => true,
                'status' => 'draft',
                'author_id' => User::factory()->create(['name' => 'Zep Fietje'])->id,
            ],
            [
                'title' => 'Customizing Filament\'s UI with a theme',
                'slug' => 'theme-guide',
                'description' => 'Learn how to customize Filament\'s UI with a theme and make it your own.',
                'is_featured' => false,
                'status' => 'reviewing',
                'author_id' => User::factory()->create(['name' => 'Dennis Koch'])->id,
            ],
            [
                'title' => 'New Filament plugins in August',
                'slug' => 'new-plugins-august',
                'description' => 'Discover the latest Filament plugins that were released in August.',
                'is_featured' => false,
                'status' => 'published',
                'author_id' => User::factory()->create(['name' => 'Adam Weston'])->id,
            ],
        ]);
        Post::factory()->count(45)->create();

        return $table
            ->query(Post::query())
            ->defaultPaginationPageOption(5);
    }

    public function usersTable(Table $table): Table
    {
        User::truncate();
        User::insert([
            [
                'name' => 'Dan Harrin',
                'email' => 'dan@filamentphp.com',
                'email_verified_at' => '2023-08-01 11:30:00',
                'password' => 'password',
                'avatar' => 'https://avatars.githubusercontent.com/u/41773797?v=4',
            ],
            [
                'name' => 'Ryan Chandler',
                'email' => 'ryan@filamentphp.com',
                'email_verified_at' => null,
                'password' => 'password',
                'avatar' => 'https://avatars.githubusercontent.com/u/41837763?v=4',
            ],
            [
                'name' => 'Zep Fietje',
                'email' => 'zep@filamentphp.com',
                'email_verified_at' => null,
                'password' => 'password',
                'avatar' => 'https://avatars.githubusercontent.com/u/44533235?v=4',
            ],
            [
                'name' => 'Dennis Koch',
                'email' => 'dennis@filamentphp.com',
                'email_verified_at' => '2023-08-01 11:30:00',
                'password' => 'password',
                'avatar' => 'https://avatars.githubusercontent.com/u/22632550?v=4',
            ],
            [
                'name' => 'Adam Weston',
                'email' => 'adam@filamentphp.com',
                'email_verified_at' => '2023-08-01 11:30:00',
                'password' => 'password',
                'avatar' => 'https://avatars.githubusercontent.com/u/3596800?v=4',
            ],
        ]);
        User::factory()->count(45)->create();

        return $table
            ->query(User::query())
            ->defaultPaginationPageOption(5);
    }

    public function table(Table $table): Table
    {
        return $this->{$this->tableConfiguration}($table);
    }

    public function render()
    {
        return view('livewire.tables');
    }
}
