<?php

namespace Filament\Tests\Upgrade\Fixtures\DeprecatedActionMethods;

use Closure;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;

function configure(Action $action, ?Action $nullableAction, Schema $schema): void
{
    $action->emit(data: ['id' => 7], event: 'saved');
    $action->emitSelf(event: 'saved', data: ['id' => 8]);
    $action->emitTo(data: ['id' => 9], component: 'posts', event: 'saved');
    $action->disableForm(condition: false);
    $action->disabledForm();
    $action->form(form: []);
    $action->getForm(schema: $schema);
    $action->isFormDisabled();
    $action->mutateFormDataUsing(callback: null);
    $action->formData(shouldMutate: false, data: ['title' => 'Draft']);
    $action->resetFormData();
    $action->getFormData();
    $action->getRawFormData();
    $nullableAction?->form(form: null);
    $nullableAction?->getFormData();
    $callback = $action->form(...);
    $action->EMIT('saved');
    Action::make('save')->form([])->disabledForm();
    EditAction::make()->form(form: [])->formData(data: [], shouldMutate: false);

    $action->form(...['form' => []]);
    $arguments = ['form' => []];
    $action->form(...$arguments);

    $method = 'form';
    $action->{$method}(form: []);
    $action->{'form'}([]);
    $callback = [$action, 'emit'];
    $action->schema([]);
}

class CustomAction extends Action
{
    public function configureSchema(): static
    {
        $this->form(form: []);

        return parent::disabledForm(condition: false);
    }
}

function configureCustom(CustomAction $action): void
{
    $action->form(form: []);
}

class OverridingAction extends Action
{
    public function form(array | Closure | null $form): static
    {
        return $this;
    }
}

function configureOverride(OverridingAction $action): void
{
    $action->form(form: []);
}

function configureNullableOverride(?OverridingAction $action): void
{
    $action?->form(form: []);
}

class ReplacementOverridingAction extends Action
{
    public function disabledSchema(bool | Closure $isDisabled = false): static
    {
        return parent::disabledSchema($isDisabled);
    }

    public function configureSchema(): static
    {
        $this->disabledForm();
        $this->disabledForm(condition: false);

        return parent::disabledForm(condition: false);
    }
}

function configurePossibleOverride(CustomAction | OverridingAction $action): void
{
    $action->form(form: []);
}

function unrelated(Resource $resource, UnrelatedComponent $component, $unknown): void
{
    $resource->form(Schema::make());
    $component->form(form: []);
    $component->emit('saved');
    $component->getFormData();
    $unknown->form(form: []);
    $unknown->emit('saved');
}

class UnrelatedComponent
{
    public function form(array $form): void {}

    public function emit(string $event): void {}

    public function getFormData(): array
    {
        return [];
    }
}
-----
<?php

namespace Filament\Tests\Upgrade\Fixtures\DeprecatedActionMethods;

use Closure;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;

function configure(Action $action, ?Action $nullableAction, Schema $schema): void
{
    $action->dispatch(data: ['id' => 7], event: 'saved');
    $action->dispatchSelf(event: 'saved', data: ['id' => 8]);
    $action->dispatchTo(data: ['id' => 9], component: 'posts', event: 'saved');
    $action->disabledSchema(condition: false);
    $action->disabledSchema();
    $action->schema(schema: []);
    $action->getSchema(schema: $schema);
    $action->isSchemaDisabled();
    $action->mutateDataUsing(callback: null);
    $action->data(shouldMutate: false, data: ['title' => 'Draft']);
    $action->resetData();
    $action->getData();
    $action->getRawData();
    $nullableAction?->schema(schema: null);
    $nullableAction?->getData();
    $callback = $action->schema(...);
    $action->dispatch('saved');
    Action::make('save')->schema([])->disabledSchema();
    EditAction::make()->schema(schema: [])->data(data: [], shouldMutate: false);

    $action->form(...['form' => []]);
    $arguments = ['form' => []];
    $action->form(...$arguments);

    $method = 'form';
    $action->{$method}(form: []);
    $action->{'form'}([]);
    $callback = [$action, 'emit'];
    $action->schema([]);
}

class CustomAction extends Action
{
    public function configureSchema(): static
    {
        $this->schema(schema: []);

        return parent::disabledForm(condition: false);
    }
}

function configureCustom(CustomAction $action): void
{
    $action->schema(schema: []);
}

class OverridingAction extends Action
{
    public function form(array | Closure | null $form): static
    {
        return $this;
    }
}

function configureOverride(OverridingAction $action): void
{
    $action->form(form: []);
}

function configureNullableOverride(?OverridingAction $action): void
{
    $action?->form(form: []);
}

class ReplacementOverridingAction extends Action
{
    public function disabledSchema(bool | Closure $isDisabled = false): static
    {
        return parent::disabledSchema($isDisabled);
    }

    public function configureSchema(): static
    {
        $this->disabledForm();
        $this->disabledForm(condition: false);

        return parent::disabledForm(condition: false);
    }
}

function configurePossibleOverride(CustomAction | OverridingAction $action): void
{
    $action->form(form: []);
}

function unrelated(Resource $resource, UnrelatedComponent $component, $unknown): void
{
    $resource->form(Schema::make());
    $component->form(form: []);
    $component->emit('saved');
    $component->getFormData();
    $unknown->form(form: []);
    $unknown->emit('saved');
}

class UnrelatedComponent
{
    public function form(array $form): void {}

    public function emit(string $event): void {}

    public function getFormData(): array
    {
        return [];
    }
}
