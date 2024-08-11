<?php

namespace Filament\Schema\Components;

use Closure;
use Filament\Actions\Action;
use Filament\Schema\Components\Concerns\EntanglesStateWithSingularRelationship;
use Filament\Schema\Components\Concerns\HasFooterActions;
use Filament\Schema\Components\Concerns\HasHeaderActions;
use Filament\Schema\Components\Contracts\CanEntangleWithSingularRelationships;
use Filament\Schema\Components\Contracts\ExposesStateToActionData;
use Filament\Schema\Components\Decorations\Layouts\AlignDecorations;
use Filament\Schema\Components\Decorations\Layouts\DecorationsLayout;

class Form extends Component implements CanEntangleWithSingularRelationships, Contracts\HasFooterActions, Contracts\HasHeaderActions, ExposesStateToActionData
{
    use EntanglesStateWithSingularRelationship;
    use HasFooterActions;
    use HasHeaderActions;

    /**
     * @var view-string
     */
    protected string $view = 'filament-schema::components.form';

    protected string | Closure | null $livewireSubmitHandler = null;

    const HEADER_DECORATIONS = 'header';

    const FOOTER_DECORATIONS = 'footer';

    /**
     * @param  array<Component> | Closure  $schema
     */
    final public function __construct(array | Closure $schema = [])
    {
        $this->schema($schema);
    }

    /**
     * @param  array<Component> | Closure  $schema
     */
    public static function make(array | Closure $schema = []): static
    {
        $static = app(static::class, ['schema' => $schema]);
        $static->configure();

        return $static;
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->header(fn (Form $component): array => $component->getHeaderActions());
        $this->setUpFooterActions();
    }

    public function action(Action | Closure | null $action): static
    {
        if ($action instanceof Closure) {
            $action = Action::make('submit')->action($action);
        }

        parent::action($action);

        return $this;
    }

    public function livewireSubmitHandler(string | Closure | null $handler): static
    {
        $this->livewireSubmitHandler = $handler;

        return $this;
    }

    public function getLivewireSubmitHandler(): ?string
    {
        return $this->evaluate($this->livewireSubmitHandler) ?? $this->action?->getLivewireClickHandler();
    }

    /**
     * @param  array<Component | Action> | DecorationsLayout | Component | Action | string | Closure | null  $decorations
     */
    public function header(array | DecorationsLayout | Component | Action | string | Closure | null $decorations): static
    {
        $this->decorations(
            static::HEADER_DECORATIONS,
            $decorations,
            makeDefaultLayoutUsing: fn (array $decorations): AlignDecorations => AlignDecorations::end($decorations),
        );

        return $this;
    }

    /**
     * @param  array<Component | Action> | DecorationsLayout | Component | Action | string | Closure | null  $decorations
     */
    public function footer(array | DecorationsLayout | Component | Action | string | Closure | null $decorations): static
    {
        $this->decorations(static::FOOTER_DECORATIONS, $decorations);

        return $this;
    }
}
