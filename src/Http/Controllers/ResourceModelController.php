<?php

namespace Filament\Http\Controllers;

class ResourceModelController extends Controller
{
    /**
     * Show the resource for the given model.
     *
     * @param  int  $id
     * @return \Illuminate\View\View
     */
    public function __invoke(string $model, string $action = 'index')
    {
        return 'Model: '.$model.' action: '.$action;
    }
}