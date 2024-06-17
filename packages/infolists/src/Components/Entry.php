<?php

namespace Filament\Infolists\Components;

use Closure;
use Exception;
use Filament\Actions\Action;
use Filament\Schema\Components\Component;
use Filament\Schema\Components\Decorations\Decoration;
use Filament\Schema\Components\Decorations\Layouts\Layout;
use Filament\Support\Concerns\HasAlignment;
use Filament\Support\Concerns\HasPlaceholder;
use Filament\Support\Concerns\HasTooltip;
use Illuminate\Contracts\Support\Htmlable;

class Entry extends Component
{
    use Concerns\CanOpenUrl;
    use Concerns\HasExtraEntryWrapperAttributes;
    use Concerns\HasHelperText;
    use Concerns\HasHint;
    use Concerns\HasName;
    use HasAlignment;
    use HasPlaceholder;
    use HasTooltip;

    protected string $viewIdentifier = 'entry';

    const ABOVE_LABEL_DECORATION = 'above_label';

    const BELOW_LABEL_DECORATION = 'below_label';

    const BEFORE_LABEL_DECORATION = 'before_label';

    const AFTER_LABEL_DECORATION = 'after_label';

    const ABOVE_CONTENT_DECORATION = 'above_content';

    const BELOW_CONTENT_DECORATION = 'below_content';

    const BEFORE_CONTENT_DECORATION = 'before_content';

    const AFTER_CONTENT_DECORATION = 'after_content';

    final public function __construct(string $name)
    {
        $this->name($name);
        $this->statePath($name);
    }

    public static function make(?string $name = null): static
    {
        $entryClass = static::class;

        $name ??= static::getDefaultName();

        if (blank($name)) {
            throw new Exception("Entry of class [$entryClass] must have a unique name, passed to the [make()] method.");
        }

        $static = app($entryClass, ['name' => $name]);
        $static->configure();

        return $static;
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->setUpHint();
    }

    public static function getDefaultName(): ?string
    {
        return null;
    }

    public function getState(): mixed
    {
        return $this->getConstantState();
    }

    public function getLabel(): string | Htmlable | null
    {
        $label = parent::getLabel() ?? (string) str($this->getName())
            ->before('.')
            ->kebab()
            ->replace(['-', '_'], ' ')
            ->ucfirst();

        return (is_string($label) && $this->shouldTranslateLabel) ?
            __($label) :
            $label;
    }

    public function state(mixed $state): static
    {
        $this->constantState($state);

        return $this;
    }

    /**
     * @param  array<Decoration | Action> | Layout | Decoration | Action | string | Closure | null  $decorations
     */
    public function aboveLabel(array | Layout | Decoration | Action | string | Closure | null $decorations): static
    {
        $this->decorations(self::ABOVE_LABEL_DECORATION, $decorations);

        return $this;
    }

    /**
     * @param  array<Decoration | Action> | Layout | Decoration | Action | string | Closure | null  $decorations
     */
    public function belowLabel(array | Layout | Decoration | Action | string | Closure | null $decorations): static
    {
        $this->decorations(self::BELOW_LABEL_DECORATION, $decorations);

        return $this;
    }

    /**
     * @param  array<Decoration | Action> | Layout | Decoration | Action | string | Closure | null  $decorations
     */
    public function beforeLabel(array | Layout | Decoration | Action | string | Closure | null $decorations): static
    {
        $this->decorations(self::BEFORE_LABEL_DECORATION, $decorations);

        return $this;
    }

    /**
     * @param  array<Decoration | Action> | Layout | Decoration | Action | string | Closure | null  $decorations
     */
    public function afterLabel(array | Layout | Decoration | Action | string | Closure | null $decorations): static
    {
        $this->decorations(self::AFTER_LABEL_DECORATION, $decorations);

        return $this;
    }

    /**
     * @param  array<Decoration | Action> | Layout | Decoration | Action | string | Closure | null  $decorations
     */
    public function aboveContent(array | Layout | Decoration | Action | string | Closure | null $decorations): static
    {
        $this->decorations(self::ABOVE_CONTENT_DECORATION, $decorations);

        return $this;
    }

    /**
     * @param  array<Decoration | Action> | Layout | Decoration | Action | string | Closure | null  $decorations
     */
    public function belowContent(array | Layout | Decoration | Action | string | Closure | null $decorations): static
    {
        $this->decorations(self::BELOW_CONTENT_DECORATION, $decorations);

        return $this;
    }

    /**
     * @param  array<Decoration | Action> | Layout | Decoration | Action | string | Closure | null  $decorations
     */
    public function beforeContent(array | Layout | Decoration | Action | string | Closure | null $decorations): static
    {
        $this->decorations(self::BEFORE_CONTENT_DECORATION, $decorations);

        return $this;
    }

    /**
     * @param  array<Decoration | Action> | Layout | Decoration | Action | string | Closure | null  $decorations
     */
    public function afterContent(array | Layout | Decoration | Action | string | Closure | null $decorations): static
    {
        $this->decorations(self::AFTER_CONTENT_DECORATION, $decorations);

        return $this;
    }
}
