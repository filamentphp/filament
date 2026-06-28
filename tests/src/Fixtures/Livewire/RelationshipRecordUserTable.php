<?php

namespace Filament\Tests\Fixtures\Livewire;

use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Support\Icons\Heroicon;
use Filament\Tables;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Tests\Fixtures\Models\Post;
use Filament\Tests\Fixtures\Models\Team;
use Filament\Tests\Fixtures\Models\User;
use Illuminate\Contracts\View\View;
use Livewire\Component;

class RelationshipRecordUserTable extends Component implements HasActions, HasSchemas, Tables\Contracts\HasTable
{
    use InteractsWithActions;
    use InteractsWithSchemas;
    use Tables\Concerns\InteractsWithTable;

    public ?int $userId = null;

    public function table(Table $table): Table
    {
        return $table
            ->query(
                User::query()
                    ->when($this->userId, fn ($query) => $query->whereKey($this->userId)),
            )
            ->columns([
                TextColumn::make('teams.name')
                    ->badge()
                    ->listWithLineBreaks()
                    ->color(fn (Team $relationshipRecord): string => $relationshipRecord->name === 'Alpha' ? 'success' : 'danger')
                    ->tooltip(fn (Team $relationshipRecord): string => "Team #{$relationshipRecord->id}")
                    ->url(fn (Team $relationshipRecord): string => "/teams/{$relationshipRecord->id}"),
                TextColumn::make('team.name')
                    ->formatStateUsing(fn (string $state, Team $relationshipRecord): string => "{$state} (#{$relationshipRecord->id})"),
                TextColumn::make('name'),
            ])
            ->paginated(false);
    }

    public function render(): View
    {
        return view('livewire.table');
    }
}

class RelationshipRecordIconColumnTable extends Component implements HasActions, HasSchemas, Tables\Contracts\HasTable
{
    use InteractsWithActions;
    use InteractsWithSchemas;
    use Tables\Concerns\InteractsWithTable;

    public ?int $userId = null;

    public function table(Table $table): Table
    {
        return $table
            ->query(
                User::query()
                    ->when($this->userId, fn ($query) => $query->whereKey($this->userId)),
            )
            ->columns([
                IconColumn::make('teams.name')
                    ->listWithLineBreaks()
                    ->icon(fn (Team $relationshipRecord): Heroicon => $relationshipRecord->name === 'Alpha' ? Heroicon::CheckCircle : Heroicon::XCircle)
                    ->color(fn (Team $relationshipRecord): string => $relationshipRecord->name === 'Alpha' ? 'success' : 'danger'),
            ])
            ->paginated(false);
    }

    public function render(): View
    {
        return view('livewire.table');
    }
}

class RelationshipRecordPostTable extends Component implements HasActions, HasSchemas, Tables\Contracts\HasTable
{
    use InteractsWithActions;
    use InteractsWithSchemas;
    use Tables\Concerns\InteractsWithTable;

    public ?int $postId = null;

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Post::query()
                    ->when($this->postId, fn ($query) => $query->whereKey($this->postId)),
            )
            ->columns([
                TextColumn::make('author.team.name')
                    ->color(fn (Team $relationshipRecord): string => $relationshipRecord->name === 'Alpha' ? 'warning' : 'gray'),
            ])
            ->paginated(false);
    }

    public function render(): View
    {
        return view('livewire.table');
    }
}

class RelationshipRecordDistinctListTable extends Component implements HasActions, HasSchemas, Tables\Contracts\HasTable
{
    use InteractsWithActions;
    use InteractsWithSchemas;
    use Tables\Concerns\InteractsWithTable;

    public ?int $userId = null;

    public function table(Table $table): Table
    {
        return $table
            ->query(
                User::query()
                    ->when($this->userId, fn ($query) => $query->whereKey($this->userId)),
            )
            ->columns([
                TextColumn::make('teams.name')
                    ->distinctList()
                    ->listWithLineBreaks()
                    ->color(fn (Team $relationshipRecord): string => "team-{$relationshipRecord->id}"),
            ])
            ->paginated(false);
    }

    public function render(): View
    {
        return view('livewire.table');
    }
}

class RelationshipRecordLimitedListTable extends Component implements HasActions, HasSchemas, Tables\Contracts\HasTable
{
    use InteractsWithActions;
    use InteractsWithSchemas;
    use Tables\Concerns\InteractsWithTable;

    public ?int $userId = null;

    public function table(Table $table): Table
    {
        return $table
            ->query(
                User::query()
                    ->when($this->userId, fn ($query) => $query->whereKey($this->userId)),
            )
            ->columns([
                TextColumn::make('teams.name')
                    ->badge()
                    ->listWithLineBreaks()
                    ->limitList(1)
                    ->color(fn (Team $relationshipRecord): string => $relationshipRecord->name === 'Alpha' ? 'success' : 'danger'),
            ])
            ->paginated(false);
    }

    public function render(): View
    {
        return view('livewire.table');
    }
}

class RelationshipRecordBelongsToTooltipTable extends Component implements HasActions, HasSchemas, Tables\Contracts\HasTable
{
    use InteractsWithActions;
    use InteractsWithSchemas;
    use Tables\Concerns\InteractsWithTable;

    public ?int $userId = null;

    public function table(Table $table): Table
    {
        return $table
            ->query(
                User::query()
                    ->when($this->userId, fn ($query) => $query->whereKey($this->userId)),
            )
            ->columns([
                TextColumn::make('team.name')
                    ->tooltip(fn (Team $relationshipRecord): string => "Belongs to team {$relationshipRecord->id}"),
            ])
            ->paginated(false);
    }

    public function render(): View
    {
        return view('livewire.table');
    }
}
