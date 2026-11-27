<?php

namespace Filament\Tests\Tables\Fixtures;

use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\TextEntry;
use Filament\SpatieLaravelTranslatableContentDriver;
use Filament\Tables\Actions\BulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ReplicateAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Tests\Models\UserWithHiddenTeamPivot;
use Illuminate\Database\Eloquent\Collection;

class HiddenPivotTeamsTable extends PostsTable
{
    public int $userKey;

    public bool $duplicates = false;

    public bool $hasPivotFields = true;

    public bool $hasHiddenPivotField = true;

    public bool $isTranslatable = false;

    public function getFilamentTranslatableContentDriver(): ?string
    {
        return $this->isTranslatable ? SpatieLaravelTranslatableContentDriver::class : null;
    }

    public function table(Table $table): Table
    {
        $configuredRelationship = $table->getRelationship();
        $fields = $this->hasPivotFields ? [
            ...($this->hasHiddenPivotField ? [TextInput::make('created_at')] : []),
            TextInput::make('pivot_created_at')->dehydrated(false),
            TextInput::make('user_id')->dehydrated(false),
            TextInput::make('pivot_user_id')->dehydrated(false),
            TextInput::make('role'),
        ] : [TextInput::make('name')->required()];

        return $table
            ->relationship($configuredRelationship ? static fn () => clone $configuredRelationship : fn () => UserWithHiddenTeamPivot::findOrFail($this->userKey)->teams())
            ->allowDuplicates($this->duplicates)
            ->columns([TextColumn::make('name')])
            ->actions([
                EditAction::make()->form($fields),
                ViewAction::make()->form($fields),
                ReplicateAction::make()->form($fields),
                ViewAction::make('info')->infolist([
                    TextEntry::make('created_at'),
                    TextEntry::make('pivot_created_at'),
                    TextEntry::make('membership.created_at'),
                    TextEntry::make('role'),
                ]),
            ])
            ->bulkActions([
                BulkAction::make('inspect')
                    ->fillForm(static fn (Collection $records): array => $records->first()->attributesToArray())
                    ->form($fields),
            ]);
    }
}
