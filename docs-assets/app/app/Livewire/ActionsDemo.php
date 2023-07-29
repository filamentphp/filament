<?php

namespace App\Livewire;

use Filament\Actions\Action;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Wizard\Step;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Support\Enums\ActionSize;
use Filament\Support\Enums\IconPosition;
use Livewire\Component;

class ActionsDemo extends Component implements HasActions, HasForms
{
    use InteractsWithActions;
    use InteractsWithForms;

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
            ->icon('heroicon-m-pencil-square')
            ->iconButton();
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
            ->size(ActionSize::Large);
    }

    public function iconAction(): Action
    {
        return Action::make('icon')
            ->label('Edit')
            ->icon('heroicon-m-pencil-square');
    }

    public function iconAfterAction(): Action
    {
        return Action::make('iconAfter')
            ->label('Edit')
            ->icon('heroicon-m-pencil-square')
            ->iconPosition(IconPosition::After);
    }

    public function badgeAction(): Action
    {
        return Action::make('badge')
            ->iconButton()
            ->icon('heroicon-m-funnel')
            ->badge(5);
    }

    public function successBadgeAction(): Action
    {
        return Action::make('successBadge')
            ->iconButton()
            ->icon('heroicon-m-funnel')
            ->badge(5)
            ->badgeColor('success');
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
            ->requiresConfirmation()
            ->modalHeading('Delete post')
            ->modalDescription('Are you sure you\'d like to delete this post? This cannot be undone.')
            ->modalSubmitActionLabel('Yes, delete it')
            ->action(fn () => null);
    }

    public function modalIconAction(): Action
    {
        return Action::make('modalIcon')
            ->label('Delete')
            ->color('danger')
            ->requiresConfirmation()
            ->action(fn () => null)
            ->modalIcon('heroicon-o-trash');
    }

    public function modalFormAction(): Action
    {
        return Action::make('modalForm')
            ->label('Update author')
            ->form([
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
                    ->description('Give the category unique name')
                    ->schema([
                        TextInput::make('name')
                            ->required(),
                        TextInput::make('slug')
                            ->disabled()
                            ->required(),
                    ])
                    ->columns(2),
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
            ->form([
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
