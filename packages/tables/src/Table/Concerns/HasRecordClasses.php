<?php

namespace Filament\Tables\Table\Concerns;

use Closure;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;

trait HasRecordClasses
{
    /**
     * @var array<string | int, bool | string> | string | Closure | null
     */
    protected array | string | Closure | null $recordClasses = null;

    /**
     * @param  array<string | int, bool | string> | string | Closure | null  $classes
     */
    public function recordClasses(array | string | Closure | null $classes): static
    {
        $this->recordClasses = $classes;

        return $this;
    }

    /**
     * @param  Model | array<string, mixed>  $record
     * @return array<string | int, bool | string>
     */
    public function getRecordClasses(Model | array $record): array
    {
        return Arr::wrap($this->evaluate(
            $this->recordClasses,
            namedInjections: [
                'record' => $record,
            ],
            typedInjections: ($record instanceof Model) ? [
                Model::class => $record,
                $record::class => $record,
            ] : [],
        ) ?? []);
    }
}
