<?php

namespace App\Http\Livewire;

use App\Models\Post;
use App\Models\User;
use Filament\Tables\Actions\Action;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\CreateAction;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
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
            ->defaultPaginationPageOption(5)
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

    public function postsTable(Table $table): Table
    {
        User::truncate();
        Post::truncate();
        Post::insert([
            [
                'title' => 'What is Filament?',
                'slug' => 'what-is-filament',
                'is_featured' => true,
                'author_id' => User::factory()->create(['name' => 'Dan Harrin'])->id,
            ],
            [
                'title' => 'Top 5 best features of Filament',
                'slug' => 'top-5-features',
                'is_featured' => false,
                'author_id' => User::factory()->create(['name' => 'Ryan Chandler'])->id,
            ],
            [
                'title' => 'Tips for building a great Filament plugin',
                'slug' => 'plugin-tips',
                'is_featured' => true,
                'author_id' => User::factory()->create(['name' => 'Zep Fietje'])->id,
            ],
            [
                'title' => 'Customizing Filament\'s UI with a theme',
                'slug' => 'theme-guide',
                'is_featured' => false,
                'author_id' => User::factory()->create(['name' => 'Adam Weston'])->id,
            ],
            [
                'title' => 'New Filament plugins in August 2023',
                'slug' => 'new-plugins-august-2023',
                'is_featured' => false,
                'author_id' => User::factory()->create(['name' => 'Dennis Koch'])->id,
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
            ],
            [
                'name' => 'Ryan Chandler',
                'email' => 'ryan@filamentphp.com',
                'email_verified_at' => null,
                'password' => 'password',
            ],
            [
                'name' => 'Zep Fietje',
                'email' => 'zep@filamentphp.com',
                'email_verified_at' => null,
                'password' => 'password',
            ],
            [
                'name' => 'Adam Weston',
                'email' => 'adam@filamentphp.com',
                'email_verified_at' => '2023-08-01 11:30:00',
                'password' => 'password',
            ],
            [
                'name' => 'Dennis Koch',
                'email' => 'dennis@filamentphp.com',
                'email_verified_at' => '2023-08-01 11:30:00',
                'password' => 'password',
            ],
        ]);
        User::factory()->count(45)->create();

        return $table
            ->query(User::query());
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
