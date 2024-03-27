<?php

namespace Filament\Schema\Components\Tabs;

use Filament\Forms\Components\Tabs\view;
use Filament\Schema\Components\Component;
use Filament\Schema\Components\Contracts\CanConcealComponents;
use Filament\Support\Concerns\HasBadge;
use Filament\Support\Concerns\HasIcon;
use Illuminate\Support\Str;

class Tab extends Component implements CanConcealComponents
{
    use HasBadge;
    use HasIcon;

    /**
     * @var view-string
     */
    protected string $view = 'filament-schema::components.tabs.tab';

    final public function __construct(string $label)
    {
        $this->label($label);
    }

    public static function make(string $label): static
    {
        $static = app(static::class, ['label' => $label]);
        $static->configure();

        return $static;
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->key(fn (Tab $component): string => Str::slug($component->getLabel()));
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
}
