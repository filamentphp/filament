<?php

namespace Filament\Forms\Components\RichEditor;

use Closure;
use Filament\Forms\Components\RichEditor;
use Illuminate\Support\Js;

use function Filament\Support\generate_icon_html;

class RichEditorDropdownTool extends RichEditorTool
{
    /**
     * @var array<RichEditorTool> | Closure
     */
    protected array | Closure $options = [];

    protected bool $isSelectMode = false;

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

    public function selectMode(bool $selectMode = true): static
    {
        $this->isSelectMode = $selectMode;

        return $this;
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
        return $this->isSelectMode
            ? $this->toSelectHtml()
            : $this->toIconDropdownHtml();
    }

    private function toIconDropdownHtml(): string
    {
        $options = $this->getOptions();
        $label = $this->getLabel();
        $defaultIconHtml = generate_icon_html($this->getIcon(), alias: $this->getIconAlias())->toHtml();

        $effectJs = $this->buildTriggerIconEffect($options, $defaultIconHtml);
        $optionsHtml = $this->buildIconOptionsHtml($options);
        $chevronSvg = $this->chevronSvg();

        $xData = e('{ open: false, triggerIconHtml: ' . Js::from($defaultIconHtml)->toHtml() . ' }');
        $xEffect = e($effectJs);
        $xTooltip = e('{ content: ' . Js::from($label)->toHtml() . ', theme: $store.theme }');

        ob_start(); ?>

        <div x-data="<?= $xData ?>"
             x-effect="<?= $xEffect ?>"
             x-on:click.outside="open = false"
             x-on:keydown.escape.window="open = false"
             class="fi-fo-rich-editor-dropdown-tool">

            <button type="button" tabindex="-1"
                    x-on:click="open = !open"
                    class="fi-fo-rich-editor-dropdown-tool-trigger"
                    aria-haspopup="listbox"
                    x-bind:aria-expanded="open"
                    x-tooltip="<?= $xTooltip ?>">
                <span x-html="triggerIconHtml"><?= $defaultIconHtml ?></span>
                <?= $chevronSvg ?>
            </button>

            <div x-show="open" x-cloak x-transition
                 class="fi-fo-rich-editor-dropdown-tool-menu"
                 role="listbox">
                <?= $optionsHtml ?>
            </div>
        </div>

        <?php return ob_get_clean();
    }

    private function toSelectHtml(): string
    {
        $options = $this->getOptions();
        $label = $this->getLabel();
        $defaultLabel = $options[0]?->getLabel() ?? $label;

        $effectJs = $this->buildTriggerLabelEffect($options, $defaultLabel);
        $optionsHtml = $this->buildSelectOptionsHtml($options);
        $chevronSvg = $this->chevronSvg();

        $xData = e('{ open: false, triggerLabel: ' . Js::from($defaultLabel)->toHtml() . ' }');
        $xEffect = e($effectJs);

        ob_start(); ?>

        <div x-data="<?= $xData ?>"
             x-effect="<?= $xEffect ?>"
             x-on:click.outside="open = false"
             x-on:keydown.escape.window="open = false"
             class="fi-fo-rich-editor-dropdown-tool fi-fo-rich-editor-dropdown-tool-select">

            <button type="button" tabindex="-1"
                    x-on:click="open = !open"
                    class="fi-fo-rich-editor-dropdown-tool-trigger"
                    aria-haspopup="listbox"
                    x-bind:aria-expanded="open">
                <span x-text="triggerLabel"><?= e($defaultLabel) ?></span>
                <?= $chevronSvg ?>
            </button>

            <div x-show="open" x-cloak x-transition
                 class="fi-fo-rich-editor-dropdown-tool-menu"
                 role="listbox">
                <?= $optionsHtml ?>
            </div>
        </div>

        <?php return ob_get_clean();
    }

    private function chevronSvg(): string
    {
        return '<svg class="fi-fo-rich-editor-dropdown-tool-chevron" viewBox="0 0 12 12" fill="none" aria-hidden="true"><path d="M3 4.5 6 7.5l3-3" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>';
    }

    /**
     * @param  array<RichEditorTool>  $options
     */
    private function buildTriggerIconEffect(array $options, string $defaultIconHtml): string
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
    private function buildTriggerLabelEffect(array $options, string $defaultLabel): string
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
    private function buildIconOptionsHtml(array $options): string
    {
        $html = '';

        foreach ($options as $option) {
            $activeExpr = $this->buildActiveExpression($option);
            $iconHtml = generate_icon_html($option->getIcon(), alias: $option->getIconAlias())->toHtml();
            $optionLabel = $option->getLabel();
            $jsHandler = $option->getJsHandler();

            $html .= '<button type="button" tabindex="-1" role="option"'
                . ' x-on:click="' . e($jsHandler . '; open = false') . '"'
                . ' x-bind:class="' . e("{ 'fi-active': " . $activeExpr . ' }') . '"'
                . ' x-tooltip="' . e('{ content: ' . Js::from($optionLabel)->toHtml() . ', theme: $store.theme }') . '"'
                . ' class="fi-fo-rich-editor-dropdown-tool-option"'
                . '>' . $iconHtml . '</button>';
        }

        return $html;
    }

    /**
     * @param  array<RichEditorTool>  $options
     */
    private function buildSelectOptionsHtml(array $options): string
    {
        $html = '';

        foreach ($options as $option) {
            $activeExpr = $this->buildActiveExpression($option);
            $jsHandler = $option->getJsHandler();

            $html .= '<button type="button" tabindex="-1" role="option"'
                . ' x-on:click="' . e($jsHandler . '; open = false') . '"'
                . ' x-bind:class="' . e("{ 'fi-active': " . $activeExpr . ' }') . '"'
                . ' class="fi-fo-rich-editor-dropdown-tool-option"'
                . '>' . e($option->getLabel()) . '</button>';
        }

        return $html;
    }

    private function buildActiveExpression(RichEditorTool $option): string
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
