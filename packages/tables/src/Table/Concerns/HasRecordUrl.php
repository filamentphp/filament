<?php

namespace Filament\Tables\Table\Concerns;

use Closure;
use Illuminate\Database\Eloquent\Model;

trait HasRecordUrl
{
    protected string | Closure | null $recordUrl = null;

    public function recordUrl(string | Closure | null $url): static
    {
        $this->recordUrl = $url;

        return $this;
    }

    /**
     * @param  Model | array<string, mixed>  $record
     */
    public function getRecordUrl(Model | array $record): ?string
    {
        return $this->evaluate(
            $this->recordUrl,
            namedInjections: [
                'record' => $record,
            ],
            typedInjections: ($record instanceof Model) ? [
                Model::class => $record,
                $record::class => $record,
            ] : [],
        );
    }
}
