<?php

namespace Filament\Forms\Components\RichEditor;

use Closure;
use Filament\Schemas\Components\Concerns\HasName;
use Filament\Support\Components\Contracts\HasEmbeddedView;
use Filament\Support\Components\ViewComponent;
use Filament\Support\Concerns\HasExtraAttributes;
use Filament\Support\Concerns\HasIcon;
use Illuminate\Support\Js;

use function Filament\Support\generate_icon_html;

class ToolbarButtonGroup extends ViewComponent implements HasEmbeddedView
{
    use HasExtraAttributes;
    use HasIcon;
    use HasName;

    /**
     * @var array<string> | Closure
     */
    protected array | Closure $buttons = [];

    protected bool | Closure $hasTextualButtons = false;

    protected string $evaluationIdentifier = 'toolbarButtonGroup';

    protected string $viewIdentifier = 'toolbarButtonGroup';

    /**
     * @var array<RichEditorTool>
     */
    protected array $resolvedButtons = [];

    /**
     * @param  array<string> | Closure  $buttons
     */
    final public function __construct(string $label, array | Closure $buttons = [])
    {
        $this->name($label);
        $this->buttons = $buttons;
    }

    /**
     * @param  array<string> | Closure  $buttons
     */
    public static function make(string $label, array | Closure $buttons = []): static
    {
        $static = app(static::class, ['label' => $label, 'buttons' => $buttons]);
        $static->configure();

        return $static;
    }

    /**
     * @param  array<string> | Closure  $buttons
     */
    public function buttons(array | Closure $buttons): static
    {
        $this->buttons = $buttons;

        return $this;
    }

    /**
     * @return array<string>
     */
    public function getButtons(): array
    {
        return $this->evaluate($this->buttons);
    }

    public function textualButtons(bool | Closure $condition = true): static
    {
        $this->hasTextualButtons = $condition;

        return $this;
    }

    public function hasTextualButtons(): bool
    {
        return (bool) $this->evaluate($this->hasTextualButtons);
    }

    /**
     * @param  array<string, RichEditorTool>  $tools
     */
    public function resolve(array $tools): static
    {
        $this->resolvedButtons = array_values(array_filter(
            array_map(static fn (string $name): ?RichEditorTool => $tools[$name] ?? null, $this->getButtons()),
        ));

        return $this;
    }

    /**
     * @return array<RichEditorTool>
     */
    public function getResolvedButtons(): array
    {
        return $this->resolvedButtons;
    }

    public function toEmbeddedHtml(): string
    {
        $resolvedButtons = $this->getResolvedButtons();

        if (empty($resolvedButtons)) {
            return '';
        }

        $isTextual = $this->hasTextualButtons();
        $label = $this->getName();
        $firstButton = $resolvedButtons[0];
        $icon = $this->getIcon();

        $defaultContent = generate_icon_html($icon ?? $firstButton->getIcon(), alias: $icon ? null : $firstButton->getIconAlias())?->toHtml() ?? '';
        $defaultContentHtml = $defaultContent;

        $effectJs = $this->buildTriggerEffect($resolvedButtons, $defaultContent);
        $activeExpression = $this->buildGroupActiveExpression($resolvedButtons);
        $buttonsHtml = $this->buildButtonsHtml($resolvedButtons);

        $triggerAttributes = $this->getExtraAttributeBag()
            ->merge([
                'type' => 'button',
                'tabindex' => -1,
                'data-rich-editor-tool' => e($label),
                'aria-label' => e($label),
                'x-bind:class' => '{ \'fi-active\': ' . $activeExpression . ' }',
                'x-tooltip' => '{ content: ' . Js::from($label)->toHtml() . ', theme: $store.theme }',
            ], escape: false)
            ->class([
                'fi-fo-rich-editor-dropdown-tool-trigger',
            ]);

        $xData = e('{ ...filamentDropdown(), triggerContent: ' . Js::from($defaultContent)->toHtml() . ' }');
        $xEffect = e($effectJs);
        $wrapperClass = 'fi-dropdown fi-fo-rich-editor-dropdown-tool' . ($isTextual ? ' fi-fo-rich-editor-dropdown-tool-textual' : '');
        $chevronSvg = '<svg class="fi-fo-rich-editor-dropdown-tool-chevron" viewBox="0 0 12 12" fill="none" aria-hidden="true"><path d="M3 4.5 6 7.5l3-3" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>';

        ob_start(); ?>

        <div x-data="<?= $xData ?>"
             x-effect="<?= $xEffect ?>"
             class="<?= $wrapperClass ?>">

            <div class="fi-dropdown-trigger" x-on:mousedown="if ($event.button === 0) toggle($event)">
                <button <?= $triggerAttributes->toHtml() ?>>
                    <span x-html="triggerContent"><?= $defaultContentHtml ?></span>
                    <?= $chevronSvg ?>
                </button>
            </div>

            <div x-cloak x-ref="panel" x-float.placement.bottom-start.flip="{ offset: 4 }"
                 aria-orientation="<?= $isTextual ? 'vertical' : 'horizontal' ?>"
                 x-on:keydown.capture="handleToolbarMenuKeydown($event)"
                 class="fi-dropdown-panel fi-fo-rich-editor-dropdown-tool-panel">
                <div class="fi-fo-rich-editor-dropdown-tool-menu">
                    <?= $buttonsHtml ?>
                </div>
            </div>
        </div>

        <?php return ob_get_clean();
    }

