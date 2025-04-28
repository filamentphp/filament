<?php

namespace Filament\Tables\Table\Concerns;

use Closure;
use Illuminate\Database\Eloquent\Model;
use Illuminate\View\ComponentAttributeBag;
trait HasRecordUrl
{
    protected bool|Closure $shouldOpenRecordUrlInNewTab = false;

    protected string|Closure|null $recordUrl = null;

    protected array $recordUrlExtraAttributes = [];

    public function openRecordUrlInNewTab(bool|Closure $condition = true): static
    {
        $this->shouldOpenRecordUrlInNewTab = $condition;

        return $this;
    }

    public function recordUrl(string|Closure|null $url, bool|Closure $shouldOpenInNewTab = false): static
    {
        $this->openRecordUrlInNewTab($shouldOpenInNewTab);
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

    public function shouldOpenRecordUrlInNewTab(Model $record): bool
    {
        return (bool) $this->evaluate(
            $this->shouldOpenRecordUrlInNewTab,
            namedInjections: [
                'record' => $record,
            ],
            typedInjections: [
                Model::class => $record,
                    $record::class => $record,
            ],
        );
    }

    public function recordUrlextraAttributes(array|Closure $attributes, bool $merge = false): static
    {
        if ($merge) {
            $this->recordUrlextraAttributes[] = $attributes;
        } else {
            $this->recordUrlextraAttributes = [$attributes];
        }

        return $this;
    }


    public function getRecordUrlExtraAttributes(): array
    {
        $temporaryAttributeBag = new ComponentAttributeBag;

        foreach ($this->recordUrlextraAttributes as $recordUrlextraAttributes) {
            $temporaryAttributeBag = $temporaryAttributeBag->merge($this->evaluate($recordUrlextraAttributes));
        }

        return $temporaryAttributeBag->getAttributes();
    }

    public function getExtraAttributeBag(): ComponentAttributeBag
    {
        return new ComponentAttributeBag($this->getExtraAttributes());
    }
}
