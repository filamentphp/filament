<?php

namespace Filament\Tables\Table\Concerns;

use Closure;
use Illuminate\Database\Eloquent\Model;

trait HasRecordUrl
{
    protected bool | Closure $shouldOpenRecordUrlInNewTab = false;

    protected string | Closure | null $recordUrl = null;

    public function openRecordUrlInNewTab(bool | Closure $condition = true): static
    {
        $this->shouldOpenRecordUrlInNewTab = $condition;

        return $this;
    }

    public function recordUrl(string | Closure | null $url, bool | Closure $shouldOpenInNewTab = false): static
    {
        $this->openRecordUrlInNewTab($shouldOpenInNewTab);
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

    /**
     * @param  Model | array<string, mixed>  $record
     */
    public function shouldOpenRecordUrlInNewTab(Model | array $record): bool
    {
        return (bool) $this->evaluate(
            $this->shouldOpenRecordUrlInNewTab,
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
