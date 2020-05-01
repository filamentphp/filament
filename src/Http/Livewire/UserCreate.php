<?php

namespace Filament\Http\Livewire;

use Livewire\Component;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Filament\Traits\HasForm;
use Filament\Contracts\User as UserContract;

class UserCreate extends Component
{    
    use AuthorizesRequests, HasForm;

    public function mount()
    {        
        $this->authorize('create', app(UserContract::class));
    }

    public function success()
    {
        $input = collect($this->form_data);

        $user = app(UserContract::class)::create($input->all());        

        session()->flash('notification', [
            'type' => 'success',
            'message' => __('filament::notifications.created', ['item' => $user->name]),
        ]);

        return redirect()->route('filament.admin.users.index');
    }

    public function render()
    {        
        $groupedFields = $this->fields()->groupBy(function ($field, $key) {
            return $field->group;
        });
        
        return view('filament::livewire.tabbed-form', [
            'title' => __('filament::users.create'),
            'groupedFields' => $groupedFields,
        ]);
    }
}
