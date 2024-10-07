<?php

namespace Filament\Tables\Table\Concerns;

use Closure;
use Filament\Actions\Action;
use Illuminate\Database\Eloquent\Model;

trait HasRecordAction
{
    protected string | Closure | null $recordAction = null;

    public function recordAction(string | Closure | null $action): static
    {
        $this->recordAction = $action;

        return $this;
    }

    /**
     * @param  Model | array<string, mixed>  $record
     */
    public function getRecordAction(Model | array $record): ?string
    {
        $action = $this->evaluate(
            $this->recordAction,
            namedInjections: [
                'record' => $record,
            ],
            typedInjections: $record instanceof Model ? [
                Model::class => $record,
                $record::class => $record,
            ] : [],
        );

        if (! $action) {
            return null;
        }

        if (! class_exists($action)) {
            return $action;
        }

        if (! is_subclass_of($action, Action::class)) {
            return $action;
        }

        return $action::getDefaultName() ?? $action;
    }
}
