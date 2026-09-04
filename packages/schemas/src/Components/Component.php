<?php

namespace Filament\Schemas\Components;

use Filament\Schemas\Components\Concerns\BelongsToContainer;
use Filament\Schemas\Components\Concerns\BelongsToModel;
use Filament\Schemas\Components\Concerns\CanBeDisabled;
use Filament\Schemas\Components\Concerns\CanBeGridContainer;
use Filament\Schemas\Components\Concerns\CanBeHidden;
use Filament\Schemas\Components\Concerns\CanBeLiberatedFromContainerGrid;
use Filament\Schemas\Components\Concerns\CanBeRepeated;
use Filament\Schemas\Components\Concerns\CanPartiallyRender;
use Filament\Schemas\Components\Concerns\CanPoll;
use Filament\Schemas\Components\Concerns\Cloneable;
use Filament\Schemas\Components\Concerns\HasActions;
use Filament\Schemas\Components\Concerns\HasChildComponents;
use Filament\Schemas\Components\Concerns\HasEntryWrapper;
use Filament\Schemas\Components\Concerns\HasFieldWrapper;
use Filament\Schemas\Components\Concerns\HasHeadings;
use Filament\Schemas\Components\Concerns\HasId;
use Filament\Schemas\Components\Concerns\HasInlineLabel;
use Filament\Schemas\Components\Concerns\HasKey;
use Filament\Schemas\Components\Concerns\HasMaxWidth;
use Filament\Schemas\Components\Concerns\HasMeta;
use Filament\Schemas\Components\Concerns\HasState;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Concerns\HasColumns;
use Filament\Schemas\Concerns\HasGap;
use Filament\Schemas\Concerns\HasStateBindingModifiers;
use Filament\Support\Components\ViewComponent;
use Filament\Support\Concerns\CanGrow;
use Filament\Support\Concerns\CanOrderColumns;
use Filament\Support\Concerns\CanSpanColumns;
use Filament\Support\Concerns\HasExtraAttributes;
use Filament\Support\Enums\Width;
use Filament\Support\View\ComponentAttributeBag as FilamentComponentAttributeBag;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Js;
use Illuminate\View\ComponentAttributeBag;
use Illuminate\View\ComponentSlot;

class Component extends ViewComponent
{
    use BelongsToContainer;
    use BelongsToModel;
    use CanBeDisabled;
    use CanBeGridContainer;
    use CanBeHidden;
    use CanBeLiberatedFromContainerGrid;
    use CanBeRepeated;
    use CanGrow;
    use CanOrderColumns;
    use CanPartiallyRender;
    use CanPoll;
    use CanSpanColumns;
    use Cloneable;
    use HasActions;
    use HasChildComponents;
    use HasColumns;
    use HasEntryWrapper;
    use HasExtraAttributes;
    use HasFieldWrapper;
    use HasGap;
    use HasHeadings;
    use HasId;
    use HasInlineLabel;
    use HasKey;
    use HasMaxWidth;
    use HasMeta;
    use HasState;
    use HasStateBindingModifiers;

    protected string $evaluationIdentifier = 'component';

    protected string $viewIdentifier = 'schemaComponent';

    /**
     * @return array<mixed>
     */
    protected function resolveDefaultClosureDependencyForEvaluationByName(string $parameterName): array
    {
        return match ($parameterName) {
            'context', 'operation' => [$this->getContainer()->getOperation()],
            'get' => [$this->makeGetUtility()],
            'livewire' => [$this->getLivewire()],
            'model' => [$this->getModel()],
            'parentRepeaterItemIndex' => [$this->getParentRepeaterItemIndex()],
            'rawState' => [$this->getRawState()],
            'record' => [$this->getRecord()],
            'set' => [$this->makeSetUtility()],
            'state' => [$this->getState()],
            default => parent::resolveDefaultClosureDependencyForEvaluationByName($parameterName),
        };
    }

    /**
     * @return array<mixed>
     */
    protected function resolveDefaultClosureDependencyForEvaluationByType(string $parameterType): array
    {
        $record = is_a($parameterType, Model::class, allow_string: true) ? $this->getRecord() : null;

        if ((! $record) || is_array($record)) {
            return match ($parameterType) {
                Get::class => [$this->makeGetUtility()],
                Set::class => [$this->makeSetUtility()],
                default => parent::resolveDefaultClosureDependencyForEvaluationByType($parameterType),
            };
        }

        return match ($parameterType) {
            Model::class, $record::class => [$record],
            default => parent::resolveDefaultClosureDependencyForEvaluationByType($parameterType),
        };
    }

    /**
     * @return array<string, mixed>
     */
    public function getExtraViewData(): array
    {
        return [
            'get' => $this->makeGetUtility(),
            'operation' => $this->getContainer()->getOperation(),
            'record' => $this->getRecord(),
        ];
    }

