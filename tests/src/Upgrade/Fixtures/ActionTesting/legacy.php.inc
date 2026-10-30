<?php

namespace Filament\Tests\Upgrade;

use Closure;

// Pinned from `19bd1e639e80f7e8d80b1a2c778d97387f87aa07`, `TestsActions`.
class LegacyActionTestingCallbacks
{
    public function setActionData(): Closure
    {
        return function (array $data): static {
            $this->fillForm($data);

            return $this;
        };
    }

    public function assertActionDataSet(): Closure
    {
        return function (array | Closure $data): static {
            $this->assertSchemaStateSet($data);

            return $this;
        };
    }

    public function assertHasActionErrors(): Closure
    {
        return function (array $keys = []): static {
            $this->assertHasFormErrors($keys);

            return $this;
        };
    }

    public function assertHasNoActionErrors(): Closure
    {
        return function (array $keys = []): static {
            $this->assertHasNoFormErrors($keys);

            return $this;
        };
    }
}
