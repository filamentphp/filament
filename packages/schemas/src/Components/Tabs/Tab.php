<?php

namespace Filament\Schemas\Components\Tabs;

use BackedEnum;
use Closure;
use Filament\Schemas\Components\Component;
use Filament\Schemas\Components\Concerns\HasLabel;
use Filament\Schemas\Components\Contracts\CanConcealComponents;
use Filament\Schemas\Components\Tabs;
use Filament\Support\Components\Contracts\HasEmbeddedView;
use Filament\Support\Concerns\HasBadge;
use Filament\Support\Concerns\HasBadgeTooltip;
use Filament\Support\Concerns\HasIcon;
use Filament\Support\Concerns\HasIconPosition;
use Filament\Support\Enums\IconPosition;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Js;
use Illuminate\Support\Str;
use Illuminate\View\ComponentAttributeBag;

class Tab extends Component implements CanConcealComponents, HasEmbeddedView
{
    use HasBadge;
    use HasBadgeTooltip;
    use HasIcon;
    use HasIconPosition;
    use HasLabel;

    protected ?Closure $modifyQueryUsing = null;

    protected bool | Closure $shouldExcludeQueryWhenResolvingRecord = false;

    protected string | BackedEnum | Htmlable | Closure | null $badgeIcon = null;

    protected IconPosition | string | Closure | null $badgeIconPosition = null;

    protected bool | Closure $isBadgeDeferred = false;

    final public function __construct(string | Htmlable | Closure | null $label = null)
    {
        $this->label($label);
    }

    public static function make(string | Htmlable | Closure | null $label = null): static
    {
        $static = app(static::class, ['label' => $label]);
        $static->configure();

        return $static;
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->key(function (Tab $component): string {
            $label = $component->getLabel();
            $statePath = $component->getStatePath();

            return Str::slug(Str::transliterate($label, strict: true)) . '::' . (filled($statePath) ? "{$statePath}::tab" : 'tab');
        }, isInheritable: false);
    }

    /**
     * @return array<string, int | null>
     */
    public function getAllColumns(): array
    {
        if ($this->columns === null) {
            return $this->getContainer()->getAllColumns();
        }

        return parent::getAllColumns();
    }

    public function canConcealComponents(): bool
    {
        return true;
    }

    public function query(?Closure $callback): static
    {
        $this->modifyQueryUsing($callback);

        return $this;
    }

    public function modifyQueryUsing(?Closure $callback): static
    {
        $this->modifyQueryUsing = $callback;

        return $this;
    }

    /**
     * @template TModel of Model
     *
     * @param  Builder<TModel>  $query
     * @return Builder<TModel>
     */
    public function modifyQuery(Builder $query): Builder
    {
        return $this->evaluate($this->modifyQueryUsing, [
            'query' => $query,
        ]) ?? $query;
    }

    public function badgeIcon(string | BackedEnum | Htmlable | Closure | null $icon): static
    {
        $this->badgeIcon = $icon;

        return $this;
    }

    public function badgeIconPosition(IconPosition | string | Closure | null $position): static
    {
        $this->badgeIconPosition = $position;

        return $this;
    }

    public function getBadgeIcon(): string | BackedEnum | Htmlable | null
    {
        return $this->evaluate($this->badgeIcon);
    }

    public function getBadgeIconPosition(): IconPosition | string
    {
        return $this->evaluate($this->badgeIconPosition) ?? IconPosition::Before;
    }

    public function deferBadge(bool | Closure $condition = true): static
    {
        $this->isBadgeDeferred = $condition;

        return $this;
    }

    public function isBadgeDeferred(): bool
    {
        return (bool) $this->evaluate($this->isBadgeDeferred);
    }

    public function toEmbeddedHtml(): string
    {
        $id = $this->getId();
        $key = $this->getKey(isAbsolute: false);
        /** @var Tabs $tabs */
        $tabs = $this->getContainer()->getParentComponent();
        $livewireProperty = $tabs->getLivewireProperty();

        $childSchema = $this->getChildSchema();

        if (empty($childSchema->getComponents())) {
            return '';
        }

        $attributes = (new ComponentAttributeBag)
            ->merge([
                'aria-labelledby' => $id,
                'id' => $id,
                'role' => 'tabpanel',
                'wire:key' => $this->getLivewireKey() . '.container',
            ], escape: false)
            ->merge($this->getExtraAttributes(), escape: false)
            ->class(['fi-sc-tabs-tab']);

        if (blank($livewireProperty)) {
            ob_start(); ?>

            <div
                x-bind:class="{
                    'fi-active': tab === <?= Js::from($key) ?>,
                }"
                x-on:expand="tab = <?= Js::from($key) ?>"
                <?= $attributes->toHtml() ?>
            >
                <?= $childSchema->toHtml() ?>
            </div>

            <?php return ob_get_clean();
        }

        if (strval($tabs->getLivewire()->{$livewireProperty}) !== strval($key)) {
            return '';
        }

        ob_start(); ?>

        <div <?= $attributes->class(['fi-active'])->toHtml() ?>>
            <?= $childSchema->toHtml() ?>
        </div>

        <?php return ob_get_clean();
    }

    public function excludeQueryWhenResolvingRecord(bool | Closure $condition = true): static
    {
        $this->shouldExcludeQueryWhenResolvingRecord = $condition;

        return $this;
    }

    public function shouldExcludeQueryWhenResolvingRecord(): bool
    {
        return (bool) $this->evaluate($this->shouldExcludeQueryWhenResolvingRecord);
    }
}
