<?php

namespace Filament\Infolists\Components;

use Closure;
use Exception;
use Filament\Actions\Action;
use Filament\Schema\Components\Component;
use Filament\Schema\Components\Decorations\Layouts\DecorationsLayout;
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

    const ABOVE_LABEL_DECORATIONS = 'above_label';

    const BELOW_LABEL_DECORATIONS = 'below_label';

    const BEFORE_LABEL_DECORATIONS = 'before_label';

    const AFTER_LABEL_DECORATIONS = 'after_label';

    const ABOVE_CONTENT_DECORATIONS = 'above_content';

    const BELOW_CONTENT_DECORATIONS = 'below_content';

    const BEFORE_CONTENT_DECORATIONS = 'before_content';

    const AFTER_CONTENT_DECORATIONS = 'after_content';

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
     * @param  array<Component | Action> | DecorationsLayout | Component | Action | string | Closure | null  $decorations
     */
    public function aboveLabel(array | DecorationsLayout | Component | Action | string | Closure | null $decorations): static
    {
        $this->decorations(static::ABOVE_LABEL_DECORATIONS, $decorations);

        return $this;
    }

    /**
     * @param  array<Component | Action> | DecorationsLayout | Component | Action | string | Closure | null  $decorations
     */
    public function belowLabel(array | DecorationsLayout | Component | Action | string | Closure | null $decorations): static
    {
        $this->decorations(static::BELOW_LABEL_DECORATIONS, $decorations);

        return $this;
    }

    /**
     * @param  array<Component | Action> | DecorationsLayout | Component | Action | string | Closure | null  $decorations
     */
    public function beforeLabel(array | DecorationsLayout | Component | Action | string | Closure | null $decorations): static
    {
        $this->decorations(static::BEFORE_LABEL_DECORATIONS, $decorations);

        return $this;
    }

    /**
     * @param  array<Component | Action> | DecorationsLayout | Component | Action | string | Closure | null  $decorations
     */
    public function afterLabel(array | DecorationsLayout | Component | Action | string | Closure | null $decorations): static
    {
        $this->decorations(static::AFTER_LABEL_DECORATIONS, $decorations);

        return $this;
    }

    /**
     * @param  array<Component | Action> | DecorationsLayout | Component | Action | string | Closure | null  $decorations
     */
    public function aboveContent(array | DecorationsLayout | Component | Action | string | Closure | null $decorations): static
    {
        $this->decorations(static::ABOVE_CONTENT_DECORATIONS, $decorations);

        return $this;
    }

    /**
     * @param  array<Component | Action> | DecorationsLayout | Component | Action | string | Closure | null  $decorations
     */
    public function belowContent(array | DecorationsLayout | Component | Action | string | Closure | null $decorations): static
    {
        $this->decorations(static::BELOW_CONTENT_DECORATIONS, $decorations);

        return $this;
    }

    /**
     * @param  array<Component | Action> | DecorationsLayout | Component | Action | string | Closure | null  $decorations
     */
    public function beforeContent(array | DecorationsLayout | Component | Action | string | Closure | null $decorations): static
    {
        $this->decorations(static::BEFORE_CONTENT_DECORATIONS, $decorations);

        return $this;
    }

    /**
     * @param  array<Component | Action> | DecorationsLayout | Component | Action | string | Closure | null  $decorations
     */
    public function afterContent(array | DecorationsLayout | Component | Action | string | Closure | null $decorations): static
    {
        $this->decorations(static::AFTER_CONTENT_DECORATIONS, $decorations);

        return $this;
    }
}
