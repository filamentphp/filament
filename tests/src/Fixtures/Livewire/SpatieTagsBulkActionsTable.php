<?php

namespace Filament\Tests\Fixtures\Livewire;

use Filament\Actions\AttachSpatieTagsBulkAction;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Actions\DetachSpatieTagsBulkAction;
use Filament\Actions\ManageSpatieTagsBulkAction;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Filament\Tests\Fixtures\Models\Article;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Component;

class SpatieTagsBulkActionsTable extends Component implements HasActions, HasSchemas, HasTable
{
    use InteractsWithActions;
    use InteractsWithSchemas;
    use InteractsWithTable;

    public ?string $tagType = null;

    public bool $useNullType = false;

    protected function getTableQuery(): Builder
    {
        return Article::query();
    }

    public function table(Table $table): Table
    {
        $attachTagsAction = AttachSpatieTagsBulkAction::make();
        $detachTagsAction = DetachSpatieTagsBulkAction::make();
        $manageTagsAction = ManageSpatieTagsBulkAction::make();

        if ($this->useNullType) {
            $attachTagsAction->type(null);
            $detachTagsAction->type(null);
            $manageTagsAction->type(null);
        } elseif ($this->tagType !== null) {
            $attachTagsAction->type($this->tagType);
            $detachTagsAction->type($this->tagType);
            $manageTagsAction->type($this->tagType);
        }

        return $table
            ->columns([
                TextColumn::make('title'),
            ])
            ->toolbarActions([
                $attachTagsAction,
                $detachTagsAction,
                $manageTagsAction,
            ]);
    }

    public function render(): View
    {
        return view('livewire.table');
    }
}
