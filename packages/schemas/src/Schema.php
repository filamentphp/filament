<?php

namespace Filament\Schemas;

use Closure;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Schemas\Components\Component;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Support\Components\Contracts\HasEmbeddedView;
use Filament\Support\Components\ViewComponent;
use Filament\Support\Concerns\HasAlignment;
use Filament\Support\Concerns\HasDefaultDataFormattingSettings;
use Filament\Support\Concerns\HasExtraAttributes;
use Filament\Support\Enums\Alignment;
use Filament\Support\Enums\Width;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Js;
use Illuminate\View\ComponentAttributeBag;
use Livewire\Component as LivewireComponent;

class Schema extends ViewComponent implements HasEmbeddedView
{
    use Concerns\BelongsToLivewire;
    use Concerns\BelongsToModel;
    use Concerns\BelongsToParentComponent;
    use Concerns\CanBeDisabled;
    use Concerns\CanBeEmbeddedInParentComponent;
    use Concerns\CanBeHidden;
    use Concerns\CanBeInline;
    use Concerns\CanBeValidated;
    use Concerns\CanModifyActions;
    use Concerns\Cloneable;
    use Concerns\HasColumns;
    use Concerns\HasComponents;
    use Concerns\HasEntryWrapper;
    use Concerns\HasFieldWrapper;
    use Concerns\HasGap;
    use Concerns\HasHeadings;
    use Concerns\HasInlineLabels;
    use Concerns\HasKey;
    use Concerns\HasOperation;
    use Concerns\HasState;
    use Concerns\HasStateBindingModifiers;
    use HasAlignment;
    use HasDefaultDataFormattingSettings;
    use HasExtraAttributes;

    protected string $evaluationIdentifier = 'schema';

    protected string $viewIdentifier = 'schema';

    /**
     * @param  (LivewireComponent & HasSchemas) | null  $livewire
     */
    final public function __construct(?HasSchemas $livewire = null)
    {
        $this->livewire($livewire);
    }

    /**
     * @param  (LivewireComponent & HasSchemas) | null  $livewire
     */
    public static function make(?HasSchemas $livewire = null): static
    {
        $static = app(static::class, ['livewire' => $livewire]);
        $static->configure();

        return $static;
    }

    /**
     * @return array<mixed>
     */
    protected function resolveDefaultClosureDependencyForEvaluationByName(string $parameterName): array
    {
        return match ($parameterName) {
            'container' => [$this],
            'livewire' => [$this->getLivewire()],
            'model' => [$this->getModel()],
            'record' => [$this->getRecord()],
            default => parent::resolveDefaultClosureDependencyForEvaluationByName($parameterName),
        };
    }

    /**
     * @return array<mixed>
     */
    protected function resolveDefaultClosureDependencyForEvaluationByType(string $parameterType): array
    {
        $record = $this->getRecord();

        if (! ($record instanceof Model)) {
            return match ($parameterType) {
                static::class, self::class => [$this],
                default => parent::resolveDefaultClosureDependencyForEvaluationByType($parameterType),
            };
        }

        return match ($parameterType) {
            static::class, self::class => [$this],
            Model::class, $record::class => [$record],
            default => parent::resolveDefaultClosureDependencyForEvaluationByType($parameterType),
        };
    }

    /**
     * @param  array<Component | Action | ActionGroup | string | Htmlable> | Schema | Component | Action | ActionGroup | string | Htmlable | Closure  $components
     */
    public static function start(array | Schema | Component | Action | ActionGroup | string | Htmlable | Closure $components): static
    {
        return static::make()
            ->components($components)
            ->alignStart();
    }

    /**
     * @param  array<Component | Action | ActionGroup | string | Htmlable> | Schema | Component | Action | ActionGroup | string | Htmlable | Closure  $components
     */
    public static function end(array | Schema | Component | Action | ActionGroup | string | Htmlable | Closure $components): static
    {
        return static::make()
            ->components($components)
            ->alignEnd();
    }

    /**
     * @param  array<Component | Action | ActionGroup | string | Htmlable> | Schema | Component | Action | ActionGroup | string | Htmlable | Closure  $components
     */
    public static function center(array | Schema | Component | Action | ActionGroup | string | Htmlable | Closure $components): static
    {
        return static::make()
            ->components($components)
            ->alignCenter();
    }

    /**
     * @param  array<Component | Action | ActionGroup | string | Htmlable> | Schema | Component | Action | ActionGroup | string | Htmlable | Closure  $components
     */
    public static function between(array | Schema | Component | Action | ActionGroup | string | Htmlable | Closure $components): static
    {
        return static::make()
            ->components($components)
            ->alignBetween();
    }

