<?php

namespace Filament\Resources\Pages;

use Filament\Forms\ComponentContainer;
use Filament\Pages\Actions\Action;
use Filament\Pages\Actions\EditAction;
use Illuminate\Database\Eloquent\Model;

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

        $this->record = $this->resolveRecord($record);

        abort_unless(static::getResource()::canView($this->getRecord()), 403);

        $this->fillForm();
    }

    protected function fillForm(): void
    {
        $this->callHook('beforeFill');

        $this->form->fill($this->getRecord()->toArray());

        $this->callHook('afterFill');
    }

    protected function getActions(): array
    {
        $resource = static::getResource();

        if (! $resource::hasPage('edit')) {
            return [];
        }

        if (! $resource::canEdit($this->getRecord())) {
            return [];
        }

        return [$this->getEditAction()];
    }

    protected function configureAction(Action $action): void
    {
        if ($action instanceof EditAction) {
            $this->configureEditAction($action);

            return;
        }
    }

    protected function configureEditAction(EditAction $action): void
    {
        $resource = static::getResource();

        $action
            ->authorize($resource::canEdit($this->getRecord()))
            ->record($this->getRecord())
            ->recordTitle($this->getRecordTitle());

        if ($resource::hasPage('edit')) {
            $action->url(fn (): string => static::getResource()::getUrl('edit', ['record' => $this->getRecord()]));

            return;
        }

        $action->form($this->getFormSchema());
    }

    /**
     * @deprecated Actions are no longer pre-defined.
     */
    protected function getEditAction(): Action
    {
        return EditAction::make();
    }

    protected function getTitle(): string
    {
        if (filled(static::$title)) {
            return static::$title;
        }

        return __('filament::resources/pages/view-record.title', [
            'label' => $this->getRecordTitle(),
        ]);
    }

    protected function getForms(): array
    {
        return [
            'form' => $this->makeForm()
                ->disabled()
                ->model($this->getRecord())
                ->schema($this->getFormSchema())
                ->statePath('data')
                ->inlineLabel(config('filament.layout.forms.have_inline_labels')),
        ];
    }

    protected function getFormSchema(): array
    {
        return $this->getResourceForm(columns: config('filament.layout.forms.have_inline_labels') ? 1 : 2)->getSchema();
    }

    protected function getMountedActionFormModel(): Model
    {
        return $this->getRecord();
    }
}
