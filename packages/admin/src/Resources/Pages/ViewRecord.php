<?php

namespace Filament\Resources\Pages;

use Filament\Forms;
use Filament\Resources\Form;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class ViewRecord extends Page implements Forms\Contracts\HasForms
{
    use Forms\Concerns\InteractsWithForms;

    protected static string $view = 'filament::resources.pages.view-record';

    public $record;

    public $data;

    public static function getBreadcrumb(): string
    {
        return static::$breadcrumb ?? 'View';
    }

    public function mount($record): void
    {
        $this->record = $this->resolveRecord($record);

        $this->form->fill($this->record->toArray());
    }

    protected function getForms(): array
    {
        return [
            'form' => $this->makeForm()
                ->schema(static::getResource()::form(Form::make())->getSchema())
                ->statePath('data')
                ->model($this->record)
                ->disabled(),
        ];
    }

    protected function resolveRecord($key): Model
    {
        $model = static::getResource()::getModel();

        $record = (new $model())->resolveRouteBinding($key);

        if ($record === null) {
            throw (new ModelNotFoundException())->setModel($model, [$key]);
        }

        return $record;
    }
}
