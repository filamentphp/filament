<?php

namespace App\Livewire;

use Filament\Actions\Action;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Wizard\Step;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Support\Enums\IconPosition;
use Filament\Support\Enums\Size;
use Filament\Support\Icons\Heroicon;
use Livewire\Component;

class ActionsDemo extends Component implements HasActions, HasSchemas
{
    use InteractsWithActions;
    use InteractsWithSchemas;

    public function buttonAction(): Action
    {
        return Action::make('button')
            ->label('Edit')
            ->button();
    }

    public function linkAction(): Action
    {
        return Action::make('link')
            ->label('Edit')
            ->link();
    }

    public function iconButtonAction(): Action
    {
        return Action::make('iconButton')
            ->icon(Heroicon::PencilSquare)
            ->iconButton();
    }

    public function badgeAction(): Action
    {
        return Action::make('badge')
            ->label('Edit')
            ->badge();
    }

    public function dangerAction(): Action
    {
        return Action::make('danger')
            ->label('Delete')
            ->color('danger');
    }

    public function largeAction(): Action
    {
        return Action::make('large')
            ->label('Create')
            ->size(Size::Large);
    }

    public function iconAction(): Action
    {
        return Action::make('icon')
            ->label('Edit')
            ->icon(Heroicon::PencilSquare);
    }

    public function iconAfterAction(): Action
    {
        return Action::make('iconAfter')
            ->label('Edit')
            ->icon(Heroicon::PencilSquare)
            ->iconPosition(IconPosition::After);
    }

    public function badgedAction(): Action
    {
        return Action::make('badged')
            ->badge(5)
            ->icon(Heroicon::Funnel)
            ->iconButton();
    }

    public function successBadgedAction(): Action
    {
        return Action::make('successBadged')
            ->badge(5)
            ->badgeColor('success')
            ->icon(Heroicon::Funnel)
            ->iconButton();
    }

    public function outlinedAction(): Action
    {
        return Action::make('outlined')
            ->label('Edit')
            ->button()
            ->outlined();
    }

    public function confirmationModalAction(): Action
    {
        return Action::make('confirmationModal')
            ->label('Delete')
            ->color('danger')
            ->requiresConfirmation()
            ->action(fn () => null);
    }

    public function confirmationModalCustomTextAction(): Action
    {
        return Action::make('confirmationModalCustomText')
            ->label('Delete')
            ->color('danger')
            ->modalDescription('Are you sure you\'d like to delete this post? This cannot be undone.')
            ->modalHeading('Delete post')
            ->modalSubmitActionLabel('Yes, delete it')
            ->requiresConfirmation()
            ->action(fn () => null);
    }

    public function modalIconAction(): Action
    {
        return Action::make('modalIcon')
            ->label('Delete')
            ->action(fn () => null)
            ->color('danger')
            ->modalIcon(Heroicon::OutlinedTrash)
            ->requiresConfirmation();
    }

    public function modalFormAction(): Action
    {
        return Action::make('modalForm')
            ->label('Update author')
            ->schema([
                Select::make('authorId')
                    ->label('Author')
                    ->required(),
            ])
            ->action(fn () => null);
    }

    public function wizardAction(): Action
    {
        return Action::make('wizard')
            ->label('Create')
            ->steps([
                Step::make('Name')
                    ->columns(2)
                    ->description('Give the category unique name')
                    ->schema([
                        TextInput::make('name')
                            ->required(),
                        TextInput::make('slug')
                            ->disabled()
                            ->required(),
                    ]),
                Step::make('Description')
                    ->description('Add some extra details')
                    ->schema([]),
                Step::make('Visibility')
                    ->description('Control who can view it')
                    ->schema([]),
            ])
            ->action(fn () => null);
    }

    public function slideOverAction(): Action
    {
        return Action::make('slideOver')
            ->label('Update author')
            ->schema([
                Select::make('authorId')
                    ->label('Author')
                    ->required(),
            ])
            ->slideOver()
            ->action(fn () => null);
    }

    public function render()
    {
        return view('livewire.actions');
    }
}
