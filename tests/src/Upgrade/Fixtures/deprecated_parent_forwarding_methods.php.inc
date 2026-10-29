<?php

namespace Filament\Tests\Upgrade\Fixtures\DeprecatedParentForwardingMethods;

use Closure;
use Filament\Actions\Action;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Illuminate\Contracts\Support\Arrayable;

class StatusIcon extends IconColumn
{
    public function options(array | Arrayable | Closure $customOptions): static
    {
        return parent::options($customOptions);
    }

    public function configureDefaults(): static
    {
        return parent::options(options: ['heroicon-o-check' => 'published']);
    }
}

class InheritedStatusIcon extends StatusIcon {}

class SaveAction extends Action
{
    public function form(array | Closure | null $customSchema = null): static
    {
        return parent::form(form: $customSchema);
    }

    public function disabledForm(bool | Closure $customCondition = true): static
    {
        return parent::disabledForm(condition: $customCondition);
    }

    public function getForm(Schema $customSchema): ?Schema
    {
        return parent::getForm(schema: $customSchema);
    }

    public function formData(array $customData, bool $shouldMutate = true): static
    {
        return parent::formData(shouldMutate: $shouldMutate, data: $customData);
    }

    public function resetFormData(): static
    {
        return parent::resetFormData();
    }

    public function getFormData(): array
    {
        return parent::getFormData();
    }

    public function configureDefaults(): static
    {
        parent::disabledForm();
        parent::disableForm(condition: false);
        parent::mutateFormDataUsing(callback: null);
        parent::fillForm(data: null);
        parent::infolist(infolist: []);
        parent::getRawFormData();
        parent::isFormDisabled();

        return $this;
    }
}

class InheritedSaveAction extends SaveAction {}

function configure(InheritedStatusIcon $icon, InheritedSaveAction $action, Schema $schema): void
{
    $icon->options(customOptions: fn (): array => ['heroicon-o-clock' => 'draft']);
    $icon->label('Status')->options(customOptions: []);
    $action->form(customSchema: null)->disabledForm(customCondition: fn (): bool => false);
    $action->formData(shouldMutate: false, customData: ['title' => 'Draft']);
    $action->getForm(customSchema: $schema);
    $action->getFormData();
    $action->resetFormData();
}
-----
<?php

namespace Filament\Tests\Upgrade\Fixtures\DeprecatedParentForwardingMethods;

use Closure;
use Filament\Actions\Action;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Illuminate\Contracts\Support\Arrayable;

class StatusIcon extends IconColumn
{
    public function options(array | Arrayable | Closure $customOptions): static
    {
        return parent::icons($customOptions);
    }

    public function configureDefaults(): static
    {
        return parent::icons(icons: ['heroicon-o-check' => 'published']);
    }
}

class InheritedStatusIcon extends StatusIcon {}

class SaveAction extends Action
{
    public function form(array | Closure | null $customSchema = null): static
    {
        return parent::schema(schema: $customSchema);
    }

    public function disabledForm(bool | Closure $customCondition = true): static
    {
        return parent::disabledSchema(condition: $customCondition);
    }

    public function getForm(Schema $customSchema): ?Schema
    {
        return parent::getSchema(schema: $customSchema);
    }

    public function formData(array $customData, bool $shouldMutate = true): static
    {
        return parent::data(shouldMutate: $shouldMutate, data: $customData);
    }

    public function resetFormData(): static
    {
        return parent::resetData();
    }

    public function getFormData(): array
    {
        return parent::getData();
    }

    public function configureDefaults(): static
    {
        parent::disabledSchema();
        parent::disabledSchema(condition: false);
        parent::mutateDataUsing(callback: null);
        parent::fillSchema(data: null);
        parent::schema(schema: []);
        parent::getRawData();
        parent::isSchemaDisabled();

        return $this;
    }
}

class InheritedSaveAction extends SaveAction {}

function configure(InheritedStatusIcon $icon, InheritedSaveAction $action, Schema $schema): void
{
    $icon->options(customOptions: fn (): array => ['heroicon-o-clock' => 'draft']);
    $icon->label('Status')->options(customOptions: []);
    $action->form(customSchema: null)->disabledForm(customCondition: fn (): bool => false);
    $action->formData(shouldMutate: false, customData: ['title' => 'Draft']);
    $action->getForm(customSchema: $schema);
    $action->getFormData();
    $action->resetFormData();
}
