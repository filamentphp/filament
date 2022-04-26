<?php

namespace Filament\Tables\Actions;

use Closure;
use Filament\Support\Concerns\Configurable;
use Illuminate\Support\Traits\Conditionable;
use Illuminate\Support\Traits\Macroable;
use Illuminate\Support\Traits\Tappable;

class BulkAction
{
    use Concerns\BelongsToTable;
    use Concerns\CanBeHidden;
    use Concerns\CanBeMounted;
    use Concerns\CanDeselectRecordsAfterCompletion;
    use Concerns\CanOpenModal;
    use Concerns\CanRequireConfirmation;
    use Concerns\EvaluatesClosures;
    use Concerns\HasAction;
    use Concerns\HasColor;
    use Concerns\HasFormSchema;
    use Concerns\HasIcon;
    use Concerns\HasLabel;
    use Concerns\HasName;
    use Concerns\HasRecords;
    use Conditionable;
    use Configurable;
    use Macroable;
    use Tappable;

    final public function __construct(string $name)
    {
        $this->name($name);
    }

    public static function make(string $name): static
    {
        $static = app(static::class, ['name' => $name]);
        $static->setUp();

        return $static;
    }

    protected function setUp(): void
    {
        $this->configure();
    }

    public function call(array $data = [])
    {
        if ($this->isHidden()) {
            return;
        }

        $action = $this->getAction();

        if (! $action) {
            return;
        }

        try {
            return $this->evaluate($action, [
                'data' => $data,
            ]);
        } finally {
            if ($this->shouldDeselectRecordsAfterCompletion()) {
                $this->getLivewire()->deselectAllTableRecords();
            }
        }
    }

    public function getAction(): ?Closure
    {
        $action = $this->action;

        if (is_string($action)) {
            $action = Closure::fromCallable([$this->getLivewire(), $action]);
        }

        return $action;
    }

    protected function getLivewireSubmitActionName(): string
    {
        return 'callMountedTableBulkAction';
    }
}
