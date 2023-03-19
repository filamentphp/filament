<?php

namespace App\Http\Livewire;

use Filament\Actions\Action;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Livewire\Component;

class Actions extends Component implements HasActions
{
    use InteractsWithActions;

    public function buttonAction(): Action
    {
        return Action::make('buttonAction')
            ->label('Edit')
            ->button();
    }

    public function linkAction(): Action
    {
        return Action::make('linkAction')
            ->label('Edit')
            ->link();
    }

    public function iconButtonAction(): Action
    {
        return Action::make('iconButtonAction')
            ->icon('heroicon-o-pencil-square')
            ->iconButton();
    }

    public function dangerAction(): Action
    {
        return Action::make('dangerAction')
            ->label('Delete')
            ->color('danger');
    }

    public function largeAction(): Action
    {
        return Action::make('largeAction')
            ->label('Create')
            ->size('lg');
    }

    public function iconAction(): Action
    {
        return Action::make('iconAction')
            ->label('Edit')
            ->icon('heroicon-m-pencil-square');
    }

    public function iconAfterAction(): Action
    {
        return Action::make('iconAfterAction')
            ->label('Edit')
            ->icon('heroicon-m-pencil-square')
            ->iconPosition('after');
    }

    public function render()
    {
        return view('livewire.actions');
    }
}
