<?php

namespace Filament\Tests\Fixtures\Livewire;

use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Actions\EditAction;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Tests\Fixtures\Models\Post;
use Livewire\Attributes\Locked;
use Livewire\Component;

class EditConflictActions extends Component implements HasActions, HasSchemas
{
    use InteractsWithActions;
    use InteractsWithSchemas;

    #[Locked]
    public Post $record;

    public function mount(Post $record): void
    {
        $this->record = $record;
    }

    public function editAction(): EditAction
    {
        return EditAction::make()
            ->record($this->record)
            ->editConflictHandler()
            ->authorize(static fn (Post $record): bool => $record->rating !== 10)
            ->schema([
                TextInput::make('title')->required()->extraInputAttributes(['data-testid' => 'edit-title']),
                TextInput::make('rating')->numeric()->required()->extraInputAttributes(['data-testid' => 'edit-rating']),
                TextInput::make('json.mine'),
            ])
            ->extraAttributes(['data-testid' => 'conflict-edit-trigger'])
            ->extraModalWindowAttributes(['data-testid' => 'conflict-edit-modal']);
    }

    public function render(): string
    {
        return '<div>{{ $this->editAction }}<x-filament-actions::modals /></div>';
    }
}
