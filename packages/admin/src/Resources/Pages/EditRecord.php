<?php

namespace Filament\Resources\Pages;

use Filament\Forms;
use Filament\Resources\Form;
use Filament\Resources\Table;
use Filament\View\Components\Actions\ButtonAction;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class EditRecord extends Page implements Forms\Contracts\HasForms
{
    use Forms\Concerns\InteractsWithForms;

    protected static string $view = 'filament::resources.pages.edit-record';

    public $record;

    public $data;

    protected ?Form $resourceForm = null;

    public static function getBreadcrumb(): string
    {
        return static::$breadcrumb ?? 'Edit';
    }

    public function mount($record): void
    {
        $this->record = $this->resolveRecord($record);

        $this->form->fill($this->record->toArray());
    }

    protected function getActions(): array
    {
        return [
            ButtonAction::make('delete')
                ->label('Delete')
                ->color('danger'),
        ];
    }

    protected function getFormActions(): array
    {
        return [
            ButtonAction::make('save')
                ->label('Save'),
        ];
    }

    protected function getFormActionsComponent(): Forms\Components\Actions
    {
        return Forms\Components\Actions::make($this->getFormActions());
    }

    protected function getForms(): array
    {
        return [
            'form' => $this->makeForm()
                ->schema($this->getFormSchema())
                ->statePath('data')
                ->model($this->record),
        ];
    }

    protected function getFormSchema(): array
    {
        $resourceForm = $this->getResourceForm();
        $schema = $resourceForm->getSchema();

        if (is_array($schema)) {
            $component = Forms\Components\Grid::make()
                ->schema(array_merge($schema, [$this->getFormActionsComponent()]))
                ->columns($resourceForm->getColumns());
        }

        return [$component];
    }

    protected function getResourceForm(): Form
    {
        if (! $this->resourceForm) {
            $this->resourceForm = static::getResource()::form(Form::make());
        }

        return $this->resourceForm;
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
