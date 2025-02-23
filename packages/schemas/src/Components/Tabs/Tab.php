<?php

namespace Filament\Schemas\Components\Tabs;

use Closure;
use Filament\Schemas\Components\Component;
use Filament\Schemas\Components\Contracts\CanConcealComponents;
use Filament\Support\Concerns\HasBadge;
use Filament\Support\Concerns\HasIcon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;

class Tab extends Component implements CanConcealComponents
{
    use HasBadge;
    use HasIcon;

    protected ?Closure $modifyQueryUsing = null;

    /**
     * @var view-string
     */
    protected string $view = 'filament-schemas::components.tabs.tab';

    final public function __construct(?string $label = null)
    {
        $this->label($label);
    }

    public static function make(?string $label = null): static
    {
        $static = app(static::class, ['label' => $label]);
        $static->configure();

        return $static;
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->key(fn (Tab $component): string => Str::slug(Str::transliterate($component->getLabel(), strict: true)));
    }

    /**
     * @return array<string, int | null>
     */
    public function getColumnsConfig(): array
    {
        return $this->columns ?? $this->getContainer()->getColumnsConfig();
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

    public function modifyQuery(Builder $query): Builder
    {
        return $this->evaluate($this->modifyQueryUsing, [
            'query' => $query,
        ]) ?? $query;
    }
}
