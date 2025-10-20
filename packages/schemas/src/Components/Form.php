<?php

namespace Filament\Schemas\Components;

use Closure;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Schemas\Components\Concerns\EntanglesStateWithSingularRelationship;
use Filament\Schemas\Components\Contracts\CanEntangleWithSingularRelationships;
use Filament\Schemas\Components\Contracts\ExposesStateToActionData;
use Filament\Schemas\Schema;
use Illuminate\Contracts\Support\Htmlable;

class Form extends Component implements CanEntangleWithSingularRelationships, ExposesStateToActionData
{
    use EntanglesStateWithSingularRelationship;

    /**
     * @var view-string
     */
    protected string $view = 'filament-schemas::components.form';

    protected string | Closure | null $livewireSubmitHandler = null;

    const HEADER_SCHEMA_KEY = 'header';

    const FOOTER_SCHEMA_KEY = 'footer';

    /**
     * @param  array<Component | Action | ActionGroup> | Closure  $schema
     */
    final public function __construct(array | Closure $schema = [])
    {
        $this->schema($schema);
    }

    /**
     * @param  array<Component | Action | ActionGroup> | Closure  $schema
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

    /**
     * @param  array<Component | Action | ActionGroup | string | Htmlable> | Schema | Component | Action | ActionGroup | string | Htmlable | Closure | null  $components
     */
    public function header(array | Schema | Component | Action | ActionGroup | string | Htmlable | Closure | null $components): static
    {
        $this->childComponents($components, static::HEADER_SCHEMA_KEY);

        return $this;
    }

    /**
     * @param  array<Component | Action | ActionGroup | string | Htmlable> | Schema | Component | Action | ActionGroup | string | Htmlable | Closure | null  $components
     */
    public function footer(array | Schema | Component | Action | ActionGroup | string | Htmlable | Closure | null $components): static
    {
        $this->childComponents($components, static::FOOTER_SCHEMA_KEY);

        return $this;
    }

    protected function configureChildSchema(Schema $schema, string $key): Schema
    {
        $schema = parent::configureChildSchema($schema, $key);

        if (in_array($key, [
            static::HEADER_SCHEMA_KEY,
            static::FOOTER_SCHEMA_KEY,
        ])) {
            $schema->embeddedInParentComponent();
        }

        return $schema;
    }
}
