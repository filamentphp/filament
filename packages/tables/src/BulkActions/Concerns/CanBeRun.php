<?php

namespace Filament\Tables\BulkActions\Concerns;

use Illuminate\Database\Eloquent\Collection;

trait CanBeRun
{
    protected $runUsing = null;

    public function runUsing(callable $callback): static
    {
        $this->runUsing = $callback;

        return $this;
    }

    public function run(Collection $records, array $data = []): void
    {
        $callback = $this->runUsing;

        if (! $callback) {
            return;
        }

        $callback($records, $data);
    }
}
