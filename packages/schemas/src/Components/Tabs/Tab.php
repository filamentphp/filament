<?php

namespace Filament\Schemas\Components\Tabs;

use BackedEnum;
use Closure;
use Filament\Schemas\Components\Component;
use Filament\Schemas\Components\Concerns\HasLabel;
use Filament\Schemas\Components\Contracts\CanConcealComponents;
use Filament\Support\Concerns\HasBadge;
use Filament\Support\Concerns\HasBadgeTooltip;
use Filament\Support\Concerns\HasIcon;
use Filament\Support\Concerns\HasIconPosition;
use Filament\Support\Enums\IconPosition;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Tab extends Component implements CanConcealComponents
{
    use HasBadge;
    use HasBadgeTooltip;
    use HasIcon;
    use HasIconPosition;
    use HasLabel;

    protected ?Closure $modifyQueryUsing = null;

    protected string | BackedEnum | Htmlable | Closure | null $badgeIcon = null;

    protected IconPosition | string | Closure | null $badgeIconPosition = null;

    /**
     * @var view-string
     */
    protected string $view = 'filament-schemas::components.tabs.tab';

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
        return $this->columns ?? $this->getContainer()->getAllColumns();
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
}
