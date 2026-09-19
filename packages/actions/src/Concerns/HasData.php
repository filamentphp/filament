<?php

namespace Filament\Actions\Concerns;

use Closure;
use Filament\Schemas\Components\Contracts\ExposesStateToActionData;
use Filament\Schemas\Schema;
use Illuminate\Support\Arr;

trait HasData
{
    /**
     * @var array<string, mixed>
     */
    protected array $data = [];

    protected ?Closure $mutateDataUsing = null;

    public function mutateDataUsing(?Closure $callback): static
    {
        $this->mutateDataUsing = $callback;

        return $this;
    }

    /**
     * @deprecated Use `mutateDataUsing()` instead.
     */
    public function mutateFormDataUsing(?Closure $callback): static
    {
        $this->mutateDataUsing($callback);

        return $this;
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function data(array $data, bool $shouldMutate = true): static
    {
        if ($shouldMutate && $this->mutateDataUsing) {
            $data = $this->evaluate($this->mutateDataUsing, [
                'data' => $data,
            ]);
        }

        $this->data = $data;

        return $this;
    }

    /**
     * @deprecated Use `data()` instead.
     *
     * @param  array<string, mixed>  $data
     */
    public function formData(array $data, bool $shouldMutate = true): static
    {
        $this->data($data, $shouldMutate);

        return $this;
    }

    public function resetData(): static
    {
        $this->data([], shouldMutate: false);

        return $this;
    }

    /**
     * @deprecated Use `resetData()` instead.
     */
    public function resetFormData(): static
    {
        $this->resetData();

        return $this;
    }

    /**
     * @return array<string, mixed>
     */
    public function getData(): array
    {
        return $this->data;
    }

    /**
     * @deprecated Use `getData()` instead.
     *
     * @return array<string, mixed>
     */
    public function getFormData(): array
    {
        return $this->getData();
    }

    /**
     * @return array<string, mixed>
     */
    public function getRawData(): array
    {
        return $this->getLivewire()->mountedActions[$this->getNestingIndex()]['data'] ?? [];
    }

    /**
     * @deprecated Use `getRawData()` instead.
     *
     * @return array<string, mixed>
     */
    public function getRawFormData(): array
    {
        return $this->getRawData();
    }

    /**
     * Validates the action's schema and returns the validated data, without running the
     * action itself. A nested action that consumes the data of the action it was mounted
     * from uses this rather than the unvalidated data from `getRawData()`.
     *
     * Throws a `ValidationException` when the schema is invalid.
     *
     * @return array<string, mixed>
     */
    public function getValidatedData(): array
    {
        if (($nestingIndex = $this->getMountedDataNestingIndex()) === null) {
            return [];
        }

        $data = [];

        if (($actionComponent = $this->getSchemaComponent()) instanceof ExposesStateToActionData) {
            foreach ($actionComponent->getChildSchemas() as $actionComponentChildSchema) {
                $data = [
                    ...$data,
                    ...$actionComponentChildSchema->getState(shouldCallHooksBefore: false),
                ];
            }
        }

        $schema = $this->getLivewire()->getSchema("mountedActionSchema{$nestingIndex}");

        if (! $schema) {
            return $data;
        }

        return [
            ...$data,
            ...$schema->getState(shouldCallHooksBefore: false),
        ];
    }

    /**
     * Fills the action's mounted schema data. Only state paths belonging to fields in the
     * action's schemas are filled.
     *
     * @param  array<string, mixed>  $data
     */
    public function fillData(array $data): static
    {
        if (($nestingIndex = $this->getMountedDataNestingIndex()) === null) {
            return $this;
        }

        if (($actionComponent = $this->getSchemaComponent()) instanceof ExposesStateToActionData) {
            foreach ($actionComponent->getChildSchemas() as $actionComponentChildSchema) {
                $this->fillSchemaData($actionComponentChildSchema, $data);
            }
        }

        if ($schema = $this->getLivewire()->getSchema("mountedActionSchema{$nestingIndex}")) {
            $this->fillSchemaData($schema, $data);
        }

        return $this;
    }

    /**
     * @param  array<string, mixed>  $data
     */
    protected function fillSchemaData(Schema $schema, array $data): void
    {
        $schemaStatePath = $schema->getStatePath();
        $statePaths = [];

        foreach ($schema->getFlatFields(withHidden: true) as $field) {
            $fieldStatePath = $field->getStatePath();

            if (filled($schemaStatePath) && str($fieldStatePath)->startsWith("{$schemaStatePath}.")) {
                $fieldStatePath = (string) str($fieldStatePath)->after("{$schemaStatePath}.");
            }

            if ((! array_key_exists($fieldStatePath, $data)) && (! Arr::has($data, $fieldStatePath))) {
                continue;
            }

            $statePaths[] = $fieldStatePath;
        }

        if (blank($statePaths)) {
            return;
        }

        $schema->fillPartially($data, $statePaths, shouldLoadStateFromRelationships: false);
    }

    protected function getMountedDataNestingIndex(): ?int
    {
        $nestingIndex = $this->getNestingIndex();

        if (
            ($nestingIndex === null) ||
            ($this->getLivewire()->getMountedAction($nestingIndex) !== $this)
        ) {
            return null;
        }

        return $nestingIndex;
    }
}
