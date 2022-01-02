<?php

namespace Filament\Resources\Pages;

use Filament\Forms;
use Filament\Forms\ComponentContainer;
use Filament\Pages\Actions\ButtonAction;
use Illuminate\Support\Str;

/**
 * @property ComponentContainer $form
 */
class ViewRecord extends Page implements Forms\Contracts\HasForms
{
    use Concerns\HasRecordBreadcrumb;
    use Concerns\HasRelationManagers;
    use Concerns\InteractsWithRecord;
    use Concerns\UsesResourceForm;
    use Forms\Concerns\InteractsWithForms;

    protected static string $view = 'filament::resources.pages.view-record';

    public $record;

    public $data;

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

        $this->activeRelationManager ??= $this->getRelationManagers()[0] ?? null;
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

        if (! $resource::canEdit($this->record)) {
            return [];
        }

        return [$this->getEditButtonAction()];
    }

    protected function getEditButtonAction(): ButtonAction
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
        return [
            'form' => $this->makeForm()
                ->disabled()
                ->model($this->record)
                ->schema($this->getResourceForm()->getSchema())
                ->statePath('data'),
        ];
    }
}