    /**
     * @internal Do not use this method outside the internals of Filament. It is subject to breaking changes in minor and patch releases.
     */
    public function toSchemaHtml(?bool $isVisible = null): string
    {
        $isVisible ??= $this->isVisible();

        $container = $this->getContainer();

        $isContainerEmbeddedInParentComponent = $container->isEmbeddedInParentComponent();
        $containerParentComponent = $isContainerEmbeddedInParentComponent
            ? $container->getParentComponent()
            : null;
        $containerStatePath = $isContainerEmbeddedInParentComponent
            ? $containerParentComponent->getContainer()->getStatePath()
            : $container->getStatePath();

        /**
         * Instead of only rendering the hidden components, we should
         * render the `<div>` wrappers for all fields, regardless of
         * if they are hidden or not. This is to solve Livewire DOM
         * diffing issues.
         *
         * Additionally, any `<div>` elements that wrap hidden
         * components need to have `class="fi-hidden"`, so that they
         * don't consume grid space.
         */
        $hiddenJs = $this->getHiddenJs();
        $visibleJs = $this->getVisibleJs();

        $visibilityJs = match ([filled($hiddenJs), filled($visibleJs)]) {
            [true, true] => "(! ({$hiddenJs})) && ({$visibleJs})",
            [true, false] => "! ({$hiddenJs})",
            [false, true] => $visibleJs,
            default => null,
        };

        // A component with a client-side visibility condition is wrapped in a `<fieldset>`
        // that is `disabled` while hidden, which bars its controls from native browser
        // validation. Otherwise, the browser would silently block form submission over an
        // invalid control that it cannot focus or anchor a validation bubble to. Unlike
        // concealing containers such as collapsed sections, these components cannot be
        // revealed on demand when they contain an invalid control. The `disabled` attribute
        // is rendered so that the controls are also barred before Alpine.js applies
        // `x-bind:disabled`.
        $wrapperTag = ($isVisible && filled($visibilityJs)) ? 'fieldset' : 'div';

        $maxWidth = $this->getMaxWidth();

        $statePath = $isContainerEmbeddedInParentComponent
            ? $containerParentComponent->getStatePath()
            : $this->getStatePath();

        $key = $this->getKey();

        $attributes = (new FilamentComponentAttributeBag)
            ->when(
                ! $container->isInline(),
                fn (ComponentAttributeBag $attributes) => $attributes->gridColumn($this->getColumnSpan(), $this->getColumnStart(), $this->getColumnOrder(), ! $isVisible),
            )
            ->merge([
                'wire:key' => $this->getLivewireKey(),
                ...(($pollingInterval = $this->getPollingInterval()) ? ["wire:poll.{$pollingInterval}" => "partiallyRenderSchemaComponent('{$this->getKey()}')"] : []),
            ], escape: false)
            ->class([
                ($maxWidth instanceof Width) ? "fi-width-{$maxWidth->value}" : $maxWidth,
                'fi-growable' => $container->isInline() && $this->canGrow(default: false),
                'fi-sc-visibility-fieldset' => $wrapperTag === 'fieldset',
            ]);

        ob_start(); ?>

        <<?= $wrapperTag ?>
            <?php if ($wrapperTag === 'fieldset') { ?>
                disabled
                role="none"
            <?php } ?>
            <?php if (filled($key)) { ?>
                wire:partial="schema-component::<?= $key ?>"
            <?php } ?>
            <?php if ($isVisible) { ?>
                x-data="filamentSchemaComponent({
                    path: <?= Js::from($statePath) ?>,
                    containerPath: <?= Js::from($containerStatePath) ?>,
                    $wire,
                })"
                <?php if ($afterStateUpdatedJs = $this->getAfterStateUpdatedJs()) { ?>
                    x-init="<?= implode(';', array_map(
                        fn (string $js): string => '$wire; $wire.watch(' . Js::from($statePath) . ', ($state, $old) => isStateChanged($state, $old) && eval(' . Js::from($js) . '))',
                        $afterStateUpdatedJs,
                    )) ?>"
                <?php } ?>
                <?php if (filled($visibilityJs)) { ?>
                    x-bind:class="{ 'fi-hidden': ! (<?= $visibilityJs ?>) }"
                    x-bind:disabled="! (<?= $visibilityJs ?>)"
                    x-cloak
                <?php } ?>
            <?php } ?>
            <?= $attributes->toHtml() ?>
        >
            <?php if ($isVisible) { ?>
                <div
                    class="<?= Arr::toCssClasses([
                        'fi-sc-component',
                        'fi-grid-ctn' => $this->isGridContainer(),
                    ]) ?>"
                >
                    <?= $this->toHtml() ?>
                </div>
            <?php } ?>
        </<?= $wrapperTag ?>>

        <?php return ob_get_clean();
    }

    /**
     * @internal This method is not part of the public API and should not be used. Its parameters may change at any time without notice.
     */
    protected function renderWrapperBladeComponent(string $component, ComponentSlot $slot, ComponentAttributeBag $attributes): string
    {
        // Wrappers were originally rendered using the `<x-dynamic-component>` Blade component,
        // so `fieldWrapperView()` and `entryWrapperView()` accepted any Blade component name,
        // which is still supported for backwards compatibility.
        return Blade::render(
            '<x-dynamic-component :component="$component" {{ $attributes }}>{{ $slot }}</x-dynamic-component>',
            [
                'component' => $component,
                'attributes' => $attributes,
                'slot' => $slot,
            ],
        );
    }

    /**
     * @deprecated Fields no longer strip their native validation attributes when concealed. Concealing containers reveal themselves around an invalid control, using the `invalid` event listener in the `filament/schemas` package, and components with a client-side visibility condition are wrapped in a `<fieldset>` that is `disabled` while hidden.
     */
    public function isConcealed(): bool
    {
        return false;
    }
}
