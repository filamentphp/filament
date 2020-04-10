<?php

namespace Filament\Http\Livewire;

use Livewire\Component;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Filament\Traits\WithDataTable;
use Filament\Models\Role;
use Illuminate\Support\HtmlString;

class Roles extends Component
{
    use AuthorizesRequests, WithDataTable;

    public function render()
    {
        $this->authorize('view', Role::class);

        $results = Role::orderBy($this->sortField, $this->sortAsc ? 'asc' : 'desc')
            ->paginate(25);

        return view('filament::livewire.roles', [
            'title' => __('filament::admin.roles'),
            'results' => $results,
            'items' => $results->map(function($item) {
                $item->action = new HtmlString('<a href="'.route('filament.admin.roles.edit', ['id' => $item->id]).'">'.__('Edit').'</a>');
                return $item;
            }),
        ]);
    }
}