<?php

namespace Filament\Forms\Components;

use Closure;
use Filament\Support\Concerns\HasExtraAlpineAttributes;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Str;

class Section extends Component implements Contracts\CanConcealComponents, Contracts\CanEntangleWithSingularRelationships
{
    use Concerns\CanBeCollapsed;
    use Concerns\CanBeCompacted;
    use Concerns\EntanglesStateWithSingularRelationship;
    use HasExtraAlpineAttributes;

    protected string $view = 'forms::components.section';

    protected string | Htmlable | Closure | null $description = null;

    protected string | Closure $heading;

    final public function __construct(string | Closure $heading)
    {
        $this->heading($heading);
    }

    public static function make(string | Closure $heading): static
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

    public function description(string | Htmlable | Closure | null $description = null): static
    {
        $this->description = $description;

        return $this;
    }

    public function heading(string | Closure $heading): static
    {
        $this->heading = $heading;

        return $this;
    }

    public function getDescription(): string | Htmlable | null
    {
        return $this->evaluate($this->description);
    }

    public function getHeading(): string
    {
        return $this->evaluate($this->heading);
    }

    public function getId(): ?string
    {
        $id = parent::getId();

        if (! $id) {
            $id = Str::slug($this->getHeading());

            if ($statePath = $this->getStatePath()) {
                $id = "{$statePath}.{$id}";
            }
        }

        return $id;
    }

    public function canConcealComponents(): bool
    {
        return $this->isCollapsible();
    }
}
