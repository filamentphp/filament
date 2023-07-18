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

    public function getRecordUrl(Model $record): ?string
    {
        return $this->evaluate(
            $this->recordUrl,
            namedInjections: [
                'record' => $record,
            ],
            typedInjections: [
                Model::class => $record,
                $record::class => $record,
            ],
        );
    }
}
