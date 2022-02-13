<?php

namespace Filament\Resources\Pages;

use Filament\Forms\ComponentContainer;
use Filament\Pages\Actions\ButtonAction;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

/**
 * @property ComponentContainer $form
 */
class ViewRecord extends Page
{
    use Concerns\HasRecordBreadcrumb;
    use Concerns\HasRelationManagers;
    use Concerns\InteractsWithRecord;
    use Concerns\UsesResourceForm;

    protected static string $view = 'filament::resources.pages.view-record';

    public $record;

    public $data;

    protected $queryString = [
        'activeRelationManager',
    ];

    public function getBreadcrumb(): string
    {
        return static::$breadcrumb ?? __('filament::resources/pages/view-record.breadcrumb');
    }

    public function mount($record): void
    {
        static::authorizeResourceAccess();

        $this->record = $this->getRecord($record);

        abort_unless(static::getResource()::canView($this->record), 403);

        $this->fillForm();
    }

    protected function fillForm(): void
    {
        $this->callHook('beforeFill');

        $this->form->fill($this->record->toArray());

        $this->callHook('afterFill');
    }

    protected function getActions(): array
    {
        $resource = static::getResource();

        if (! $resource::hasPage('edit')) {
            return [];
        }

        if (! $resource::canEdit($this->record)) {
            return [];
        }

        return [$this->getEditAction()];
    }

    protected function getEditAction(): ButtonAction
    {
        return ButtonAction::make('edit')
            ->label(__('filament::resources/pages/view-record.actions.edit.label'))
            ->url(fn () => static::getResource()::getUrl('edit', ['record' => $this->record]));
    }

    protected function getTitle(): string
    {
        if (filled(static::$title)) {
            return static::$title;
        }

        if (filled($recordTitle = $this->getRecordTitle())) {
            return $recordTitle;
        }

        return Str::title(static::getResource()::getLabel());
    }

    protected function getForms(): array
    {
        return array_merge(parent::getForms(), [
            'form' => $this->makeForm()
                ->disabled()
                ->model($this->record)
                ->schema($this->getResourceForm()->getSchema())
                ->statePath('data'),
        ]);
    }

    protected function getMountedActionFormModel(): Model
    {
        return $this->record;
    }
}
