<?php

namespace Filament\Tests\Upgrade\Fixtures\DeprecatedActionMethods;

use Closure;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\Concerns\HasLabel;
use Filament\Actions\EditAction;
use Filament\Actions\ReplicateAction;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Illuminate\Contracts\Support\Htmlable;

function configureForwarders(Action $action, ?Action $nullableAction, ReplicateAction $replicateAction, ?ActionGroup $group): void
{
    $action->modalActions();
    $action->modalActions(actions: null);
    $action->extraModalActions(actions: fn () => []);
    $action->modalButton(label: null);
    $action->modalFooter(footer: null);
    $action->modalSubheading(subheading: fn () => 'Details');
    $action->makeExtraModalAction(arguments: ['another' => true], name: 'another');
    $action->failureNotificationMessage(message: fn () => 'Failed');
    $action->successNotificationMessage(message: null);
    $action->withAttributes(attributes: ['data-test' => 'value']);
    $action->hold();
    $action->infolist(infolist: fn () => []);
    $action->disableLabel(condition: false);
    $group?->disableLabel(condition: fn (): bool => false);
    $replicateAction->afterReplicaSaved(callback: null);
    $replicateAction->label('Copy')->afterReplicaSaved(fn () => null);
    $nullableAction?->MODALSUBHEADING(subheading: null);
    $callback = $action->successNotificationMessage(...);
    $callback = $action->makeExtraModalAction(...);
    $action->modalActions(...['actions' => []]);
    Action::make('save')->modalButton()->disableLabel();
    ReplicateAction::make()->afterReplicaSaved(null);

    // These calls and callables need manual migration.
    $action->modalSubheading(...['subheading' => null]);
    $action->failureNotificationMessage(...['message' => null]);
    $action->successNotificationMessage(...['message' => null]);
    $action->infolist(...['infolist' => []]);
    $action->hold(true);
    $action->hold(...[]);
    $action->withAttributes([], true);
    $action->withAttributes(...['attributes' => []]);
    $callback = $action->hold(...);
    $callback = $action->withAttributes(...);
    $action->getJavaScriptClickHandler();
    $action->centerModal(false);
    $action->afterReplicaSaved(null);
}

class CustomForwardingAction extends Action
{
    public function modalDescription(string | Htmlable | Closure | null $text = null): static
    {
        return $this;
    }

    public function configureModal(): void
    {
        $this->modalButton();
        $this->modalSubheading(subheading: 'Custom');
        parent::modalSubheading(subheading: 'Custom');
        $this->getJavaScriptClickHandler();
        parent::getJavaScriptClickHandler();
    }
}

class CustomAliasAction extends Action
{
    public function successNotificationMessage(string | Closure | null $message): static
    {
        return $this;
    }
}

class CustomReplicateAction extends ReplicateAction
{
    public function after(?Closure $callback): static
    {
        return $this;
    }
}

class InheritedAliasAction extends CustomAliasAction {}

class AdaptedLabelAction extends Action
{
    use HasLabel {
        hiddenLabel as protected;
    }
}

function configureInheritedAndAdapted(InheritedAliasAction $action, AdaptedLabelAction $adapted): void
{
    $action->successNotificationMessage(message: null);
    $adapted->disableLabel();
}

function configureForwardingOverrides(CustomForwardingAction $action, ?CustomAliasAction $aliasAction, CustomReplicateAction $replicateAction, CustomForwardingAction | CustomAliasAction $possibleOverride, $unknown): void
{
    $action->label('Custom')->modalSubheading(subheading: 'Custom');
    $aliasAction?->successNotificationMessage(message: null);
    $replicateAction->label('Copy')->afterReplicaSaved(null);
    $possibleOverride->successNotificationMessage(message: null);
    $unknown->modalButton();
    $unknown->hold();
}

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
use Filament\Actions\ActionGroup;
use Filament\Actions\Concerns\HasLabel;
use Filament\Actions\EditAction;
use Filament\Actions\ReplicateAction;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Illuminate\Contracts\Support\Htmlable;

function configureForwarders(Action $action, ?Action $nullableAction, ReplicateAction $replicateAction, ?ActionGroup $group): void
{
    $action->modalFooterActions();
    $action->modalFooterActions(actions: null);
    $action->extraModalFooterActions(actions: fn () => []);
    $action->modalSubmitActionLabel(label: null);
    $action->modalContentFooter(footer: null);
    $action->modalDescription(description: fn () => 'Details');
    $action->makeModalSubmitAction(arguments: ['another' => true], name: 'another');
    $action->failureNotificationTitle(title: fn () => 'Failed');
    $action->successNotificationTitle(title: null);
    $action->extraAttributes(attributes: ['data-test' => 'value']);
    $action->halt();
    $action->schema(schema: fn () => []);
    $action->hiddenLabel(condition: false);
    $group?->hiddenLabel(condition: fn (): bool => false);
    $replicateAction->after(callback: null);
    $replicateAction->label('Copy')->after(fn () => null);
    $nullableAction?->modalDescription(description: null);
    $callback = $action->successNotificationTitle(...);
    $callback = $action->makeModalSubmitAction(...);
    $action->modalFooterActions(...['actions' => []]);
    Action::make('save')->modalSubmitActionLabel()->hiddenLabel();
    ReplicateAction::make()->after(null);

    // These calls and callables need manual migration.
    $action->modalSubheading(...['subheading' => null]);
    $action->failureNotificationMessage(...['message' => null]);
    $action->successNotificationMessage(...['message' => null]);
    $action->infolist(...['infolist' => []]);
    $action->hold(true);
    $action->hold(...[]);
    $action->withAttributes([], true);
    $action->withAttributes(...['attributes' => []]);
    $callback = $action->hold(...);
    $callback = $action->withAttributes(...);
    $action->getJavaScriptClickHandler();
    $action->centerModal(false);
    $action->afterReplicaSaved(null);
}

class CustomForwardingAction extends Action
{
    public function modalDescription(string | Htmlable | Closure | null $text = null): static
    {
        return $this;
    }

    public function configureModal(): void
    {
        $this->modalSubmitActionLabel();
        $this->modalSubheading(subheading: 'Custom');
        parent::modalSubheading(subheading: 'Custom');
        $this->getJavaScriptClickHandler();
        parent::getJavaScriptClickHandler();
    }
}

class CustomAliasAction extends Action
{
    public function successNotificationMessage(string | Closure | null $message): static
    {
        return $this;
    }
}

class CustomReplicateAction extends ReplicateAction
{
    public function after(?Closure $callback): static
    {
        return $this;
    }
}

class InheritedAliasAction extends CustomAliasAction {}

class AdaptedLabelAction extends Action
{
    use HasLabel {
        hiddenLabel as protected;
    }
}

function configureInheritedAndAdapted(InheritedAliasAction $action, AdaptedLabelAction $adapted): void
{
    $action->successNotificationMessage(message: null);
    $adapted->disableLabel();
}

function configureForwardingOverrides(CustomForwardingAction $action, ?CustomAliasAction $aliasAction, CustomReplicateAction $replicateAction, CustomForwardingAction | CustomAliasAction $possibleOverride, $unknown): void
{
    $action->label('Custom')->modalSubheading(subheading: 'Custom');
    $aliasAction?->successNotificationMessage(message: null);
    $replicateAction->label('Copy')->afterReplicaSaved(null);
    $possibleOverride->successNotificationMessage(message: null);
    $unknown->modalButton();
    $unknown->hold();
}

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
