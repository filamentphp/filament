<?php

namespace Filament\Support\Actions\Concerns;

use Closure;
use Illuminate\Database\Eloquent\Model;
use function Filament\Support\get_model_label;

trait InteractsWithRecord
{
    protected Model | Closure | null $record = null;

    protected string | Closure | null $recordTitle = null;

    public function record(Model | Closure | null $record): static
    {
        $this->record = $record;

        return $this;
    }

    public function recordTitle(string | Closure | null $title): static
    {
        $this->recordTitle = $title;

        return $this;
    }

    public function getRecord(): ?Model
    {
        return $this->evaluate(
            $this->record,
            exceptParameters: ['record'],
        );
    }

    public function getRecordTitle(): ?string
    {
        $title = $this->evaluate($this->recordTitle);

        if (filled($title)) {
            return $title;
        }

        $record = $this->getRecord();

        if (! $record) {
            return null;
        }

        return get_model_label($record::class);
    }

    protected function parseAuthorizationArguments(array $arguments): array
    {
        if ($record = $this->getRecord()) {
            array_unshift($arguments, $record);
        }

        return $arguments;
    }
}