    /**
     * @param  array<RichEditorTool>  $resolvedButtons
     */
    protected function buildTriggerEffect(array $resolvedButtons, string $defaultContent): string
    {
        $parts = [];

        foreach ($resolvedButtons as $button) {
            $value = Js::from(generate_icon_html($button->getIcon(), alias: $button->getIconAlias())?->toHtml() ?? '')->toHtml();

            $parts[] = 'if (' . $this->buildActiveExpression($button) . ') return ' . $value . ';';
        }

        return 'triggerContent = (() => { ' . implode(' ', $parts) . ' return ' . Js::from($defaultContent)->toHtml() . '; })()';
    }

    /**
     * @param  array<RichEditorTool>  $resolvedButtons
     */
    protected function buildButtonsHtml(array $resolvedButtons): string
    {
        $isTextual = $this->hasTextualButtons();
        $html = '';

        foreach ($resolvedButtons as $button) {
            $activeExpression = $this->buildActiveExpression($button);
            $buttonLabel = $button->getLabel();

            $buttonAttributes = $button->getExtraAttributeBag()
                ->merge([
                    'tabindex' => -1,
                    'type' => 'button',
                    'role' => $button->isToggle() ? 'menuitemcheckbox' : 'menuitem',
                    'aria-label' => e($buttonLabel),
                    'aria-checked' => $button->isToggle() ? 'false' : null,
                    'x-bind:aria-checked' => $button->isToggle() ? '(' . $activeExpression . ') ? \'true\' : \'false\'' : null,
                    'x-bind:aria-disabled' => $button->isDisabledWhenNotActive() ? '!(' . $activeExpression . ') ? \'true\' : \'false\'' : null,
                    'x-on:click' => '$event.stopPropagation(); if ($el.getAttribute(\'aria-disabled\') === \'true\') return; Alpine.$data($el.closest(\'.fi-dropdown\')).closeMenuHierarchy(); ' . $button->getJsHandler(),
                    'x-bind:class' => '{ \'fi-active\': ' . ($button->hasActiveStyling() ? $activeExpression : 'false') . ' }',
                    ...($isTextual ? [] : [
                        'x-tooltip' => '{ content: ' . Js::from($buttonLabel)->toHtml() . ', theme: $store.theme }',
                    ]),
                ], escape: false)
                ->class([
                    'fi-dropdown-list-item',
                    'fi-fo-rich-editor-dropdown-tool-option',
                ]);

            $iconHtml = generate_icon_html($button->getIcon(), alias: $button->getIconAlias())?->toHtml() ?? '';

            $content = $isTextual
                ? $iconHtml . ' <span>' . e($buttonLabel) . '</span>'
                : $iconHtml;

            $html .= '<button ' . $buttonAttributes->toHtml() . '>' . $content . '</button>';
        }

        return $html;
    }

    /**
     * @param  array<RichEditorTool>  $resolvedButtons
     */
    protected function buildGroupActiveExpression(array $resolvedButtons): string
    {
        $expressions = array_map(
            fn (RichEditorTool $button): string => '(' . $this->buildActiveExpression($button) . ')',
            $resolvedButtons,
        );

        return implode(' || ', $expressions);
    }

    protected function buildActiveExpression(RichEditorTool $button): string
    {
        $activeJsExpression = $button->getActiveJsExpression();

        if (filled($activeJsExpression)) {
            return "editorUpdatedAt && ({$activeJsExpression})";
        }

        return 'editorUpdatedAt && $getEditor()?.isActive('
            . Js::from($button->getActiveKey())->toHtml()
            . ', '
            . Js::from($button->getActiveOptions())->toHtml()
            . ')';
    }
}
