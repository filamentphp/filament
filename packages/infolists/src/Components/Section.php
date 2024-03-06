<?php

namespace Filament\Infolists\Components;

use Closure;
use Filament\Support\Concerns\HasDescription;
use Filament\Support\Concerns\HasExtraAlpineAttributes;
use Filament\Support\Concerns\HasHeading;
use Filament\Support\Concerns\HasIcon;
use Filament\Support\Concerns\HasIconColor;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Str;

class Section extends Component implements Contracts\HasFooterActions, Contracts\HasHeaderActions
{
    use Concerns\CanBeCollapsed;
    use Concerns\CanBeCompacted;
    use Concerns\EntanglesStateWithSingularRelationship;
    use Concerns\HasFooterActions;
    use Concerns\HasHeaderActions;
    use HasDescription;
    use HasExtraAlpineAttributes;
    use HasHeading;
    use HasIcon;
    use HasIconColor;

    /**
     * @var view-string
     */
    protected string $view = 'filament-infolists::components.section';

    protected bool | Closure | null $isAside = null;

    protected bool | Closure $isContentBefore = false;

    /**
     * @param  string | array<Component> | Htmlable | Closure | null  $heading
     */
    final public function __construct(string | array | Htmlable | Closure | null $heading = null)
    {
        is_array($heading)
            ? $this->childComponents($heading)
            : $this->heading($heading);
    }

    /**
     * @param  string | array<Component> | Htmlable | Closure | null  $heading
     */
    public static function make(string | array | Htmlable | Closure | null $heading = null): static
    {
        $static = app(static::class, ['heading' => $heading]);
        $static->configure();

        return $static;
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->columnSpan('full');
    }

    public function aside(bool | Closure | null $condition = true): static
    {
        $this->isAside = $condition;

        return $this;
    }

    public function getId(): ?string
    {
        $id = parent::getId();

        if (filled($id)) {
            return $id;
        }

        $heading = $this->getHeading();

        if (blank($heading)) {
            return null;
        }

        $id = Str::slug($heading);

        if ($statePath = $this->getStatePath()) {
            $id = "{$statePath}.{$id}";
        }

        return $id;
    }

    public function getKey(): ?string
    {
        return parent::getKey() ?? ($this->getActions() ? $this->getId() : null);
    }

    public function isAside(): bool
    {
        return (bool) ($this->evaluate($this->isAside) ?? false);
    }

    public function contentBefore(bool | Closure $condition = true): static
    {
        $this->isContentBefore = $condition;

        return $this;
    }

    public function isContentBefore(): bool
    {
        return (bool) $this->evaluate($this->isContentBefore);
    }
}
