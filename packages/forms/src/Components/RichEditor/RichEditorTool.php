<?php

namespace Filament\Forms\Components\RichEditor;

use Closure;
use Filament\Forms\Components\RichEditor;
use Filament\Schemas\Components\Concerns\HasLabel;
use Filament\Schemas\Components\Concerns\HasName;
use Filament\Support\Components\Contracts\HasEmbeddedView;
use Filament\Support\Components\ViewComponent;
use Filament\Support\Concerns\HasExtraAttributes;
use Filament\Support\Concerns\HasIcon;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Js;

use function Filament\Support\generate_icon_html;

class RichEditorTool extends ViewComponent implements HasEmbeddedView
{
    use HasExtraAttributes;
    use HasIcon;
    use HasLabel {
        getLabel as getBaseLabel;
    }
    use HasName;

    /**
     * @var array<string, mixed> | Closure
     */
    protected array | Closure $activeOptions = [];

    protected string | Closure | null $iconAlias = null;

    protected string | Closure | null $activeKey = null;

    protected string | Closure | null $jsHandler = null;

    protected string | Closure | null $activeJsExpression = null;

    protected bool | Closure $isDisabledWhenNotActive = false;

    protected bool | Closure $hasActiveStyling = true;

    protected RichEditor $editor;

    protected string $evaluationIdentifier = 'tool';

    protected string $viewIdentifier = 'tool';

    final public function __construct(string $name)
    {
        $this
            ->name($name)
            ->hiddenLabel();
    }

    public static function make(string $name): static
    {
        $static = app(static::class, ['name' => $name]);

        $static->configure();

        return $static;
    }

    public function editor(RichEditor $editor): static
    {
        $this->editor = $editor;

        return $this;
    }

    public function getEditor(): RichEditor
    {
        return $this->editor;
    }

    /**
     * @param  array<string, mixed> | Closure  $options
     */
    public function activeOptions(array | Closure $options): static
    {
        $this->activeOptions = $options;

        return $this;
    }

    /**
     * @return array<string, mixed>
     */
    public function getActiveOptions(): array
    {
        return $this->evaluate($this->activeOptions);
    }

    public function iconAlias(string | Closure | null $alias): static
    {
        $this->iconAlias = $alias;

        return $this;
    }

    public function getIconAlias(): ?string
    {
        return $this->evaluate($this->iconAlias);
    }

    public function action(string | Closure | null $action = null, string | Closure | null $arguments = null): static
    {
        $this->jsHandler(fn (RichEditorTool $tool): string => '$wire.mountAction(\'' . ($tool->evaluate($action) ?? $tool->getName()) . '\', { editorSelection, ...' . ($tool->evaluate($arguments) ?? '{}') . ' }, ' . Js::from(['schemaComponent' => $tool->getEditor()->getKey()]) . ')');

        return $this;
    }

    public function jsHandler(string | Closure | null $handler): static
    {
        $this->jsHandler = $handler;

        return $this;
    }

    public function activeJsExpression(string | Closure | null $expression): static
    {
        $this->activeJsExpression = $expression;

        return $this;
    }

    public function getJsHandler(): ?string
    {
        return $this->evaluate($this->jsHandler);
    }

    public function getActiveJsExpression(): ?string
    {
        return $this->evaluate($this->activeJsExpression);
    }

    public function activeKey(string | Closure | null $key): static
    {
        $this->activeKey = $key;

        return $this;
    }

    public function getActiveKey(): string
    {
        return $this->evaluate($this->activeKey) ?? $this->getName();
    }

    public function getLabel(): string | Htmlable | null
    {
        if (filled($label = $this->getBaseLabel())) {
            return $label;
        }

        $label = (string) str($this->getName())
            ->afterLast('.')
            ->kebab()
            ->replace(['-', '_'], ' ')
            ->ucfirst();

        return $this->shouldTranslateLabel ? __($label) : $label;
    }

    public function disabledWhenNotActive(bool | Closure $condition = true): static
    {
        $this->isDisabledWhenNotActive = $condition;

        return $this;
    }

    public function isDisabledWhenNotActive(): bool
    {
        return (bool) $this->evaluate($this->isDisabledWhenNotActive);
    }

    public function activeStyling(bool | Closure $condition = true): static
    {
        $this->hasActiveStyling = $condition;

        return $this;
    }

    public function hasActiveStyling(): bool
    {
        return (bool) $this->evaluate($this->hasActiveStyling);
    }

    public function toEmbeddedHtml(): string
    {
        $activeJsExpression = $this->getActiveJsExpression();

        if (filled($activeJsExpression)) {
            $activeJsExpression = "editorUpdatedAt && ({$activeJsExpression})";
        } else {
            $activeJsExpression = 'editorUpdatedAt && $getEditor()?.isActive(' . Js::from($this->getActiveKey())->toHtml() . ', ' . Js::from($this->getActiveOptions())->toHtml() . ')';
        }

        $isLabelHidden = $this->isLabelHidden();

        $attributes = $this->getExtraAttributeBag()
            ->merge([
                'tabindex' => -1,
                'type' => 'button',
                'x-bind:class' => '{ \'fi-active\': ' . ($this->hasActiveStyling() ? $activeJsExpression : 'false') . ' }',
                'x-bind:disabled' => $this->isDisabledWhenNotActive() ? '!(' . $activeJsExpression . ')' : null,
                'x-on:click' => $this->getJsHandler(),
                'x-tooltip' => (filled($label = $this->getLabel()) && $isLabelHidden)
                    ? '{ content: ' . Js::from($label) . ', theme: $store.theme }'
                    : null,
            ], escape: false)
            ->class([
                'fi-fo-rich-editor-tool',
                'fi-fo-rich-editor-tool-with-label' => ! $isLabelHidden,
            ]);

        ob_start(); ?>

        <button <?= $attributes->toHtml() ?>>
            <?= generate_icon_html($this->getIcon(), alias: $this->getIconAlias())->toHtml() ?>
            <?= $isLabelHidden ? null : '<span class="fi-fo-rich-editor-tool-label">' . $this->getLabel() . '</span>' ?>
        </button>

        <?php return ob_get_clean();
    }
}
