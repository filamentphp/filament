<?php

namespace Filament\Tests\Upgrade\Fixtures\DeprecatedSchemaManualBoundaries;

use Closure;
use Filament\Actions\Action;
use Filament\Schemas\Components\Component;
use Filament\Schemas\Components\Concerns\HasLabel;
use Filament\Schemas\Components\EmbeddedSchema;
use Filament\Schemas\Components\RenderHook;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Wizard\Step;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Filament\Support\Components\ViewComponent;

class AliasOverridingSchema extends Schema
{
    public function name(string | Closure | null $customName): static
    {
        return parent::name($customName);
    }
}

class InheritedAliasOverridingSchema extends AliasOverridingSchema {}

class UnmodifiedSchema extends Schema {}

trait CustomOperation
{
    public function operation(?string $customOperation): static
    {
        return $this;
    }
}

class ReplacementOverridingSchema extends Schema
{
    use CustomOperation;
}

class InheritedReplacementOverridingSchema extends ReplacementOverridingSchema {}

class GetterOverridingSchema extends Schema
{
    public function getKey(bool $isAbsolute = true): ?string
    {
        return 'custom';
    }
}

class CustomSection extends Section
{
    public function hiddenLabel(bool | Closure $customCondition = true): static
    {
        return $this;
    }
}

trait CustomLabel
{
    public function disableLabel(bool | Closure $customCondition = true): static
    {
        return $this;
    }
}

class TraitOverridingSection extends Section
{
    use CustomLabel;
}

class AdaptedLabelComponent extends ViewComponent
{
    use HasLabel {
        hiddenLabel as protected;
    }
}

function adapted(AdaptedLabelComponent $component): void
{
    $component->disableLabel();
}

class CustomComponent extends Component
{
    public function getChildSchema($customKey = null): ?Schema
    {
        return null;
    }

    public function makeGetUtility(): Get
    {
        return parent::makeGetUtility();
    }

    public function clearCachedChildSchemas(): void {}
}

class CustomStep extends Step
{
    public function afterValidated(?Closure $customCallback): static
    {
        return parent::afterValidated($customCallback);
    }
}

function custom(InheritedAliasOverridingSchema $alias, InheritedReplacementOverridingSchema $replacement, GetterOverridingSchema $getter, CustomSection $section, TraitOverridingSection $trait, CustomComponent $component, CustomStep $step, UnmodifiedSchema | AliasOverridingSchema $union): void
{
    $alias->name(customName: 'details');
    $alias->statePath('data')->name(customName: 'details');
    $alias?->statePath('data')?->name(customName: null);
    $replacement->context(context: 'edit');
    $replacement->statePath('data')->context(context: 'edit');
    $getter->getName();
    $section->disableLabel(condition: false);
    $section->schema([])->disableLabel(condition: false);
    $trait->disableLabel(customCondition: false);
    $component->getChildComponentContainer(key: 'footer');
    $component->getGetCallback();
    $component->clearCachedDefaultChildSchemas();
    $step->afterValidated(customCallback: null);
    $union->name('details');
}

function dynamic(Schema $schema, Component $component, array $arguments): void
{
    $schema->reactive(false);
    $schema->reactive(...$arguments);
    $callback = $schema->reactive(...);
    $callback = [$component, 'reactive'];
    $schema->name(...$arguments);
    $schema->context(...$arguments);
    $schema->getName(false);
    $schema->getName(...$arguments);
    $callback = $schema->getName(...);
    $method = 'name';
    $schema->{$method}('details');
    $schema->{'context'}('edit');
    $callback = [$component, 'getChildComponentContainer'];
    Schema::context('edit');
}

class Unrelated extends ViewComponent
{
    public function hiddenLabel(bool $condition = true): static
    {
        return $this;
    }

    public function disableLabel(bool $condition = true): static
    {
        return $this;
    }
}

function unrelated(Action $action, RenderHook $hook, EmbeddedSchema $embedded, Unrelated $unrelated, Schema | Unrelated $union, $unknown): void
{
    $unrelated->reactive();
    $unknown->reactive();
    $union->reactive();
    $action->name('save')->getName();
    $hook->name('panels::head.end')->getName();
    $embedded->name('sidebar')->getName();
    $unrelated->name('details');
    $unrelated->getName();
    $unrelated->context('edit');
    $unrelated->disableLabel();
    $unrelated->afterValidated(null);
    $unrelated->getGetCallback();
    $unrelated->getSetCallback();
    $unrelated->getChildComponentContainer();
    $unrelated->getChildComponentContainers();
    $unrelated->clearCachedDefaultChildSchemas();
    $union->name('details');
    $unknown->context('edit');
}

class ReactiveOverride extends Schema
{
    public function reactive(): static
    {
        return parent::reactive();
    }
}

class LiveOverride extends Component
{
    public function live(bool $onBlur = false, int | string | null $debounce = null, bool | Closure | null $condition = true): static
    {
        return $this;
    }
}

class InheritedLiveOverride extends LiveOverride {}

function customBinding(ReactiveOverride $schema, InheritedLiveOverride $component, Schema | ReactiveOverride $union): void
{
    $schema->reactive();
    $component->reactive();
    $union->reactive();
}
