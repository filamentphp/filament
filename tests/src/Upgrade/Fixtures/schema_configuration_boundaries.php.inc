<?php

namespace Filament\Tests\Upgrade\Fixtures\SchemaConfigurationBoundaries;

use Closure;
use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Resources\Pages\CreateRecord;
use Livewire\Features\SupportTesting\Testable;

class CustomAction extends Action
{
    public function fillForm(array | Closure | null $customData): static
    {
        return $this;
    }
}

class CustomSelect extends Select
{
    public function createOptionSchema(array | Closure | null $customSchema): static
    {
        return $this;
    }
}

class CreatePage extends CreateRecord
{
    protected function preserveFormDataWhenCreatingAnother(array $data): array
    {
        return $data;
    }
}

function inspect(CustomAction $action, CustomSelect $select, Testable $test, mixed $unknown, string $method): void
{
    $action->fillForm(customData: []);
    $select->createOptionForm([]);
    $test->fillForm(['title' => 'Test']);
    $unknown->fillForm([]);
    $action->{$method}([]);
    $reference = [$action, 'fillForm'];
}
