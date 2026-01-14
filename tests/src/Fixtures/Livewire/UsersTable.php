<?php

namespace Filament\Tests\Fixtures\Livewire;

use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tests\Fixtures\Models\User;
use Illuminate\Contracts\View\View;
use Livewire\Component;

class UsersTable extends Component implements HasActions, HasSchemas, Tables\Contracts\HasTable
{
    use InteractsWithActions;
    use InteractsWithSchemas;
    use Tables\Concerns\InteractsWithTable;

    public function table(Table $table): Table
    {
        return $table
            ->query(User::query())
            ->groups(fn () => [
                Tables\Grouping\Group::make('name'),
                Tables\Grouping\Group::make('profile.company.name'),
                Tables\Grouping\Group::make('profile.setting.theme'),
                Tables\Grouping\Group::make('image.url'),
                Tables\Grouping\Group::make('setting.theme'),
            ])
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('email')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('profile.bio')
                    ->label('Profile Bio')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('profile.company.name')
                    ->label('Company')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('profile.setting.theme')
                    ->label('Theme')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('profile.setting.language')
                    ->label('Language')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('image.url')
                    ->label('Image URL')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('setting.theme')
                    ->label('Setting Theme (HasOneThrough)')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('setting.language')
                    ->label('Setting Language (HasOneThrough)')
                    ->sortable()
                    ->searchable(),
            ]);
    }

    public function render(): View
    {
        return view('livewire.table');
    }
}
