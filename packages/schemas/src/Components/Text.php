<?php

namespace Filament\Schemas\Components;

use BackedEnum;
use Closure;
use Filament\Schemas\JsContent;
use Filament\Schemas\View\Components\TextComponent;
use Filament\Support\Components\Contracts\HasEmbeddedView;
use Filament\Support\Concerns\CanBeCopied;
use Filament\Support\Concerns\HasColor;
use Filament\Support\Concerns\HasFontFamily;
use Filament\Support\Concerns\HasIcon;
use Filament\Support\Concerns\HasIconPosition;
use Filament\Support\Concerns\HasIconSize;
use Filament\Support\Concerns\HasTooltip;
use Filament\Support\Concerns\HasWeight;
use Filament\Support\Enums\FontFamily;
use Filament\Support\Enums\FontWeight;
use Filament\Support\Enums\IconPosition;
use Filament\Support\Enums\IconSize;
use Filament\Support\Enums\Size;
use Filament\Support\Enums\TextSize;
use Filament\Support\View\ComponentAttributeBag as FilamentComponentAttributeBag;
use Filament\Support\View\Components\BadgeComponent;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Js;

use function Filament\Support\generate_icon_html;

class Text extends Component implements HasEmbeddedView
{
    use CanBeCopied;
    use HasColor;
    use HasFontFamily;
    use HasIcon;
    use HasIconPosition;
    use HasIconSize;
    use HasTooltip;
    use HasWeight;

    protected ?string $publishedViewOverrideCheckPath = 'filament-schemas::components.text';

    protected string | Htmlable | Closure | null $content;

    protected bool | Closure $isBadge = false;

    protected TextSize | Size | string | Closure | null $size = null;

    final public function __construct(string | Htmlable | Closure | null $content)
    {
        $this->content($content);
    }

    public static function make(string | Htmlable | Closure | null $content): static
    {
        $static = app(static::class, ['content' => $content]);
        $static->configure();

        return $static;
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->defaultColor('gray');
    }

    public function content(string | Htmlable | Closure | null $content): static
    {
        $this->content = $content;

        return $this;
    }

    public function badge(bool | Closure $condition = true): static
    {
        $this->isBadge = $condition;

        return $this;
    }

    public function isBadge(): bool
    {
        return (bool) $this->evaluate($this->isBadge);
    }

    public function js(): static
    {
        $this->content(JsContent::make($this->getContent()));

        return $this;
    }

    public function getContent(): string | Htmlable | null
    {
        return $this->evaluate($this->content);
    }

    public function size(TextSize | Size | string | Closure | null $size): static
    {
        $this->size = $size;

        return $this;
    }

    public function getSize(): TextSize | Size | string | null
    {
        $size = $this->evaluate($this->size);

        if (blank($size)) {
            return null;
        }

        if (is_string($size)) {
            $size = TextSize::tryFrom($size) ?? $size;
        }

        return $size;
    }

    public function toEmbeddedHtml(): string
    {
        $color = $this->getColor();
        $content = $this->getContent();
        $icon = $this->getIcon();
        $iconPosition = $this->getIconPosition();
        $iconSize = $this->getIconSize();
        $size = $this->getSize();
        $tooltip = $this->getTooltip();
        $weight = $this->getWeight();
        $fontFamily = $this->getFontFamily();

        $copyableState = $this->getCopyableState($content) ?? $content;
        $copyMessage = $this->getCopyMessage($copyableState);
        $copyMessageDuration = $this->getCopyMessageDuration($copyableState);
        $isCopyable = $this->isCopyable($copyableState);

        $hasTooltip = filled($tooltip);

        if ($this->isBadge()) {
            if (! $size instanceof Size) {
                $size = filled($size) ? (is_string($size) ? (Size::tryFrom($size) ?? $size) : $size) : Size::Medium;
            }

            if (filled($iconSize) && (! $iconSize instanceof IconSize)) {
                $iconSize = IconSize::tryFrom($iconSize) ?? $iconSize;
            }

            $badgeAttributes = $this->getExtraAttributeBag()
                ->merge([
                    'type' => $isCopyable ? 'button' : null,
                    'wire:loading.attr' => $isCopyable ? 'disabled' : null,
                ], escape: false)
                ->class([
                    'fi-sc-text',
                    'fi-badge',
                    ($size instanceof BackedEnum) ? "fi-size-{$size->value}" : $size,
                ])
                ->color(BadgeComponent::class, $color ?? 'primary');

            ob_start(); ?>

            <<?= $isCopyable ? 'button' : 'span' ?>
                <?php if ($isCopyable) { ?>
                    x-on:click="
                        window.navigator.clipboard.writeText(<?= Js::from($copyableState) ?>)
                        $tooltip(<?= Js::from($copyMessage) ?>, {
                            theme: $store.theme,
                            timeout: <?= Js::from($copyMessageDuration) ?>,
                        })
                    "
                <?php } ?>
                <?php if ($hasTooltip) { ?>
                    x-tooltip="{
                        content: <?= Js::from($tooltip) ?>,
                        theme: $store.theme,
                        allowHTML: <?= Js::from($tooltip instanceof Htmlable) ?>,
                    }"
                <?php } ?>
                <?= $badgeAttributes->toHtml() ?>
            >
                <?php if ($iconPosition === IconPosition::Before && filled($icon)) { ?>
                    <?= generate_icon_html($icon, size: $iconSize ?? IconSize::Small)?->toHtml() ?>
                <?php } ?>

                <span class="fi-badge-label-ctn">
                    <span class="fi-badge-label"><?= e($content) ?></span>
                </span>

                <?php if ($iconPosition === IconPosition::After && filled($icon)) { ?>
                    <?= generate_icon_html($icon, size: $iconSize ?? IconSize::Small)?->toHtml() ?>
                <?php } ?>
            </<?= $isCopyable ? 'button' : 'span' ?>>

            <?php return ob_get_clean();
        }

        // Non-badge mode: simple span
        $spanAttributes = (new FilamentComponentAttributeBag)
            ->color(TextComponent::class, $color)
            ->class([
                'fi-sc-text',
                'fi-copyable' => $isCopyable,
                ($size instanceof BackedEnum) ? "fi-size-{$size->value}" : $size,
                ($weight instanceof FontWeight) ? "fi-font-{$weight->value}" : $weight,
                ($fontFamily instanceof FontFamily) ? "fi-font-{$fontFamily->value}" : $fontFamily,
            ])
            ->merge($this->getExtraAttributes(), escape: false);

        ob_start(); ?>

        <span
            <?php if ($isCopyable) { ?>
                role="button"
                tabindex="0"
                x-on:click="
                    window.navigator.clipboard.writeText(<?= Js::from($copyableState) ?>)
                    $tooltip(<?= Js::from($copyMessage) ?>, {
                        theme: $store.theme,
                        timeout: <?= Js::from($copyMessageDuration) ?>,
                    })
                "
                x-on:keydown.enter.prevent="$el.click()"
                x-on:keydown.space.prevent="$el.click()"
            <?php } ?>
            <?php if ($hasTooltip) { ?>
                x-tooltip="{
                    content: <?= Js::from($tooltip) ?>,
                    theme: $store.theme,
                    allowHTML: <?= Js::from($tooltip instanceof Htmlable) ?>,
                }"
            <?php } ?>
            <?= $spanAttributes->toHtml() ?>
        >
            <?= e($content) ?>
        </span>

        <?php return ob_get_clean();
    }
}
