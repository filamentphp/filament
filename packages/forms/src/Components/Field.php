<?php

namespace Filament\Forms\Components;

use Closure;
use Exception;
use Filament\Actions\Action;
use Filament\Schema\Components\Component;
use Filament\Schema\Components\Decorations\Decoration;
use Filament\Schema\Components\Decorations\Layouts\AlignDecorations;
use Filament\Schema\Components\Decorations\Layouts\Layout;
use Filament\Schema\Components\StateCasts\Contracts\StateCast;
use Filament\Schema\Components\StateCasts\EnumStateCast;

class Field extends Component implements Contracts\HasValidationRules
{
    use Concerns\CanBeAutofocused;
    use Concerns\CanBeMarkedAsRequired;
    use Concerns\CanBeValidated;
    use Concerns\HasEnum;
    use Concerns\HasExtraFieldWrapperAttributes;
    use Concerns\HasHelperText;
    use Concerns\HasHint;
    use Concerns\HasName;

    protected string $viewIdentifier = 'field';

    const ABOVE_LABEL_DECORATION = 'above_label';

    const BELOW_LABEL_DECORATION = 'below_label';

    const BEFORE_LABEL_DECORATION = 'before_label';

    const AFTER_LABEL_DECORATION = 'after_label';

    const ABOVE_CONTENT_DECORATION = 'above_content';

    const BELOW_CONTENT_DECORATION = 'below_content';

    const BEFORE_CONTENT_DECORATION = 'before_content';

    const AFTER_CONTENT_DECORATION = 'after_content';

    const ABOVE_ERROR_MESSAGE_DECORATION = 'above_error_message';

    const BELOW_ERROR_MESSAGE_DECORATION = 'below_error_message';

    final public function __construct(string $name)
    {
        $this->name($name);
        $this->statePath($name);
    }

    public static function make(?string $name = null): static
    {
        $fieldClass = static::class;

        $name ??= static::getDefaultName();

        if ($name === null) {
            throw new Exception("Field of class [$fieldClass] must have a unique name, passed to the [make()] method.");
        }

        $static = app($fieldClass, ['name' => $name]);
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

    /**
     * @return array<StateCast>
     */
    public function getDefaultStateCasts(): array
    {
        $casts = parent::getDefaultStateCasts();

        if ($enumStateCast = $this->getEnumDefaultStateCast()) {
            $casts[] = $enumStateCast;
        }

        return $casts;
    }

    public function getEnumDefaultStateCast(): ?StateCast
    {
        $enum = $this->getEnum();

        if (blank($enum)) {
            return null;
        }

        return app(
            EnumStateCast::class,
            ['enum' => $enum],
        );
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
        $this->decorations(
            self::AFTER_LABEL_DECORATION,
            $decorations,
            makeDefaultLayoutUsing: fn (array $decorations): AlignDecorations => AlignDecorations::end($decorations),
        );

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
        $this->decorations(
            self::AFTER_CONTENT_DECORATION,
            $decorations,
            makeDefaultLayoutUsing: fn (array $decorations): AlignDecorations => AlignDecorations::end($decorations),
        );

        return $this;
    }

    /**
     * @param  array<Decoration | Action> | Layout | Decoration | Action | string | Closure | null  $decorations
     */
    public function aboveErrorMessage(array | Layout | Decoration | Action | string | Closure | null $decorations): static
    {
        $this->decorations(self::ABOVE_ERROR_MESSAGE_DECORATION, $decorations);

        return $this;
    }

    /**
     * @param  array<Decoration | Action> | Layout | Decoration | Action | string | Closure | null  $decorations
     */
    public function belowErrorMessage(array | Layout | Decoration | Action | string | Closure | null $decorations): static
    {
        $this->decorations(self::BELOW_ERROR_MESSAGE_DECORATION, $decorations);

        return $this;
    }
}
