<?php

namespace Filament\Forms\Components\RichEditor;

use Closure;
use Filament\Forms\Components\RichEditor;
use Filament\Support\Components\Contracts\HasEmbeddedView;
use Illuminate\Support\Js;

use function Filament\Support\generate_icon_html;

class RichEditorDropdownTool extends RichEditorTool implements HasEmbeddedView
{
    /**
     * @var array<RichEditorTool> | Closure
     */
    protected array | Closure $options = [];

    protected bool | Closure $isSelectMode = false;

    /**
     * @param  array<RichEditorTool> | Closure  $options
     */
    public function options(array | Closure $options): static
    {
        $this->options = $options;

        return $this;
    }

    /**
     * @return array<RichEditorTool>
     */
    public function getOptions(): array
    {
        return $this->evaluate($this->options);
    }

    public function selectMode(bool | Closure $condition = true): static
    {
        $this->isSelectMode = $condition;

        return $this;
    }

    public function isSelectMode(): bool
    {
        return (bool) $this->evaluate($this->isSelectMode);
    }

    public function editor(RichEditor $editor): static
    {
        parent::editor($editor);

        foreach ($this->getOptions() as $option) {
            $option->editor($editor);
        }

        return $this;
    }

    public function toEmbeddedHtml(): string
    {
        $options = $this->getOptions();

        if (empty($options)) {
            return '';
        }

        return $this->isSelectMode()
            ? $this->toSelectHtml($options)
            : $this->toIconDropdownHtml($options);
    }

    /**
     * @param  array<RichEditorTool>  $options
     */
    protected function toIconDropdownHtml(array $options): string
    {
        $label = $this->getLabel();
        $defaultIconHtml = generate_icon_html($this->getIcon(), alias: $this->getIconAlias())->toHtml();

        $effectJs = $this->buildTriggerIconEffect($options, $defaultIconHtml);
        $optionsHtml = $this->buildIconOptionsHtml($options);
        $chevronSvg = $this->getChevronSvg();

        $triggerAttributes = $this->getExtraAttributeBag()
            ->merge([
                'type' => 'button',
                'tabindex' => -1,
                'aria-label' => $label,
                'aria-haspopup' => 'menu',
                'x-on:click' => 'open = !open',
                'x-bind:aria-expanded' => 'open',
                'x-tooltip' => '{ content: ' . Js::from($label)->toHtml() . ', theme: $store.theme }',
            ], escape: false)
            ->class([
                'fi-fo-rich-editor-dropdown-tool-trigger',
            ]);

        $xData = e('{ open: false, triggerIconHtml: ' . Js::from($defaultIconHtml)->toHtml() . ' }');
        $xEffect = e($effectJs);

        ob_start(); ?>

        <div x-data="<?= $xData ?>"
             x-effect="<?= $xEffect ?>"
             x-on:click.outside="open = false"
             x-on:keydown.escape.prevent="open = false"
             class="fi-fo-rich-editor-dropdown-tool">

            <button <?= $triggerAttributes->toHtml() ?>>
                <span x-html="triggerIconHtml"><?= $defaultIconHtml ?></span>
                <?= $chevronSvg ?>
            </button>

            <div x-show="open" x-cloak x-transition
                 class="fi-fo-rich-editor-dropdown-tool-menu"
                 role="menu">
                <?= $optionsHtml ?>
            </div>
        </div>

        <?php return ob_get_clean();
    }

    /**
     * @param  array<RichEditorTool>  $options
     */
    protected function toSelectHtml(array $options): string
    {
        $label = $this->getLabel();
        $defaultLabel = isset($options[0]) ? $options[0]->getLabel() : $label;

        $effectJs = $this->buildTriggerLabelEffect($options, $defaultLabel);
        $optionsHtml = $this->buildSelectOptionsHtml($options);
        $chevronSvg = $this->getChevronSvg();

        $triggerAttributes = $this->getExtraAttributeBag()
            ->merge([
                'type' => 'button',
                'tabindex' => -1,
                'aria-label' => $label,
                'aria-haspopup' => 'menu',
                'x-on:click' => 'open = !open',
                'x-bind:aria-expanded' => 'open',
            ], escape: false)
            ->class([
                'fi-fo-rich-editor-dropdown-tool-trigger',
            ]);

        $xData = e('{ open: false, triggerLabel: ' . Js::from($defaultLabel)->toHtml() . ' }');
        $xEffect = e($effectJs);

        ob_start(); ?>

        <div x-data="<?= $xData ?>"
             x-effect="<?= $xEffect ?>"
             x-on:click.outside="open = false"
             x-on:keydown.escape.prevent="open = false"
             class="fi-fo-rich-editor-dropdown-tool fi-fo-rich-editor-dropdown-tool-select">

            <button <?= $triggerAttributes->toHtml() ?>>
                <span x-text="triggerLabel"><?= e($defaultLabel) ?></span>
                <?= $chevronSvg ?>
            </button>

            <div x-show="open" x-cloak x-transition
                 class="fi-fo-rich-editor-dropdown-tool-menu"
                 role="menu">
                <?= $optionsHtml ?>
            </div>
        </div>

        <?php return ob_get_clean();
    }

