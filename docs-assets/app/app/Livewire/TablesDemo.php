<?php

namespace App\Livewire;

use App\Models\Post;
use App\Models\User;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Support\Enums\ActionSize;
use Filament\Support\Enums\Alignment;
use Filament\Support\Enums\FontFamily;
use Filament\Support\Enums\FontWeight;
use Filament\Support\Enums\IconPosition;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Actions\BulkAction;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\CreateAction;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ForceDeleteBulkAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\CheckboxColumn;
use Filament\Tables\Columns\ColorColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\Layout\Panel;
use Filament\Tables\Columns\Layout\Split;
use Filament\Tables\Columns\Layout\Stack;
use Filament\Tables\Columns\SelectColumn;
use Filament\Tables\Columns\Summarizers\Average;
use Filament\Tables\Columns\Summarizers\Count;
use Filament\Tables\Columns\Summarizers\Range;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\TextInputColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Enums\ActionsPosition;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Grouping\Group;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Component;
use stdClass;

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
                    ->iconPosition(IconPosition::After),
            ]);
    }

    public function textColumnLarge(Table $table): Table
    {
        return $this->postsTable($table)
            ->columns([
                TextColumn::make('title')
                    ->size(TextColumn\TextColumnSize::Large),
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
                    ->size(IconColumn\IconColumnSize::Medium),
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
                    ->icon(fn (string $state): string => match ($state) {
                        'draft' => 'heroicon-o-pencil',
                        'reviewing' => 'heroicon-o-clock',
                        'published' => 'heroicon-o-check-circle',
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
                    ->form([
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
                        'one' => 'Posted by administrator',
                        'two' => 'Less than 1 year old',
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
                    ->form([
                        DatePicker::make('created_from'),
                        DatePicker::make('created_until'),
                    ])
                    ->columns(2)
                    ->columnSpan(2),
            ], layout: FiltersLayout::AboveContent)
            ->filtersFormColumns(4);
    }

    public function filtersBelowContent(Table $table): Table
    {
        return $this->filtersTable($table)
            ->filters([
                SelectFilter::make('status'),
                SelectFilter::make('author'),
                Filter::make('created_at')
                    ->form([
                        DatePicker::make('created_from'),
                        DatePicker::make('created_until'),
                    ])
                    ->columns(2)
                    ->columnSpan(2),
            ], layout: FiltersLayout::BelowContent)
            ->filtersFormColumns(4);
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
                    ->icon(fn (string $state): string => match ($state) {
                        'draft' => 'heroicon-o-pencil',
                        'reviewing' => 'heroicon-o-clock',
                        'published' => 'heroicon-o-check-circle',
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
            ], position: ActionsPosition::BeforeColumns);
    }

    public function actionsBeforeCells(Table $table): Table
    {
        return $this->actionsTable($table)
            ->actions([
                EditAction::make(),
                DeleteAction::make(),
            ], position: ActionsPosition::BeforeCells)
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
                ])->icon('heroicon-m-ellipsis-horizontal'),
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
                ])->size(ActionSize::Small),
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
                            ->icon('heroicon-m-phone'),
                        TextColumn::make('email')
                            ->icon('heroicon-m-envelope'),
                    ])
                        ->visibleFrom('md'),
                ]),
                Panel::make([
                    TextColumn::make('email')
                        ->icon('heroicon-m-envelope'),
                    TextColumn::make('phone')
                        ->icon('heroicon-m-phone'),
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
                            ->icon('heroicon-m-phone'),
                        TextColumn::make('email')
                            ->icon('heroicon-m-envelope'),
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
                            ->icon('heroicon-m-phone'),
                        TextColumn::make('email')
                            ->icon('heroicon-m-envelope'),
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
                            ->icon('heroicon-m-phone')
                            ->grow(false),
                        TextColumn::make('email')
                            ->icon('heroicon-m-envelope')
                            ->grow(false),
                    ])
                        ->alignment(Alignment::End)
                        ->visibleFrom('md'),
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
                            ->icon('heroicon-m-phone'),
                        TextColumn::make('email')
                            ->icon('heroicon-m-envelope'),
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
                    ->icon(fn (string $state): string => match ($state) {
                        'draft' => 'heroicon-o-pencil',
                        'reviewing' => 'heroicon-o-clock',
                        'published' => 'heroicon-o-check-circle',
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
                    ->icon(fn (string $state): string => match ($state) {
                        'draft' => 'heroicon-o-pencil',
                        'reviewing' => 'heroicon-o-clock',
                        'published' => 'heroicon-o-check-circle',
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
                    ->icon(fn (string $state): string => match ($state) {
                        'draft' => 'heroicon-o-pencil',
                        'reviewing' => 'heroicon-o-clock',
                        'published' => 'heroicon-o-check-circle',
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
            ->emptyStateIcon('heroicon-o-bookmark');
    }

    public function emptyStateActions(Table $table): Table
    {
        return $this->emptyStateIcon($table)
            ->emptyStateActions([
                Action::make('create')
                    ->label('Create post')
                    ->icon('heroicon-m-plus')
                    ->button(),
            ]);
    }

    public function reordering(Table $table): Table
    {
        return $this->postsTable($table)
            ->columns([
                TextColumn::make('title')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('slug'),
                TextColumn::make('author.name')
                    ->numeric(),
                TextColumn::make('status')
                    ->badge()
                    ->icon(fn (string $state): string => match ($state) {
                        'draft' => 'heroicon-o-pencil',
                        'reviewing' => 'heroicon-o-clock',
                        'published' => 'heroicon-o-check-circle',
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
            ],
            [
                'title' => 'Top 5 best features of Filament',
                'slug' => 'top-5-features',
                'description' => 'Discover the top 5 best features of Filament and how they can help you build your next project.',
                'is_featured' => false,
                'status' => 'reviewing',
                'author_id' => User::factory()->create(['name' => 'Ryan Chandler'])->id,
                'rating' => 9.3,
            ],
            [
                'title' => 'Tips for building a great Filament plugin',
                'slug' => 'plugin-tips',
                'description' => 'Learn how to build a great Filament plugin and get it featured in the official plugin directory.',
                'is_featured' => true,
                'status' => 'draft',
                'author_id' => User::factory()->create(['name' => 'Zep Fietje'])->id,
                'rating' => 9.7,
            ],
            [
                'title' => 'Customizing Filament\'s UI with a theme',
                'slug' => 'theme-guide',
                'description' => 'Learn how to customize Filament\'s UI with a theme and make it your own.',
                'is_featured' => false,
                'status' => 'reviewing',
                'author_id' => User::factory()->create(['name' => 'Dennis Koch'])->id,
                'rating' => 9.5,
            ],
            [
                'title' => 'New Filament plugins in August',
                'slug' => 'new-plugins-august',
                'description' => 'Discover the latest Filament plugins that were released in August.',
                'is_featured' => false,
                'status' => 'published',
                'author_id' => User::factory()->create(['name' => 'Adam Weston'])->id,
                'rating' => 8.4,
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

    public function table(Table $table): Table
    {
        return $this->{$this->tableConfiguration}($table);
    }

    public function render()
    {
        return view('livewire.tables');
    }
}
