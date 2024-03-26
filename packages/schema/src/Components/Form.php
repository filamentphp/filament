<?php

namespace Filament\Schema\Components;

use Closure;
use Filament\Actions\Action;
use Filament\Schema\Components\Concerns\EntanglesStateWithSingularRelationship;
use Filament\Schema\Components\Concerns\HasFooterActions;
use Filament\Schema\Components\Concerns\HasHeaderActions;
use Filament\Schema\Components\Contracts\CanEntangleWithSingularRelationships;
use Filament\Schema\Components\Contracts\ExposesStateToActionData;

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
}
