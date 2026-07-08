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

    public bool $shouldFetchSelectedRecords = true;

    public bool $authorizeUsingPublished = false;

    protected function getTableQuery(): Builder
    {
        return Article::query();
    }

    public function table(Table $table): Table
    {
        $attachTagsAction = AttachSpatieTagsBulkAction::make();
        $detachTagsAction = DetachSpatieTagsBulkAction::make();
        $manageTagsAction = ManageSpatieTagsBulkAction::make();

        $actions = [$attachTagsAction, $detachTagsAction, $manageTagsAction];

        foreach ($actions as $action) {
            if ($this->useNullType) {
                $action->type(null);
            } elseif ($this->tagType !== null) {
                $action->type($this->tagType);
            }

            if (! $this->shouldFetchSelectedRecords) {
                // Exercises the `getSelectedRecordsQuery()->cursor()` branch instead of the eager fetch.
                $action->fetchSelectedRecords(false);
            }

            if ($this->authorizeUsingPublished) {
                // Skips unpublished records so the individual-authorization path documented in the
                // README is exercised: authorized records are processed and denied ones are reported.
                $action->authorizeIndividualRecords(fn (Article $record): bool => (bool) $record->is_published);
            }
        }

        return $table
            ->columns([
                TextColumn::make('title'),
            ])
            ->toolbarActions($actions);
    }

    public function render(): View
    {
        return view('livewire.table');
    }
}
