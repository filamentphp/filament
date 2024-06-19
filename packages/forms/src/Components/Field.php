<?php

namespace Filament\Forms\Components;

use Closure;
use Exception;
use Filament\Actions\Action;
use Filament\Schema\Components\Component;
use Filament\Schema\Components\Decorations\Decoration;
use Filament\Schema\Components\Decorations\Layouts\AlignDecorations;
use Filament\Schema\Components\Decorations\Layouts\DecorationsLayout;
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

    const ABOVE_LABEL_DECORATIONS = 'above_label';

    const BELOW_LABEL_DECORATIONS = 'below_label';

    const BEFORE_LABEL_DECORATIONS = 'before_label';

    const AFTER_LABEL_DECORATIONS = 'after_label';

    const ABOVE_CONTENT_DECORATIONS = 'above_content';

    const BELOW_CONTENT_DECORATIONS = 'below_content';

    const BEFORE_CONTENT_DECORATIONS = 'before_content';

    const AFTER_CONTENT_DECORATIONS = 'after_content';

    const ABOVE_ERROR_MESSAGE_DECORATIONS = 'above_error_message';

    const BELOW_ERROR_MESSAGE_DECORATIONS = 'below_error_message';

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
     * @param  array<Decoration | Action> | DecorationsLayout | Decoration | Action | string | Closure | null  $decorations
     */
    public function aboveLabel(array | DecorationsLayout | Decoration | Action | string | Closure | null $decorations): static
    {
        $this->decorations(self::ABOVE_LABEL_DECORATIONS, $decorations);

        return $this;
    }

    /**
     * @param  array<Decoration | Action> | DecorationsLayout | Decoration | Action | string | Closure | null  $decorations
     */
    public function belowLabel(array | DecorationsLayout | Decoration | Action | string | Closure | null $decorations): static
    {
        $this->decorations(self::BELOW_LABEL_DECORATIONS, $decorations);

        return $this;
    }

    /**
     * @param  array<Decoration | Action> | DecorationsLayout | Decoration | Action | string | Closure | null  $decorations
     */
    public function beforeLabel(array | DecorationsLayout | Decoration | Action | string | Closure | null $decorations): static
    {
        $this->decorations(self::BEFORE_LABEL_DECORATIONS, $decorations);

        return $this;
    }

    /**
     * @param  array<Decoration | Action> | DecorationsLayout | Decoration | Action | string | Closure | null  $decorations
     */
    public function afterLabel(array | DecorationsLayout | Decoration | Action | string | Closure | null $decorations): static
    {
        $this->decorations(
            self::AFTER_LABEL_DECORATIONS,
            $decorations,
            makeDefaultLayoutUsing: fn (array $decorations): AlignDecorations => AlignDecorations::end($decorations),
        );

        return $this;
    }

    /**
     * @param  array<Decoration | Action> | DecorationsLayout | Decoration | Action | string | Closure | null  $decorations
     */
    public function aboveContent(array | DecorationsLayout | Decoration | Action | string | Closure | null $decorations): static
    {
        $this->decorations(self::ABOVE_CONTENT_DECORATIONS, $decorations);

        return $this;
    }

    /**
     * @param  array<Decoration | Action> | DecorationsLayout | Decoration | Action | string | Closure | null  $decorations
     */
    public function belowContent(array | DecorationsLayout | Decoration | Action | string | Closure | null $decorations): static
    {
        $this->decorations(self::BELOW_CONTENT_DECORATIONS, $decorations);

        return $this;
    }

    /**
     * @param  array<Decoration | Action> | DecorationsLayout | Decoration | Action | string | Closure | null  $decorations
     */
    public function beforeContent(array | DecorationsLayout | Decoration | Action | string | Closure | null $decorations): static
    {
        $this->decorations(self::BEFORE_CONTENT_DECORATIONS, $decorations);

        return $this;
    }

    /**
     * @param  array<Decoration | Action> | DecorationsLayout | Decoration | Action | string | Closure | null  $decorations
     */
    public function afterContent(array | DecorationsLayout | Decoration | Action | string | Closure | null $decorations): static
    {
        $this->decorations(
            self::AFTER_CONTENT_DECORATIONS,
            $decorations,
            makeDefaultLayoutUsing: fn (array $decorations): AlignDecorations => AlignDecorations::end($decorations),
        );

        return $this;
    }

    /**
     * @param  array<Decoration | Action> | DecorationsLayout | Decoration | Action | string | Closure | null  $decorations
     */
    public function aboveErrorMessage(array | DecorationsLayout | Decoration | Action | string | Closure | null $decorations): static
    {
        $this->decorations(self::ABOVE_ERROR_MESSAGE_DECORATIONS, $decorations);

        return $this;
    }

    /**
     * @param  array<Decoration | Action> | DecorationsLayout | Decoration | Action | string | Closure | null  $decorations
     */
    public function belowErrorMessage(array | DecorationsLayout | Decoration | Action | string | Closure | null $decorations): static
    {
        $this->decorations(self::BELOW_ERROR_MESSAGE_DECORATIONS, $decorations);

        return $this;
    }
}