    protected function getChevronSvg(): string
    {
        return '<svg class="fi-fo-rich-editor-dropdown-tool-chevron" viewBox="0 0 12 12" fill="none" aria-hidden="true"><path d="M3 4.5 6 7.5l3-3" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>';
    }

    /**
     * @param  array<RichEditorTool>  $options
     */
    protected function buildTriggerIconEffect(array $options, string $defaultIconHtml): string
    {
        $parts = [];

        foreach ($options as $option) {
            $parts[] = 'if (' . $this->buildActiveExpression($option) . ') return ' . Js::from(
                generate_icon_html($option->getIcon(), alias: $option->getIconAlias())->toHtml()
            )->toHtml() . ';';
        }

        return 'triggerIconHtml = (() => { ' . implode(' ', $parts) . ' return ' . Js::from($defaultIconHtml)->toHtml() . '; })()';
    }

    /**
     * @param  array<RichEditorTool>  $options
     */
    protected function buildTriggerLabelEffect(array $options, string $defaultLabel): string
    {
        $parts = [];

        foreach ($options as $option) {
            $parts[] = 'if (' . $this->buildActiveExpression($option) . ') return ' . Js::from($option->getLabel())->toHtml() . ';';
        }

        return 'triggerLabel = (() => { ' . implode(' ', $parts) . ' return ' . Js::from($defaultLabel)->toHtml() . '; })()';
    }

    /**
     * @param  array<RichEditorTool>  $options
     */
    protected function buildIconOptionsHtml(array $options): string
    {
        $html = '';

        foreach ($options as $option) {
            $activeExpr = $this->buildActiveExpression($option);
            $iconHtml = generate_icon_html($option->getIcon(), alias: $option->getIconAlias())->toHtml();
            $optionLabel = $option->getLabel();
            $jsHandler = $option->getJsHandler();

            $optionAttributes = $option->getExtraAttributeBag()
                ->merge([
                    'tabindex' => -1,
                    'type' => 'button',
                    'role' => 'menuitem',
                    'aria-label' => $optionLabel,
                    'x-on:click' => $jsHandler . '; open = false',
                    'x-bind:class' => '{ \'fi-active\': ' . $activeExpr . ' }',
                    'x-tooltip' => '{ content: ' . Js::from($optionLabel)->toHtml() . ', theme: $store.theme }',
                ], escape: false)
                ->class([
                    'fi-fo-rich-editor-dropdown-tool-option',
                ]);

            $html .= '<button ' . $optionAttributes->toHtml() . '>' . $iconHtml . '</button>';
        }

        return $html;
    }

    /**
     * @param  array<RichEditorTool>  $options
     */
    protected function buildSelectOptionsHtml(array $options): string
    {
        $html = '';

        foreach ($options as $option) {
            $activeExpr = $this->buildActiveExpression($option);
            $jsHandler = $option->getJsHandler();
            $optionLabel = $option->getLabel();

            $optionAttributes = $option->getExtraAttributeBag()
                ->merge([
                    'tabindex' => -1,
                    'type' => 'button',
                    'role' => 'menuitem',
                    'aria-label' => $optionLabel,
                    'x-on:click' => $jsHandler . '; open = false',
                    'x-bind:class' => '{ \'fi-active\': ' . $activeExpr . ' }',
                ], escape: false)
                ->class([
                    'fi-fo-rich-editor-dropdown-tool-option',
                ]);

            $html .= '<button ' . $optionAttributes->toHtml() . '>' . e($optionLabel) . '</button>';
        }

        return $html;
    }

    protected function buildActiveExpression(RichEditorTool $option): string
    {
        $activeJsExpression = $option->getActiveJsExpression();

        if (filled($activeJsExpression)) {
            return "editorUpdatedAt && ({$activeJsExpression})";
        }

        return 'editorUpdatedAt && $getEditor()?.isActive('
            . Js::from($option->getActiveKey())->toHtml()
            . ', '
            . Js::from($option->getActiveOptions())->toHtml()
            . ')';
    }
}
