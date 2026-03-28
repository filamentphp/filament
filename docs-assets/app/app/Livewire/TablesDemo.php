<?php

namespace App\Livewire;

use App\Models\Post;
use App\Models\User;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\DatePicker;
use Filament\QueryBuilder\Constraints\BooleanConstraint;
use Filament\QueryBuilder\Constraints\DateConstraint;
use Filament\QueryBuilder\Constraints\SelectConstraint;
use Filament\QueryBuilder\Constraints\TextConstraint;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Support\Enums\Alignment;
use Filament\Support\Enums\FontFamily;
use Filament\Support\Enums\FontWeight;
use Filament\Support\Enums\IconPosition;
use Filament\Support\Enums\IconSize;
use Filament\Support\Enums\Size;
use Filament\Support\Enums\TextSize;
use Filament\Support\Enums\Width;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\CheckboxColumn;
use Filament\Tables\Columns\ColorColumn;
use Filament\Tables\Columns\ColumnGroup;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\Layout\Grid;
use Filament\Tables\Columns\Layout\Panel;
use Filament\Tables\Columns\Layout\Split;
use Filament\Tables\Columns\Layout\Stack;
use Filament\Tables\Columns\SelectColumn;
use Filament\Tables\Columns\Summarizers\Average;
use Filament\Tables\Columns\Summarizers\Count;
use Filament\Tables\Columns\Summarizers\Range;
use Filament\Tables\Columns\Summarizers\Sum;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\TextInputColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Enums\ColumnManagerLayout;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Enums\PaginationMode;
use Filament\Tables\Enums\RecordActionsPosition;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\Indicator;
use Filament\Tables\Filters\QueryBuilder;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Grouping\Group;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Component;
use stdClass;

