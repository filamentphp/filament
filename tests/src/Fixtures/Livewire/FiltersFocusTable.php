<?php

namespace Filament\Tests\Fixtures\Livewire;

use Filament\Actions\Action;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tests\Fixtures\Models\Post;
use Illuminate\Contracts\View\View;
use Livewire\Component;

class FiltersFocusTable extends Component implements HasActions, HasSchemas, Tables\Contracts\HasTable
{
    use InteractsWithActions;
    use InteractsWithSchemas;
    use Tables\Concerns\InteractsWithTable;

    public function table(Table $table): Table
    {
        return $table
            ->query(Post::query())
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->toggleable(),
            ])
            ->filters([
                Tables\Filters\Filter::make('is_published')
                    ->schema([
                        Toggle::make('isActive')
                            ->label('Published')
                            ->extraAttributes(['data-testid' => 'published-filter']),
                    ]),
            ])
            ->filtersTriggerAction(
                static fn (Action $action): Action => $action
                    ->extraAttributes(['data-testid' => 'filters-trigger']),
            )
            ->paginated(false);
    }

    public function render(): View
    {
        return view('livewire.table');
    }
}
