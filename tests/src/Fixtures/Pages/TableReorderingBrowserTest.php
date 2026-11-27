<?php

namespace Filament\Tests\Fixtures\Pages;

use Filament\Actions\Action;
use Filament\Pages\Page;
use Filament\Schemas\Components\EmbeddedTable;
use Filament\Schemas\Components\Livewire;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tests\Fixtures\Models\Post;
use Filament\Tests\Fixtures\Models\User;
use Filament\Tests\Fixtures\Resources\Users\Pages\ViewUser;
use Filament\Tests\Fixtures\Resources\Users\RelationManagers\ReorderableRelatedPostsRelationManager;
use Livewire\Attributes\Url;

class TableReorderingBrowserTest extends Page implements Tables\Contracts\HasTable
{
    use Tables\Concerns\InteractsWithTable;

    protected static bool $shouldRegisterNavigation = false;

    #[Url]
    public bool $grid = false;

    #[Url]
    public bool $authorized = true;

    #[Url]
    public ?int $relationOwner = null;

    public function table(Table $table): Table
    {
        $columns = [Tables\Columns\TextColumn::make('title')->searchable()];

        return $table
            ->query(Post::query())
            ->extraAttributes(['data-testid' => 'reordering-table'])
            ->columns($this->grid ? [Tables\Columns\Layout\Stack::make($columns)] : $columns)
            ->contentGrid($this->grid ? ['md' => 2] : null)
            ->recordTitleAttribute('title')
            ->reorderable('sort')
            ->reorderAnimationDuration(0)
            ->authorizeReorder($this->authorized)
            ->defaultSort('sort')
            ->paginated(false)
            ->reorderRecordsTriggerAction(static fn (Action $action): Action => $action->extraAttributes(['data-testid' => 'toggle-reordering']));
    }

    public function content(Schema $schema): Schema
    {
        if ($this->relationOwner) {
            return $schema->components([
                Livewire::make(ReorderableRelatedPostsRelationManager::class, fn (): array => [
                    'ownerRecord' => User::findOrFail($this->relationOwner),
                    'pageClass' => ViewUser::class,
                    'lazy' => false,
                ]),
            ]);
        }

        return $schema->components([EmbeddedTable::make()]);
    }
}