    public function toEmbeddedHtml(): string
    {
        if ($this->isDirectlyHidden()) {
            return '';
        }

        $hasVisibleComponents = false;

        $componentsWithVisibility = array_map(
            function (Component | Action | ActionGroup $component) use (&$hasVisibleComponents): array {
                $isComponentVisible = $component->isVisible();

                if ($isComponentVisible) {
                    $hasVisibleComponents = true;
                }

                return [$component, $isComponentVisible];
            },
            $this->getComponents(withHidden: true),
        );

        if (! $hasVisibleComponents) {
            return '';
        }

        $alignment = $this->getAlignment();
        $isInline = $this->isInline();
        $isRoot = $this->isRoot();

        $isEmbeddedInParentComponent = $this->isEmbeddedInParentComponent();
        $parentComponent = $isEmbeddedInParentComponent
            ? $this->getParentComponent()
            : null;
        $statePath = $isEmbeddedInParentComponent
            ? $parentComponent->getContainer()->getStatePath()
            : $this->getStatePath();

        $attributes = $this->getExtraAttributeBag()
            ->when(
                ! $isInline,
                fn (ComponentAttributeBag $attributes) => $attributes->grid($this->getColumns()),
            )
            ->merge([
                'wire:partial' => $this->shouldPartiallyRender() ? ('schema.' . $this->getKey()) : null,
                'x-data' => $isRoot ? 'filamentSchema({ livewireId: ' . Js::from($this->getLivewire()->getId()) . ' })' : null,
                'x-on:form-validation-error.window' => $isRoot ? 'handleFormValidationError' : null,
            ], escape: false)
            ->class([
                'fi-sc',
                'fi-inline' => $isInline,
                ($alignment instanceof Alignment) ? "fi-align-{$alignment->value}" : $alignment,
                'fi-sc-has-gap' => $this->hasGap(),
                'fi-sc-dense' => $this->isDense(),
            ]);

        ob_start(); ?>

        <div <?= $attributes->toHtml() ?>>
            <?php foreach ($componentsWithVisibility as [$schemaComponent, $isSchemaComponentVisible]) { ?>
                <?php if (($schemaComponent instanceof Action) || ($schemaComponent instanceof ActionGroup)) { ?>
                    <div <?php if (! $isSchemaComponentVisible) { ?> class="fi-hidden"<?php } ?>>
                        <?php if ($isSchemaComponentVisible) { ?>
                            <?= $schemaComponent->toHtml() ?>
                        <?php } ?>
                    </div>
                <?php } elseif (! $schemaComponent->isLiberatedFromContainerGrid()) { ?>
                    <?php
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
                        $hiddenJs = $schemaComponent->getHiddenJs();
                    $visibleJs = $schemaComponent->getVisibleJs();

                    $maxWidth = $schemaComponent->getMaxWidth();

                    $schemaComponentStatePath = $isEmbeddedInParentComponent
                        ? $parentComponent->getStatePath()
                        : $schemaComponent->getStatePath();

                    $attributes = (new ComponentAttributeBag)
                        ->when(
                            ! $isInline,
                            fn (ComponentAttributeBag $attributes) => $attributes->gridColumn($schemaComponent->getColumnSpan(), $schemaComponent->getColumnStart(), $schemaComponent->getColumnOrder(), ! $isSchemaComponentVisible),
                        )
                        ->merge([
                            'wire:key' => $schemaComponent->getLivewireKey(),
                            ...(($pollingInterval = $schemaComponent->getPollingInterval()) ? ["wire:poll.{$pollingInterval}" => "partiallyRenderSchemaComponent('{$schemaComponent->getKey()}')"] : []),
                        ], escape: false)
                        ->class([
                            ($maxWidth instanceof Width) ? "fi-width-{$maxWidth->value}" : $maxWidth,
                        ]);
                    ?>
                    <div
                        <?php if ($isSchemaComponentVisible) { ?>
                            x-data="filamentSchemaComponent({
                                path: <?= Js::from($schemaComponentStatePath) ?>,
                                containerPath: <?= Js::from($statePath) ?>,
                                isLive: <?= Js::from($schemaComponent->isLive()) ?>,
                                $wire,
                            })"
                            <?php if ($afterStateUpdatedJs = $schemaComponent->getAfterStateUpdatedJs()) { ?>
                                x-init="<?= implode(';', array_map(
                                    fn (string $js): string => '$wire.watch(' . Js::from($schemaComponentStatePath) . ', ($state, $old) => ($state !== undefined) && eval(' . Js::from($js) . '))',
                                    $afterStateUpdatedJs,
                                )) ?>"
                            <?php } ?>
                            <?php if (filled($visibilityJs = match ([filled($hiddenJs), filled($visibleJs)]) {
                                [true, true] => "(! ({$hiddenJs})) && ({$visibleJs})",
                                [true, false] => "! ({$hiddenJs})",
                                [false, true] => $visibleJs,
                                default => null,
                            })) { ?>
                                x-bind:class="{ 'fi-hidden': ! (<?= $visibilityJs ?>) }"
                                x-cloak
                            <?php } ?>
                        <?php } ?>
                        <?= $attributes->toHtml() ?>
                    >
                        <?php if ($isSchemaComponentVisible) { ?>
                            <div
                                class="<?= Arr::toCssClasses([
                                    'fi-sc-component',
                                    'fi-grid-ctn' => $schemaComponent->isGridContainer(),
                                ]) ?>"
                            >
                                <?= $schemaComponent->toHtml() ?>
                            </div>
                        <?php } ?>
                    </div>
                <?php } elseif ($isSchemaComponentVisible) { ?>
                    <?= $schemaComponent->toHtml() ?>
                <?php } ?>
            <?php } ?>
        </div>

        <?php return ob_get_clean();
    }
}
