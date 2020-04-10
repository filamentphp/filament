<?php

namespace Filament\Http\Livewire;

use Livewire\Component;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Filament\Traits\WithDataTable;
use Filament\Models\Permission;
use Illuminate\Support\HtmlString;

class Permissions extends Component
{
    use AuthorizesRequests, WithDataTable;

    public function render()
    {
        $this->authorize('view', Permission::class);

        $results = Permission::search($this->search)
            ->orderBy($this->sortField, $this->sortAsc ? 'asc' : 'desc')
            ->paginate($this->perPage);

        return view('filament::livewire.permissions', [
            'title' => __('filament::admin.permissions'),
            'results' => $results,
            'items' => $results->map(function($item) {
                $item->action = new HtmlString('<a href="'.route('filament.admin.permissions.edit', ['id' => $item->id]).'">'.__('Edit').'</a>');
                return $item;
            }),
        ]);
    }
}