class TablesDemo extends Component implements HasActions, HasSchemas, HasTable
{
    use InteractsWithActions;
    use InteractsWithSchemas;
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
                    ->action(function (Post $record): void {
                        $record->is_featured = true;
                        $record->save();
                    })
                    ->hidden(fn (Post $record): bool => $record->is_featured),
                Action::make('unfeature')
                    ->action(function (Post $record): void {
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

    public function placeholderColumns(Table $table): Table
    {
        return $this->postsTable($table)
            ->columns([
                TextColumn::make('title'),
                TextColumn::make('description')
                    ->getStateUsing(fn (Post $record, stdClass $rowLoop): ?string => $rowLoop->odd ? $record->description : null)
                    ->placeholder('No description.'),
            ]);
    }

    public function columnManager(Table $table): Table
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

    public function columnManagerReorderable(Table $table): Table
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
            ])
            ->reorderableColumns();
    }

    public function columnManagerModal(Table $table): Table
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
            ])
            ->columnManagerLayout(ColumnManagerLayout::Modal);
    }

    public function columnManagerColumns(Table $table): Table
    {
        return $this->usersTable($table)
            ->columns([
                TextColumn::make('name')
                    ->toggleable(),
                TextColumn::make('email')
                    ->label('Email address')
                    ->toggleable(),
                TextColumn::make('phone')
                    ->toggleable(),
                TextColumn::make('job')
                    ->label('Job title')
                    ->toggleable(),
                IconColumn::make('email_verified_at')
                    ->label('Verified')
                    ->boolean()
                    ->getStateUsing(fn ($record) => filled($record->email_verified_at))
                    ->toggleable(),
            ])
            ->columnManagerColumns(2);
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

    public function columnHeaderTooltips(Table $table): Table
    {
        return $this->usersTable($table)
            ->columns([
                TextColumn::make('name'),
                TextColumn::make('email')
                    ->label('Email address'),
                TextColumn::make('email_verified_at')
                    ->label('Verified')
                    ->headerTooltip('The date the email address was verified')
                    ->date(),
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

    public function columnVerticalAlignment(Table $table): Table
    {
        return $this->usersTable($table)
            ->columns([
                TextColumn::make('name')
                    ->verticallyAlignStart(),
                TextColumn::make('email')
                    ->label('Email addresses')
                    ->getStateUsing(fn ($record): array => [
                        $record->email,
                        str($record->email)->replace('filamentphp.com', 'filament.dev'),
                    ])
                    ->listWithLineBreaks(),
                IconColumn::make('email_verified_at')
                    ->label('Verified')
                    ->boolean()
                    ->getStateUsing(fn ($record) => filled($record->email_verified_at)),
            ]);
    }

    public function columnListWithLineBreaks(Table $table): Table
    {
        return $this->usersTable($table)
            ->columns([
                TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('email')
                    ->label('Email addresses')
                    ->getStateUsing(fn ($record): array => [
                        $record->email,
                        str($record->email)->replace('filamentphp.com', 'filament.dev'),
                    ])
                    ->listWithLineBreaks(),
                IconColumn::make('email_verified_at')
                    ->label('Verified')
                    ->boolean()
                    ->getStateUsing(fn ($record) => filled($record->email_verified_at)),
            ]);
    }

    public function columnBulleted(Table $table): Table
    {
        return $this->usersTable($table)
            ->columns([
                TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('email')
                    ->label('Email addresses')
                    ->getStateUsing(fn ($record): array => [
                        $record->email,
                        str($record->email)->replace('filamentphp.com', 'filament.dev'),
                    ])
                    ->bulleted(),
                IconColumn::make('email_verified_at')
                    ->label('Verified')
                    ->boolean()
                    ->getStateUsing(fn ($record) => filled($record->email_verified_at)),
            ]);
    }

    public function columnWrapHeader(Table $table): Table
    {
        return $this->usersTable($table)
            ->columns([
                TextColumn::make('name')
                    ->label('Full legal name of the registered user')
                    ->wrapHeader()
                    ->searchable()
                    ->sortable(),
                TextColumn::make('email')
                    ->label('Email address used for account notifications')
                    ->wrapHeader(),
                TextColumn::make('phone')
                    ->label('Primary contact phone number on file')
                    ->wrapHeader(),
            ]);
    }

    public function tableHeading(Table $table): Table
    {
        return $this->postsTable($table)
            ->heading('Blog Posts')
            ->description('Manage your blog posts and articles.')
            ->columns([
                TextColumn::make('title')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'draft' => 'gray',
                        'reviewing' => 'warning',
                        'published' => 'success',
                        default => 'gray',
                    }),
                TextColumn::make('author.name'),
            ]);
    }

    public function columnGrouping(Table $table): Table
    {
        return $this->postsTable($table)
            ->columns([
                TextColumn::make('title')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('slug'),
                ColumnGroup::make('Visibility', [
                    TextColumn::make('status')
                        ->badge()
                        ->icon(fn (string $state): Heroicon => match ($state) {
                            'draft' => Heroicon::OutlinedPencil,
                            'reviewing' => Heroicon::OutlinedClock,
                            'published' => Heroicon::OutlinedCheckCircle,
                        })
                        ->color(fn (string $state): string => match ($state) {
                            'draft' => 'gray',
                            'reviewing' => 'warning',
                            'published' => 'success',
                        }),
                    IconColumn::make('is_featured')
                        ->boolean(),
                ]),
                TextColumn::make('author.name'),
            ]);
    }

    public function columnWidth(Table $table): Table
    {
        return $this->postsTable($table)
            ->columns([
                TextColumn::make('title')
                    ->grow()
                    ->searchable()
                    ->sortable(),
                TextColumn::make('slug'),
                TextColumn::make('status')
                    ->badge()
                    ->icon(fn (string $state): Heroicon => match ($state) {
                        'draft' => Heroicon::OutlinedPencil,
                        'reviewing' => Heroicon::OutlinedClock,
                        'published' => Heroicon::OutlinedCheckCircle,
                    })
                    ->color(fn (string $state): string => match ($state) {
                        'draft' => 'gray',
                        'reviewing' => 'warning',
                        'published' => 'success',
                    }),
                IconColumn::make('is_featured')
                    ->boolean()
                    ->width('1%'),
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

    public function textColumnSeparatorBadge(Table $table): Table
    {
        $tagSets = [
            'Laravel, Livewire, PHP',
            'Filament, Admin Panel',
            'Plugins, Open Source, Community',
            'Tutorial, Beginner',
            'Advanced, Performance, Tips',
        ];

        return $this->postsTable($table)
            ->columns([
                TextColumn::make('title'),
                TextColumn::make('tags')
                    ->getStateUsing(fn ($rowLoop): string => $tagSets[$rowLoop->index] ?? 'General')
                    ->badge()
                    ->separator(','),
            ]);
    }

    public function textColumnMarkdown(Table $table): Table
    {
        $table = $this->postsTable($table)
            ->columns([
                TextColumn::make('title'),
                TextColumn::make('description')
                    ->markdown(),
            ]);

        $markdownContents = [
            '**Bold text** and *italic text* with a [link](https://filamentphp.com)',
            'Supports `inline code` and **nested** *formatting*',
            'Built with **Laravel** and [Livewire](https://livewire.laravel.com)',
            'Uses *Tailwind CSS* for **responsive** styling',
            'A **powerful** framework for building *admin panels*',
        ];

        foreach (Post::orderBy('id')->limit(5)->get() as $index => $post) {
            $post->timestamps = false;
            $post->update(['description' => $markdownContents[$index]]);
        }

        return $table;
    }

    public function textColumnHtml(Table $table): Table
    {
        $table = $this->postsTable($table)
            ->columns([
                TextColumn::make('title'),
                TextColumn::make('description')
                    ->html(),
            ]);

        $htmlContents = [
            '<strong>Bold text</strong> and <em>italic text</em> with a <a href="https://filamentphp.com">link</a>',
            'Supports <code>inline code</code> and <strong>nested</strong> <em>formatting</em>',
            'Built with <strong>Laravel</strong> and <a href="https://livewire.laravel.com">Livewire</a>',
            'Uses <em>Tailwind CSS</em> for <strong>responsive</strong> styling',
            'A <strong>powerful</strong> framework for building <em>admin panels</em>',
        ];

        foreach (Post::orderBy('id')->limit(5)->get() as $index => $post) {
            $post->timestamps = false;
            $post->update(['description' => $htmlContents[$index]]);
        }

        return $table;
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
                    ->icon(Heroicon::Envelope),
            ]);
    }

    public function textColumnIconAfter(Table $table): Table
    {
        return $this->usersTable($table)
            ->columns([
                TextColumn::make('name'),
                TextColumn::make('email')
                    ->icon(Heroicon::Envelope)
                    ->iconPosition(IconPosition::After),
            ]);
    }

    public function textColumnIconColor(Table $table): Table
    {
        return $this->usersTable($table)
            ->columns([
                TextColumn::make('name'),
                TextColumn::make('email')
                    ->icon(Heroicon::Envelope)
                    ->iconColor('primary'),
            ]);
    }

    public function textColumnLarge(Table $table): Table
    {
        return $this->postsTable($table)
            ->columns([
                TextColumn::make('title')
                    ->size(TextSize::Large),
            ]);
    }

    public function textColumnBold(Table $table): Table
    {
        return $this->postsTable($table)
            ->columns([
                TextColumn::make('title')
                    ->weight(FontWeight::Bold),
            ]);
    }

    public function textColumnMono(Table $table): Table
    {
        return $this->usersTable($table)
            ->columns([
                TextColumn::make('name'),
                TextColumn::make('email')
                    ->fontFamily(FontFamily::Mono),
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

    public function textColumnNumeric(Table $table): Table
    {
        return $this->postsTable($table)
            ->columns([
                TextColumn::make('title'),
                TextColumn::make('rating')
                    ->numeric(decimalPlaces: 1),
            ]);
    }

    public function textColumnMoney(Table $table): Table
    {
        return $this->postsTable($table)
            ->columns([
                TextColumn::make('title'),
                TextColumn::make('rating')
                    ->label('Price')
                    ->money('USD'),
            ]);
    }

    public function textColumnDate(Table $table): Table
    {
        return $this->postsTable($table)
            ->columns([
                TextColumn::make('title'),
                TextColumn::make('created_at')
                    ->dateTime(),
            ]);
    }

    public function textColumnSince(Table $table): Table
    {
        $table = $this->postsTable($table)
            ->columns([
                TextColumn::make('title'),
                TextColumn::make('created_at')
                    ->since(),
            ]);

        // Update dates to be relative to now for meaningful `since()` display
        $dates = [
            now()->subHours(2),
            now()->subDays(3),
            now()->subWeek(),
            now()->subMonths(2),
            now()->subMonths(6),
        ];

        foreach (Post::orderBy('id')->limit(5)->get() as $index => $post) {
            $post->timestamps = false;
            $post->update(['created_at' => $dates[$index]]);
        }

        return $table;
    }

    public function textColumnDateTooltip(Table $table): Table
    {
        $table = $this->postsTable($table)
            ->columns([
                TextColumn::make('title'),
                TextColumn::make('created_at')
                    ->since()
                    ->dateTooltip(),
            ]);

        // Update dates to be relative to now for meaningful `since()` display
        $dates = [
            now()->subHours(2),
            now()->subDays(3),
            now()->subWeek(),
            now()->subMonths(2),
            now()->subMonths(6),
        ];

        foreach (Post::orderBy('id')->limit(5)->get() as $index => $post) {
            $post->timestamps = false;
            $post->update(['created_at' => $dates[$index]]);
        }

        return $table;
    }

    public function textColumnLimit(Table $table): Table
    {
        return $this->postsTable($table)
            ->columns([
                TextColumn::make('title'),
                TextColumn::make('description')
                    ->limit(50),
            ]);
    }

    public function textColumnWords(Table $table): Table
    {
        return $this->postsTable($table)
            ->columns([
                TextColumn::make('title'),
                TextColumn::make('description')
                    ->words(10),
            ]);
    }

    public function textColumnWrap(Table $table): Table
    {
        $table = $this->postsTable($table)
            ->columns([
                TextColumn::make('title'),
                TextColumn::make('description')
                    ->wrap(),
            ]);

        // Use longer descriptions so wrapping is clearly visible across 3+ lines
        $descriptions = [
            'Find out what Filament is and how it can help you build your next project. This comprehensive guide covers all the fundamentals you need to get started with the framework, including forms, tables, actions, and notifications.',
            'Discover the top 5 best features of Filament and how they can help you build your next project. From forms to tables, actions to notifications, learn what makes Filament unique and why thousands of developers choose it for their admin panels.',
            'Learn how to build a great Filament plugin and get it featured in the official plugin directory. We cover best practices for packaging, testing, and distributing your plugins to the community, including versioning and documentation strategies.',
            'Learn how to customize Filament\'s UI with a theme and make it your own. Covers CSS hooks, color customization, font changes, and creating a cohesive brand experience across your entire admin panel and all of its components.',
            'Discover the latest Filament plugins that were released in August. Includes reviews, installation guides, and tips for integrating them into your existing admin panel setup. Each plugin is evaluated for code quality, documentation, and ease of use.',
        ];

        foreach (Post::orderBy('id')->limit(5)->get() as $index => $post) {
            $post->timestamps = false;
            $post->update(['description' => $descriptions[$index]]);
        }

        return $table;
    }

    public function textColumnLineClamp(Table $table): Table
    {
        $table = $this->postsTable($table)
            ->columns([
                TextColumn::make('title'),
                TextColumn::make('description')
                    ->wrap()
                    ->lineClamp(2),
            ]);

        // Use same longer descriptions so line clamping at 2 lines is clearly visible
        $descriptions = [
            'Find out what Filament is and how it can help you build your next project. This comprehensive guide covers all the fundamentals you need to get started with the framework, including forms, tables, actions, and notifications.',
            'Discover the top 5 best features of Filament and how they can help you build your next project. From forms to tables, actions to notifications, learn what makes Filament unique and why thousands of developers choose it for their admin panels.',
            'Learn how to build a great Filament plugin and get it featured in the official plugin directory. We cover best practices for packaging, testing, and distributing your plugins to the community, including versioning and documentation strategies.',
            'Learn how to customize Filament\'s UI with a theme and make it your own. Covers CSS hooks, color customization, font changes, and creating a cohesive brand experience across your entire admin panel and all of its components.',
            'Discover the latest Filament plugins that were released in August. Includes reviews, installation guides, and tips for integrating them into your existing admin panel setup. Each plugin is evaluated for code quality, documentation, and ease of use.',
        ];

        foreach (Post::orderBy('id')->limit(5)->get() as $index => $post) {
            $post->timestamps = false;
            $post->update(['description' => $descriptions[$index]]);
        }

        return $table;
    }

    public function iconColumn(Table $table): Table
    {
        return $this->postsTable($table)
            ->columns([
                TextColumn::make('title'),
                IconColumn::make('status')
                    ->icon(fn (string $state): Heroicon => match ($state) {
                        'draft' => Heroicon::OutlinedPencil,
                        'reviewing' => Heroicon::OutlinedClock,
                        'published' => Heroicon::OutlinedCheckCircle,
                    }),
            ]);
    }

    public function iconColumnColor(Table $table): Table
    {
        return $this->postsTable($table)
            ->columns([
                TextColumn::make('title'),
                IconColumn::make('status')
                    ->icon(fn (string $state): Heroicon => match ($state) {
                        'draft' => Heroicon::OutlinedPencil,
                        'reviewing' => Heroicon::OutlinedClock,
                        'published' => Heroicon::OutlinedCheckCircle,
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
                    ->icon(fn (string $state): Heroicon => match ($state) {
                        'draft' => Heroicon::OutlinedPencil,
                        'reviewing' => Heroicon::OutlinedClock,
                        'published' => Heroicon::OutlinedCheckCircle,
                    })
                    ->size(IconSize::Medium),
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
                    ->trueIcon(Heroicon::OutlinedCheckBadge)
                    ->falseIcon(Heroicon::OutlinedXMark),
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

    public function iconColumnWrap(Table $table): Table
    {
        return $this->postsTable($table)
            ->columns([
                TextColumn::make('title')
                    ->grow(),
                IconColumn::make('icons')
                    ->width('1%')
                    ->getStateUsing(fn ($rowLoop): array => match ($rowLoop->index) {
                        0 => ['draft', 'reviewing', 'published', 'draft', 'published', 'reviewing', 'draft', 'published'],
                        1 => ['published', 'reviewing', 'draft', 'published', 'draft', 'reviewing'],
                        2 => ['draft', 'published', 'reviewing', 'published', 'draft', 'reviewing', 'published'],
                        3 => ['reviewing', 'draft', 'published', 'reviewing', 'draft', 'published', 'reviewing', 'draft'],
                        default => ['published', 'draft', 'reviewing', 'draft', 'published', 'reviewing', 'published', 'draft'],
                    })
                    ->icon(fn (string $state): Heroicon => match ($state) {
                        'draft' => Heroicon::OutlinedPencil,
                        'reviewing' => Heroicon::OutlinedClock,
                        'published' => Heroicon::OutlinedCheckCircle,
                    })
                    ->color(fn (string $state): string => match ($state) {
                        'draft' => 'info',
                        'reviewing' => 'warning',
                        'published' => 'success',
                        default => 'gray',
                    })
                    ->wrap(),
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

    public function imageColumnSize(Table $table): Table
    {
        return $this->usersTable($table)
            ->columns([
                TextColumn::make('name'),
                ImageColumn::make('avatar')
                    ->imageSize(60),
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

    public function imageColumnStacked(Table $table): Table
    {
        return $this->usersTable($table)
            ->columns([
                TextColumn::make('name'),
                ImageColumn::make('colleagues')
                    ->circular()
                    ->stacked(),
            ]);
    }

    public function imageColumnLimited(Table $table): Table
    {
        return $this->usersTable($table)
            ->columns([
                TextColumn::make('name'),
                ImageColumn::make('colleagues')
                    ->circular()
                    ->stacked()
                    ->limit(3),
            ]);
    }

    public function imageColumnLimitedRemainingText(Table $table): Table
    {
        return $this->usersTable($table)
            ->columns([
                TextColumn::make('name'),
                ImageColumn::make('colleagues')
                    ->circular()
                    ->stacked()
                    ->limit(3)
                    ->limitedRemainingText(),
            ]);
    }

    public function imageColumnLimitedRemainingTextSeparately(Table $table): Table
    {
        return $this->usersTable($table)
            ->columns([
                TextColumn::make('name'),
                ImageColumn::make('colleagues')
                    ->circular()
                    ->stacked()
                    ->limit(3)
                    ->limitedRemainingText(isSeparate: true),
            ]);
    }

    public function imageColumnStackedRing(Table $table): Table
    {
        return $this->usersTable($table)
            ->columns([
                TextColumn::make('name'),
                ImageColumn::make('colleagues')
                    ->circular()
                    ->stacked()
                    ->ring(5),
            ]);
    }

    public function imageColumnStackedOverlap(Table $table): Table
    {
        return $this->usersTable($table)
            ->columns([
                TextColumn::make('name'),
                ImageColumn::make('colleagues')
                    ->circular()
                    ->stacked()
                    ->overlap(2),
            ]);
    }

    public function imageColumnWrap(Table $table): Table
    {
        return $this->usersTable($table)
            ->columns([
                TextColumn::make('name'),
                ImageColumn::make('colleagues')
                    ->circular()
                    ->stacked()
                    ->wrap(),
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

    public function colorColumnWrap(Table $table): Table
    {
        return $this->postsTable($table)
            ->columns([
                TextColumn::make('title')
                    ->grow(),
                ColorColumn::make('colors')
                    ->width('1%')
                    ->getStateUsing(fn ($rowLoop): array => match ($rowLoop->index) {
                        0 => ['#ef4444', '#fde047', '#22c55e', '#0ea5e9', '#8b5cf6', '#ec4899'],
                        1 => ['#ef4444', '#22c55e', '#0ea5e9', '#8b5cf6'],
                        2 => ['#fde047', '#22c55e', '#0ea5e9', '#ec4899', '#f97316'],
                        3 => ['#ef4444', '#fde047', '#8b5cf6'],
                        default => ['#0ea5e9', '#22c55e', '#ef4444', '#fde047', '#8b5cf6'],
                    })
                    ->wrap(),
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

    public function selectColumnJavascript(Table $table): Table
    {
        return $this->postsTable($table)
            ->columns([
                TextColumn::make('title'),
                SelectColumn::make('status')
                    ->options([
                        'draft' => 'Draft',
                        'reviewing' => 'Reviewing',
                        'published' => 'Published',
                    ])
                    ->native(false),
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

    public function textInputColumnAffix(Table $table): Table
    {
        return $this->postsTable($table)
            ->columns([
                TextColumn::make('title'),
                TextInputColumn::make('slug')
                    ->prefix('/')
                    ->suffix('.html'),
            ]);
    }

    public function textInputColumnPrefixIcon(Table $table): Table
    {
        return $this->postsTable($table)
            ->columns([
                TextColumn::make('title'),
                TextInputColumn::make('slug')
                    ->prefixIcon(Heroicon::GlobeAlt)
                    ->suffixIcon(Heroicon::CheckCircle),
            ]);
    }

    public function textInputColumnSuffixIconColor(Table $table): Table
    {
        return $this->usersTable($table)
            ->columns([
                TextColumn::make('name'),
                TextInputColumn::make('email')
                    ->suffixIcon(Heroicon::CheckCircle)
                    ->suffixIconColor('success'),
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

    public function filtersTable(Table $table): Table
    {
        return $this->postsTable($table)
            ->columns([
                TextColumn::make('title')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('slug'),
                TextColumn::make('status')
                    ->badge()
                    ->icon(fn (string $state): Heroicon => match ($state) {
                        'draft' => Heroicon::OutlinedPencil,
                        'reviewing' => Heroicon::OutlinedClock,
                        'published' => Heroicon::OutlinedCheckCircle,
                    })
                    ->color(fn (string $state): string => match ($state) {
                        'draft' => 'gray',
                        'reviewing' => 'warning',
                        'published' => 'success',
                    }),
                IconColumn::make('is_featured')
                    ->boolean(),
            ]);
    }

    public function filters(Table $table): Table
    {
        return $this->filtersTable($table)
            ->filters([
                Filter::make('is_featured'),
            ]);
    }

    public function filtersToggle(Table $table): Table
    {
        return $this->filtersTable($table)
            ->filters([
                Filter::make('is_featured')->toggle(),
            ]);
    }

    public function filtersSelect(Table $table): Table
    {
        return $this->filtersTable($table)
            ->filters([
                SelectFilter::make('status'),
            ]);
    }

    public function filtersCustomForm(Table $table): Table
    {
        return $this->filtersTable($table)
            ->filters([
                Filter::make('created_at')
                    ->schema([
                        DatePicker::make('created_from'),
                        DatePicker::make('created_until'),
                    ]),
            ]);
    }

    public function filtersIndicators(Table $table): Table
    {
        return $this->filtersTable($table)
            ->filters([
                Filter::make('dummy')
                    ->indicateUsing(fn () => [
                        Indicator::make('Posted by administrator')
                            ->removeField('one'),
                        Indicator::make('Less than 1 year old')
                            ->removeField('two'),
                    ]),
            ]);
    }

    public function filtersAboveContent(Table $table): Table
    {
        return $this->filtersTable($table)
            ->filters([
                SelectFilter::make('status'),
                SelectFilter::make('author'),
                Filter::make('created_at')
                    ->schema([
                        DatePicker::make('created_from'),
                        DatePicker::make('created_until'),
                    ])
                    ->columns(2)
                    ->columnSpan(2),
            ], layout: FiltersLayout::AboveContent)
            ->filtersFormColumns(4);
    }

    public function filtersAboveContentCollapsible(Table $table): Table
    {
        return $this->filtersTable($table)
            ->filters([
                SelectFilter::make('status'),
                SelectFilter::make('author'),
                Filter::make('created_at')
                    ->schema([
                        DatePicker::make('created_from'),
                        DatePicker::make('created_until'),
                    ])
                    ->columns(2)
                    ->columnSpan(2),
            ], layout: FiltersLayout::AboveContentCollapsible)
            ->filtersFormColumns(4);
    }

    public function filtersBelowContent(Table $table): Table
    {
        return $this->filtersTable($table)
            ->filters([
                SelectFilter::make('status'),
                SelectFilter::make('author'),
                Filter::make('created_at')
                    ->schema([
                        DatePicker::make('created_from'),
                        DatePicker::make('created_until'),
                    ])
                    ->columns(2)
                    ->columnSpan(2),
            ], layout: FiltersLayout::BelowContent)
            ->filtersFormColumns(4);
    }

    public function filtersBeforeContent(Table $table): Table
    {
        return $this->filtersTable($table)
            ->filters([
                SelectFilter::make('status'),
                SelectFilter::make('author'),
                Filter::make('is_featured'),
            ], layout: FiltersLayout::BeforeContent);
    }

    public function filtersAfterContent(Table $table): Table
    {
        return $this->filtersTable($table)
            ->filters([
                SelectFilter::make('status'),
                SelectFilter::make('author'),
                Filter::make('is_featured'),
            ], layout: FiltersLayout::AfterContent);
    }

    public function filtersCustomFormSchema(Table $table): Table
    {
        return $this->filtersTable($table)
            ->filters([
                TernaryFilter::make('is_featured'),
                SelectFilter::make('status')
                    ->options([
                        'draft' => 'Draft',
                        'reviewing' => 'Reviewing',
                        'published' => 'Published',
                    ]),
                SelectFilter::make('author'),
            ])
            ->filtersFormColumns(2)
            ->filtersFormWidth(Width::FourExtraLarge)
            ->filtersFormSchema(fn (array $filters): array => [
                Section::make('Visibility')
                    ->description('These filters affect the visibility of the records in the table.')
                    ->schema([
                        $filters['is_featured'],
                        $filters['status'],
                    ])
                    ->columns(2)
                    ->columnSpanFull(),
                $filters['author'],
            ]);
    }

    public function filtersCustomTriggerAction(Table $table): Table
    {
        return $this->filtersTable($table)
            ->filters([
                Filter::make('is_featured'),
            ])
            ->filtersTriggerAction(
                fn (Action $action) => $action
                    ->button()
                    ->label('Filter'),
            );
    }

    public function filtersGridColumns(Table $table): Table
    {
        return $this->filtersTable($table)
            ->filters([
                SelectFilter::make('status')
                    ->options([
                        'draft' => 'Draft',
                        'reviewing' => 'Reviewing',
                        'published' => 'Published',
                    ]),
                SelectFilter::make('author'),
                TernaryFilter::make('is_featured'),
            ])
            ->filtersFormColumns(3)
            ->filtersFormWidth(Width::FourExtraLarge);
    }

    public function filtersModal(Table $table): Table
    {
        return $this->filtersTable($table)
            ->filters([
                SelectFilter::make('status')
                    ->options([
                        'draft' => 'Draft',
                        'reviewing' => 'Reviewing',
                        'published' => 'Published',
                    ]),
                SelectFilter::make('author'),
                Filter::make('is_featured'),
            ], layout: FiltersLayout::Modal);
    }

    public function filtersTernary(Table $table): Table
    {
        return $this->filtersTable($table)
            ->filters([
                TernaryFilter::make('is_featured'),
            ]);
    }

    public function filtersQueryBuilder(Table $table): Table
    {
        return $this->filtersTable($table)
            ->filters([
                QueryBuilder::make()
                    ->constraints([
                        TextConstraint::make('title'),
                        BooleanConstraint::make('is_featured'),
                        SelectConstraint::make('status')
                            ->options([
                                'draft' => 'Draft',
                                'reviewing' => 'Reviewing',
                                'published' => 'Published',
                            ]),
                        DateConstraint::make('created_at'),
                    ]),
            ], layout: FiltersLayout::AboveContent);
    }

    public function actionsTable(Table $table): Table
    {
        return $this->postsTable($table)
            ->columns([
                TextColumn::make('title')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('slug'),
                TextColumn::make('status')
                    ->badge()
                    ->icon(fn (string $state): Heroicon => match ($state) {
                        'draft' => Heroicon::OutlinedPencil,
                        'reviewing' => Heroicon::OutlinedClock,
                        'published' => Heroicon::OutlinedCheckCircle,
                    })
                    ->color(fn (string $state): string => match ($state) {
                        'draft' => 'gray',
                        'reviewing' => 'warning',
                        'published' => 'success',
                    }),
                IconColumn::make('is_featured')
                    ->boolean(),
            ]);
    }

    public function actions(Table $table): Table
    {
        return $this->actionsTable($table)
            ->actions([
                EditAction::make(),
                DeleteAction::make(),
            ]);
    }

    public function actionsBeforeColumns(Table $table): Table
    {
        return $this->actionsTable($table)
            ->actions([
                EditAction::make(),
                DeleteAction::make(),
            ], position: RecordActionsPosition::BeforeColumns);
    }

    public function actionsBeforeCells(Table $table): Table
    {
        return $this->actionsTable($table)
            ->actions([
                EditAction::make(),
                DeleteAction::make(),
            ], position: RecordActionsPosition::BeforeCells)
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public function bulkActions(Table $table): Table
    {
        return $this->actionsTable($table)
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public function bulkActionsNotGrouped(Table $table): Table
    {
        return $this->actionsTable($table)
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    ForceDeleteBulkAction::make(),
                ]),
                BulkAction::make('export')->button(),
            ]);
    }

    public function headerActions(Table $table): Table
    {
        return $this->actionsTable($table)
            ->headerActions([
                CreateAction::make(),
            ]);
    }

    public function toolbarActions(Table $table): Table
    {
        return $this->actionsTable($table)
            ->toolbarActions([
                CreateAction::make(),
            ]);
    }

    public function groupedActions(Table $table): Table
    {
        return $this->actionsTable($table)
            ->actions([
                ActionGroup::make([
                    ViewAction::make(),
                    EditAction::make(),
                    DeleteAction::make(),
                ]),
            ]);
    }

    public function groupedActionsIconButton(Table $table): Table
    {
        return $this->actionsTable($table)
            ->actions([
                ActionGroup::make([
                    ViewAction::make(),
                    EditAction::make(),
                    DeleteAction::make(),
                ])->iconButton(),
            ]);
    }

    public function groupedActionsButton(Table $table): Table
    {
        return $this->actionsTable($table)
            ->actions([
                ActionGroup::make([
                    ViewAction::make(),
                    EditAction::make(),
                    DeleteAction::make(),
                ])
                    ->button()
                    ->label('Actions'),
            ]);
    }

    public function groupedActionsLink(Table $table): Table
    {
        return $this->actionsTable($table)
            ->actions([
                ActionGroup::make([
                    ViewAction::make(),
                    EditAction::make(),
                    DeleteAction::make(),
                ])
                    ->link()
                    ->label('Actions'),
            ]);
    }

    public function groupedActionsIcon(Table $table): Table
    {
        return $this->actionsTable($table)
            ->actions([
                ActionGroup::make([
                    ViewAction::make(),
                    EditAction::make(),
                    DeleteAction::make(),
                ])->icon(Heroicon::EllipsisHorizontal),
            ]);
    }

    public function groupedActionsColor(Table $table): Table
    {
        return $this->actionsTable($table)
            ->actions([
                ActionGroup::make([
                    ViewAction::make(),
                    EditAction::make(),
                    DeleteAction::make(),
                ])->color('info'),
            ]);
    }

    public function groupedActionsSmall(Table $table): Table
    {
        return $this->actionsTable($table)
            ->actions([
                ActionGroup::make([
                    ViewAction::make(),
                    EditAction::make(),
                    DeleteAction::make(),
                ])->size(Size::Small),
            ]);
    }

    public function groupedActionsTooltip(Table $table): Table
    {
        return $this->actionsTable($table)
            ->actions([
                ActionGroup::make([
                    ViewAction::make(),
                    EditAction::make(),
                    DeleteAction::make(),
                ])->tooltip('Actions'),
            ]);
    }

    public function layoutTable(Table $table): Table
    {
        return $this->usersTable($table)
            ->actions([
                EditAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public function layoutDemo(Table $table): Table
    {
        return $this->layoutTable($table)
            ->columns([
                Split::make([
                    ImageColumn::make('avatar')
                        ->circular()
                        ->grow(false),
                    Stack::make([
                        TextColumn::make('name')
                            ->weight(FontWeight::Bold)
                            ->searchable()
                            ->sortable(),
                        TextColumn::make('job'),
                    ]),
                    Stack::make([
                        TextColumn::make('phone')
                            ->icon(Heroicon::Phone),
                        TextColumn::make('email')
                            ->icon(Heroicon::Envelope),
                    ])
                        ->visibleFrom('md'),
                ]),
                Panel::make([
                    TextColumn::make('email')
                        ->icon(Heroicon::Envelope),
                    TextColumn::make('phone')
                        ->icon(Heroicon::Phone),
                ])->collapsible(),
            ]);
    }

    public function layoutSplit(Table $table): Table
    {
        return $this->layoutTable($table)
            ->columns([
                Split::make([
                    ImageColumn::make('avatar')
                        ->circular(),
                    TextColumn::make('name')
                        ->weight(FontWeight::Bold)
                        ->searchable()
                        ->sortable(),
                    TextColumn::make('email'),
                ]),
            ]);
    }

    public function layoutSplitDesktop(Table $table): Table
    {
        return $this->layoutTable($table)
            ->columns([
                Split::make([
                    ImageColumn::make('avatar')
                        ->circular(),
                    TextColumn::make('name')
                        ->weight(FontWeight::Bold)
                        ->searchable()
                        ->sortable(),
                    TextColumn::make('email'),
                ])->from('md'),
            ]);
    }

    public function layoutGrowDisabled(Table $table): Table
    {
        return $this->layoutTable($table)
            ->columns([
                Split::make([
                    ImageColumn::make('avatar')
                        ->circular()
                        ->grow(false),
                    TextColumn::make('name')
                        ->weight(FontWeight::Bold)
                        ->searchable()
                        ->sortable(),
                    TextColumn::make('email'),
                ])->from('md'),
            ]);
    }

    public function layoutStack(Table $table): Table
    {
        return $this->layoutTable($table)
            ->columns([
                Split::make([
                    ImageColumn::make('avatar')
                        ->circular()
                        ->grow(false),
                    TextColumn::make('name')
                        ->weight(FontWeight::Bold)
                        ->searchable()
                        ->sortable(),
                    Stack::make([
                        TextColumn::make('phone')
                            ->icon(Heroicon::Phone),
                        TextColumn::make('email')
                            ->icon(Heroicon::Envelope),
                    ]),
                ])->from('md'),
            ]);
    }

    public function layoutStackHiddenOnMobile(Table $table): Table
    {
        return $this->layoutTable($table)
            ->columns([
                Split::make([
                    ImageColumn::make('avatar')
                        ->circular()
                        ->grow(false),
                    TextColumn::make('name')
                        ->weight(FontWeight::Bold)
                        ->searchable()
                        ->sortable(),
                    Stack::make([
                        TextColumn::make('phone')
                            ->icon(Heroicon::Phone),
                        TextColumn::make('email')
                            ->icon(Heroicon::Envelope),
                    ])->visibleFrom('md'),
                ])->from('md'),
            ]);
    }

    public function layoutStackAlignedRight(Table $table): Table
    {
        return $this->layoutTable($table)
            ->columns([
                Split::make([
                    ImageColumn::make('avatar')
                        ->circular()
                        ->grow(false),
                    TextColumn::make('name')
                        ->weight(FontWeight::Bold)
                        ->searchable()
                        ->sortable(),
                    Stack::make([
                        TextColumn::make('phone')
                            ->icon(Heroicon::Phone)
                            ->grow(false),
                        TextColumn::make('email')
                            ->icon(Heroicon::Envelope)
                            ->grow(false),
                    ])
                        ->alignment(Alignment::End)
                        ->visibleFrom('md'),
                ])->from('md'),
            ]);
    }

    public function layoutStackSpaced(Table $table): Table
    {
        return $this->layoutTable($table)
            ->columns([
                Split::make([
                    ImageColumn::make('avatar')
                        ->circular()
                        ->grow(false),
                    TextColumn::make('name')
                        ->weight(FontWeight::Bold)
                        ->searchable()
                        ->sortable(),
                    Stack::make([
                        TextColumn::make('phone')
                            ->icon(Heroicon::Phone),
                        TextColumn::make('email')
                            ->icon(Heroicon::Envelope),
                    ])->space(1),
                ])->from('md'),
            ]);
    }

    public function layoutCollapsible(Table $table): Table
    {
        return $this->layoutTable($table)
            ->columns([
                Split::make([
                    ImageColumn::make('avatar')
                        ->circular()
                        ->grow(false),
                    TextColumn::make('name')
                        ->weight(FontWeight::Bold)
                        ->searchable()
                        ->sortable(),
                ])->from('md'),
                Panel::make([
                    Split::make([
                        TextColumn::make('phone')
                            ->icon(Heroicon::Phone),
                        TextColumn::make('email')
                            ->icon(Heroicon::Envelope),
                    ])->from('md'),
                ])->collapsible(),
            ]);
    }

    public function layoutGrid(Table $table): Table
    {
        return $this->layoutTable($table)
            ->columns([
                Stack::make([
                    ImageColumn::make('avatar')
                        ->circular()
                        ->grow(false),
                    TextColumn::make('name')
                        ->weight(FontWeight::Bold)
                        ->searchable()
                        ->sortable(),
                    TextColumn::make('job'),
                ]),
                Panel::make([])->collapsible(),
            ])
            ->contentGrid([
                'md' => 2,
                'xl' => 3,
            ])
            ->paginated([6])
            ->defaultPaginationPageOption(6);
    }

    public function layoutColumnGrid(Table $table): Table
    {
        return $this->layoutTable($table)
            ->columns([
                Split::make([
                    ImageColumn::make('avatar')
                        ->circular()
                        ->grow(false),
                    Stack::make([
                        TextColumn::make('name')
                            ->weight(FontWeight::Bold)
                            ->searchable()
                            ->sortable(),
                        TextColumn::make('job'),
                    ]),
                    Grid::make([
                        'lg' => 2,
                    ])
                        ->schema([
                            TextColumn::make('phone')
                                ->icon(Heroicon::Phone),
                            TextColumn::make('email')
                                ->icon(Heroicon::Envelope),
                        ]),
                ])->from('md'),
            ]);
    }

    public function layoutStackedOnMobile(Table $table): Table
    {
        return $this->layoutTable($table)
            ->columns([
                TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('email'),
                TextColumn::make('phone'),
                TextColumn::make('job'),
            ])
            ->stackedOnMobile();
    }

    public function summaries(Table $table): Table
    {
        return $this->postsTable($table)
            ->columns([
                TextColumn::make('title')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('slug'),
                TextColumn::make('rating')
                    ->numeric()
                    ->summarize([
                        Average::make(),
                        Range::make(),
                    ]),
                TextColumn::make('status')
                    ->badge()
                    ->icon(fn (string $state): Heroicon => match ($state) {
                        'draft' => Heroicon::OutlinedPencil,
                        'reviewing' => Heroicon::OutlinedClock,
                        'published' => Heroicon::OutlinedCheckCircle,
                    })
                    ->color(fn (string $state): string => match ($state) {
                        'draft' => 'gray',
                        'reviewing' => 'warning',
                        'published' => 'success',
                    }),
                IconColumn::make('is_featured')
                    ->boolean()
                    ->summarize([
                        Count::make()
                            ->query(fn ($query) => $query->where('is_featured', true)),
                    ]),
            ]);
    }

    public function grouping(Table $table): Table
    {
        return $this->postsTable($table, hasSeededPosts: false)
            ->columns([
                TextColumn::make('title')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('slug'),
                TextColumn::make('author.name')
                    ->numeric(),
                TextColumn::make('status')
                    ->badge()
                    ->icon(fn (string $state): Heroicon => match ($state) {
                        'draft' => Heroicon::OutlinedPencil,
                        'reviewing' => Heroicon::OutlinedClock,
                        'published' => Heroicon::OutlinedCheckCircle,
                    })
                    ->color(fn (string $state): string => match ($state) {
                        'draft' => 'gray',
                        'reviewing' => 'warning',
                        'published' => 'success',
                    }),
            ])
            ->defaultGroup('status');
    }

    public function groupingDescriptions(Table $table): Table
    {
        return $this->postsTable($table, hasSeededPosts: false)
            ->columns([
                TextColumn::make('title')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('slug'),
                TextColumn::make('author.name')
                    ->numeric(),
                TextColumn::make('status')
                    ->badge()
                    ->icon(fn (string $state): Heroicon => match ($state) {
                        'draft' => Heroicon::OutlinedPencil,
                        'reviewing' => Heroicon::OutlinedClock,
                        'published' => Heroicon::OutlinedCheckCircle,
                    })
                    ->color(fn (string $state): string => match ($state) {
                        'draft' => 'gray',
                        'reviewing' => 'warning',
                        'published' => 'success',
                    }),
            ])
            ->defaultGroup(Group::make('status')->getDescriptionFromRecordUsing(fn (Post $post): string => match ($post->status) {
                'draft' => 'Posts that are still being written.',
                'reviewing' => 'Posts that are being checked by the content team.',
                'published' => 'Posts that are public on the website.',
            }));
    }

    public function groupingCollapsible(Table $table): Table
    {
        return $this->postsTable($table, hasSeededPosts: false)
            ->columns([
                TextColumn::make('title')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('slug'),
                TextColumn::make('author.name')
                    ->numeric(),
                TextColumn::make('status')
                    ->badge()
                    ->icon(fn (string $state): Heroicon => match ($state) {
                        'draft' => Heroicon::OutlinedPencil,
                        'reviewing' => Heroicon::OutlinedClock,
                        'published' => Heroicon::OutlinedCheckCircle,
                    })
                    ->color(fn (string $state): string => match ($state) {
                        'draft' => 'gray',
                        'reviewing' => 'warning',
                        'published' => 'success',
                    }),
            ])
            ->defaultGroup(Group::make('status')->collapsible())
            ->groupingSettingsHidden();
    }

    public function groupingSelectable(Table $table): Table
    {
        return $this->postsTable($table, hasSeededPosts: false)
            ->columns([
                TextColumn::make('title')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('slug'),
                TextColumn::make('author.name')
                    ->numeric(),
                TextColumn::make('status')
                    ->badge()
                    ->icon(fn (string $state): Heroicon => match ($state) {
                        'draft' => Heroicon::OutlinedPencil,
                        'reviewing' => Heroicon::OutlinedClock,
                        'published' => Heroicon::OutlinedCheckCircle,
                    })
                    ->color(fn (string $state): string => match ($state) {
                        'draft' => 'gray',
                        'reviewing' => 'warning',
                        'published' => 'success',
                    }),
            ])
            ->groups([
                Group::make('status'),
                Group::make('author.name'),
            ])
            ->defaultGroup('status');
    }

    public function groupingDate(Table $table): Table
    {
        return $this->postsTable($table, hasSeededPosts: false)
            ->columns([
                TextColumn::make('title')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('slug'),
                TextColumn::make('author.name')
                    ->numeric(),
                TextColumn::make('status')
                    ->badge()
                    ->icon(fn (string $state): Heroicon => match ($state) {
                        'draft' => Heroicon::OutlinedPencil,
                        'reviewing' => Heroicon::OutlinedClock,
                        'published' => Heroicon::OutlinedCheckCircle,
                    })
                    ->color(fn (string $state): string => match ($state) {
                        'draft' => 'gray',
                        'reviewing' => 'warning',
                        'published' => 'success',
                    }),
            ])
            ->defaultGroup(Group::make('created_at')->date());
    }

    public function groupingGroupsOnly(Table $table): Table
    {
        return $this->postsTable($table, hasSeededPosts: false)
            ->columns([
                TextColumn::make('title')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('rating')
                    ->summarize(Average::make()),
                TextColumn::make('status')
                    ->badge()
                    ->icon(fn (string $state): Heroicon => match ($state) {
                        'draft' => Heroicon::OutlinedPencil,
                        'reviewing' => Heroicon::OutlinedClock,
                        'published' => Heroicon::OutlinedCheckCircle,
                    })
                    ->color(fn (string $state): string => match ($state) {
                        'draft' => 'gray',
                        'reviewing' => 'warning',
                        'published' => 'success',
                    }),
            ])
            ->defaultGroup('status')
            ->groupsOnly();
    }

    public function summaryAverage(Table $table): Table
    {
        return $this->postsTable($table, hasSeededPosts: false)
            ->columns([
                TextColumn::make('title'),
                TextColumn::make('rating')
                    ->numeric(decimalPlaces: 1)
                    ->summarize(Average::make()),
            ]);
    }

    public function summarySum(Table $table): Table
    {
        return $this->postsTable($table, hasSeededPosts: false)
            ->columns([
                TextColumn::make('title'),
                TextColumn::make('rating')
                    ->numeric(decimalPlaces: 1)
                    ->summarize(Sum::make()),
            ]);
    }

    public function summaryCount(Table $table): Table
    {
        return $this->postsTable($table, hasSeededPosts: false)
            ->columns([
                TextColumn::make('title'),
                IconColumn::make('is_featured')
                    ->boolean()
                    ->summarize(Count::make()->icons()),
            ]);
    }

    public function summaryRange(Table $table): Table
    {
        return $this->postsTable($table, hasSeededPosts: false)
            ->columns([
                TextColumn::make('title'),
                TextColumn::make('rating')
                    ->numeric(decimalPlaces: 1)
                    ->summarize(Range::make()),
            ]);
    }

    public function emptyState(Table $table): Table
    {
        Post::truncate();

        return $table
            ->query(Post::query());
    }

    public function emptyStateHeading(Table $table): Table
    {
        return $this->emptyState($table)
            ->emptyStateHeading('No posts yet');
    }

    public function emptyStateDescription(Table $table): Table
    {
        return $this->emptyStateHeading($table)
            ->emptyStateDescription('Once you write your first post, it will appear here.');
    }

    public function emptyStateIcon(Table $table): Table
    {
        return $this->emptyStateDescription($table)
            ->emptyStateIcon(Heroicon::OutlinedBookmark);
    }

    public function emptyStateActions(Table $table): Table
    {
        return $this->emptyStateIcon($table)
            ->emptyStateActions([
                Action::make('create')
                    ->label('Create post')
                    ->icon(Heroicon::Plus)
                    ->button(),
            ]);
    }

    public function reordering(Table $table): Table
    {
        return $this->postsTable($table)
            ->query(Post::query()->limit(5)->orderBy('title'))
            ->paginated(false)
            ->columns([
                TextColumn::make('title')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('slug'),
                TextColumn::make('author.name')
                    ->numeric(),
                TextColumn::make('status')
                    ->badge()
                    ->icon(fn (string $state): Heroicon => match ($state) {
                        'draft' => Heroicon::OutlinedPencil,
                        'reviewing' => Heroicon::OutlinedClock,
                        'published' => Heroicon::OutlinedCheckCircle,
                    })
                    ->color(fn (string $state): string => match ($state) {
                        'draft' => 'gray',
                        'reviewing' => 'warning',
                        'published' => 'success',
                    }),
            ])
            ->reorderable('rating');
    }

    public function reorderingCustomTriggerAction(Table $table): Table
    {
        return $this->reordering($table)
            ->reorderRecordsTriggerAction(
                fn (Action $action, bool $isReordering) => $action
                    ->button()
                    ->label($isReordering ? 'Disable reordering' : 'Enable reordering'),
            );
    }

    public function striped(Table $table): Table
    {
        return $this->example($table)
            ->striped();
    }

    public function postsTable(Table $table, bool $hasSeededPosts = true): Table
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
                'rating' => 8.1,
                'created_at' => '2025-02-10 09:15:00',
                'updated_at' => '2025-02-10 09:15:00',
            ],
            [
                'title' => 'Top 5 best features of Filament',
                'slug' => 'top-5-features',
                'description' => 'Discover the top 5 best features of Filament and how they can help you build your next project.',
                'is_featured' => false,
                'status' => 'reviewing',
                'author_id' => User::factory()->create(['name' => 'Ryan Chandler'])->id,
                'rating' => 9.3,
                'created_at' => '2025-02-14 14:30:00',
                'updated_at' => '2025-02-14 14:30:00',
            ],
            [
                'title' => 'Tips for building a great Filament plugin',
                'slug' => 'plugin-tips',
                'description' => 'Learn how to build a great Filament plugin and get it featured in the official plugin directory.',
                'is_featured' => true,
                'status' => 'draft',
                'author_id' => User::factory()->create(['name' => 'Zep Fietje'])->id,
                'rating' => 9.7,
                'created_at' => '2025-02-18 11:00:00',
                'updated_at' => '2025-02-18 11:00:00',
            ],
            [
                'title' => 'Customizing Filament\'s UI with a theme',
                'slug' => 'theme-guide',
                'description' => 'Learn how to customize Filament\'s UI with a theme and make it your own.',
                'is_featured' => false,
                'status' => 'reviewing',
                'author_id' => User::factory()->create(['name' => 'Dennis Koch'])->id,
                'rating' => 9.5,
                'created_at' => '2025-02-22 16:45:00',
                'updated_at' => '2025-02-22 16:45:00',
            ],
            [
                'title' => 'New Filament plugins in August',
                'slug' => 'new-plugins-august',
                'description' => 'Discover the latest Filament plugins that were released in August.',
                'is_featured' => false,
                'status' => 'published',
                'author_id' => User::factory()->create(['name' => 'Adam Weston'])->id,
                'rating' => 8.4,
                'created_at' => '2025-02-28 10:20:00',
                'updated_at' => '2025-02-28 10:20:00',
            ],
        ]);

        if ($hasSeededPosts) {
            Post::factory()->count(45)->create();
        }

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
                'phone' => '+1 (555) 555-5555',
                'job' => 'Developer',
                'colleagues' => json_encode([
                    'https://avatars.githubusercontent.com/u/41837763?v=4',
                    'https://avatars.githubusercontent.com/u/44533235?v=4',
                    'https://avatars.githubusercontent.com/u/22632550?v=4',
                    'https://avatars.githubusercontent.com/u/3596800?v=4',
                    'https://avatars.githubusercontent.com/u/881938?v=4',
                ]),
            ],
            [
                'name' => 'Ryan Chandler',
                'email' => 'ryan@filamentphp.com',
                'email_verified_at' => null,
                'password' => 'password',
                'avatar' => 'https://avatars.githubusercontent.com/u/41837763?v=4',
                'phone' => '+1 (555) 555-5555',
                'job' => 'Developer',
                'colleagues' => json_encode([
                    'https://avatars.githubusercontent.com/u/41773797?v=4',
                    'https://avatars.githubusercontent.com/u/44533235?v=4',
                    'https://avatars.githubusercontent.com/u/22632550?v=4',
                    'https://avatars.githubusercontent.com/u/3596800?v=4',
                    'https://avatars.githubusercontent.com/u/881938?v=4',
                ]),
            ],
            [
                'name' => 'Zep Fietje',
                'email' => 'zep@filamentphp.com',
                'email_verified_at' => null,
                'password' => 'password',
                'avatar' => 'https://avatars.githubusercontent.com/u/44533235?v=4',
                'phone' => '+1 (555) 555-5555',
                'job' => 'Developer',
                'colleagues' => json_encode([
                    'https://avatars.githubusercontent.com/u/41773797?v=4',
                    'https://avatars.githubusercontent.com/u/41837763?v=4',
                    'https://avatars.githubusercontent.com/u/22632550?v=4',
                    'https://avatars.githubusercontent.com/u/3596800?v=4',
                ]),
            ],
            [
                'name' => 'Dennis Koch',
                'email' => 'dennis@filamentphp.com',
                'email_verified_at' => '2023-08-01 11:30:00',
                'password' => 'password',
                'avatar' => 'https://avatars.githubusercontent.com/u/22632550?v=4',
                'phone' => '+1 (555) 555-5555',
                'job' => 'Developer',
                'colleagues' => json_encode([
                    'https://avatars.githubusercontent.com/u/41773797?v=4',
                    'https://avatars.githubusercontent.com/u/41837763?v=4',
                    'https://avatars.githubusercontent.com/u/44533235?v=4',
                    'https://avatars.githubusercontent.com/u/3596800?v=4',
                ]),
            ],
            [
                'name' => 'Adam Weston',
                'email' => 'adam@filamentphp.com',
                'email_verified_at' => '2023-08-01 11:30:00',
                'password' => 'password',
                'avatar' => 'https://avatars.githubusercontent.com/u/3596800?v=4',
                'phone' => '+1 (555) 555-5555',
                'job' => 'Developer',
                'colleagues' => json_encode([
                    'https://avatars.githubusercontent.com/u/41773797?v=4',
                    'https://avatars.githubusercontent.com/u/41837763?v=4',
                    'https://avatars.githubusercontent.com/u/44533235?v=4',
                    'https://avatars.githubusercontent.com/u/22632550?v=4',
                ]),
            ],
            [
                'name' => 'Ryan Scherler',
                'email' => 'ryans@filamentphp.com',
                'email_verified_at' => '2023-08-01 11:30:00',
                'password' => 'password',
                'avatar' => 'https://avatars.githubusercontent.com/u/881938?v=4',
                'phone' => '+1 (555) 555-5555',
                'job' => 'Developer',
                'colleagues' => json_encode([
                    'https://avatars.githubusercontent.com/u/41773797?v=4',
                    'https://avatars.githubusercontent.com/u/41837763?v=4',
                ]),
            ],
        ]);
        User::factory()->count(45)->create();

        return $table
            ->query(User::query())
            ->defaultPaginationPageOption(5);
    }

    public function tableCustomRowClasses(Table $table): Table
    {
        return $this->postsTable($table, hasSeededPosts: false)
            ->columns([
                TextColumn::make('title')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'draft' => 'gray',
                        'reviewing' => 'warning',
                        'published' => 'success',
                        default => 'gray',
                    }),
                TextColumn::make('author.name'),
            ])
            ->recordClasses(fn (Post $record) => match ($record->status) {
                'draft' => '!bg-danger-50 dark:!bg-danger-400/10',
                'reviewing' => '!bg-warning-50 dark:!bg-warning-400/10',
                'published' => '!bg-success-50 dark:!bg-success-400/10',
                default => null,
            });
    }

    public function tablePaginationDefault(Table $table): Table
    {
        return $this->postsTable($table)
            ->columns([
                TextColumn::make('title')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'draft' => 'gray',
                        'reviewing' => 'warning',
                        'published' => 'success',
                        default => 'gray',
                    }),
                TextColumn::make('author.name'),
            ])
            ->defaultPaginationPageOption(5);
    }

    public function tablePaginationExtreme(Table $table): Table
    {
        return $this->postsTable($table)
            ->columns([
                TextColumn::make('title')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'draft' => 'gray',
                        'reviewing' => 'warning',
                        'published' => 'success',
                        default => 'gray',
                    }),
                TextColumn::make('author.name'),
            ])
            ->extremePaginationLinks()
            ->defaultPaginationPageOption(5);
    }

    public function tablePaginationCursor(Table $table): Table
    {
        return $this->postsTable($table)
            ->columns([
                TextColumn::make('title')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'draft' => 'gray',
                        'reviewing' => 'warning',
                        'published' => 'success',
                        default => 'gray',
                    }),
                TextColumn::make('author.name'),
            ])
            ->paginationMode(PaginationMode::Cursor)
            ->defaultPaginationPageOption(5);
    }

    public function tablePaginationSimple(Table $table): Table
    {
        return $this->postsTable($table)
            ->columns([
                TextColumn::make('title')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'draft' => 'gray',
                        'reviewing' => 'warning',
                        'published' => 'success',
                        default => 'gray',
                    }),
                TextColumn::make('author.name'),
            ])
            ->paginationMode(PaginationMode::Simple)
            ->defaultPaginationPageOption(3);
    }

    public function columnExpandableLimitedList(Table $table): Table
    {
        return $this->usersTable($table)
            ->columns([
                TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('email')
                    ->label('Email addresses')
                    ->getStateUsing(fn ($record): array => [
                        $record->email,
                        str($record->email)->replace('filamentphp.com', 'filament.dev'),
                        str($record->email)->replace('filamentphp.com', 'example.com'),
                        str($record->email)->replace('filamentphp.com', 'company.org'),
                    ])
                    ->listWithLineBreaks()
                    ->limitList(2)
                    ->expandableLimitedList(),
            ]);
    }

    public function filtersMultiSelect(Table $table): Table
    {
        return $this->filtersTable($table)
            ->filters([
                SelectFilter::make('status')
                    ->multiple()
                    ->native(false)
                    ->options([
                        'draft' => 'Draft',
                        'reviewing' => 'Reviewing',
                        'published' => 'Published',
                    ]),
            ]);
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
