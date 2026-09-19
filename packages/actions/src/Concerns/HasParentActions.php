<?php

namespace Filament\Actions\Concerns;

use Closure;
use Filament\Schemas\Components\Contracts\ExposesStateToActionData;
use Filament\Schemas\Schema;
use Illuminate\Support\Arr;
use LogicException;

trait HasParentActions
{
    protected bool | string | Closure | null $cancelParentActions = null;

    protected bool | string | Closure | null $cancelParentActionsOnClose = null;

    protected bool | Closure $shouldOverlayParentActions = false;

    public function cancelParentActions(bool | string | Closure | null $toAction = true): static
    {
        $this->cancelParentActions = $toAction;

        return $this;
    }

    public function cancelParentActionsOnClose(bool | string | Closure | null $toAction = true): static
    {
        $this->cancelParentActionsOnClose = $toAction;

        return $this;
    }

    public function overlayParentActions(bool | Closure $condition = true): static
    {
        $this->shouldOverlayParentActions = $condition;

        return $this;
    }

    public function shouldCancelAllParentActions(): bool
    {
        return $this->evaluate($this->cancelParentActions) === true;
    }

    public function getParentActionToCancelTo(): ?string
    {
        $toAction = $this->evaluate($this->cancelParentActions);

        return is_string($toAction) ? $toAction : null;
    }

    public function getParentActionsToCancelOnClose(): bool | string
    {
        $toAction = $this->evaluate($this->cancelParentActionsOnClose);

        return is_string($toAction) ? $toAction : ($toAction === true);
    }

    public function shouldCancelParentActionsOnClose(): bool
    {
        return $this->getParentActionsToCancelOnClose() !== false;
    }

    public function shouldCancelAllParentActionsOnClose(): bool
    {
        return $this->getParentActionsToCancelOnClose() === true;
    }

    public function getParentActionToCancelToOnClose(): ?string
    {
        $toAction = $this->getParentActionsToCancelOnClose();

        return is_string($toAction) ? $toAction : null;
    }

    public function shouldOverlayParentActions(): bool
    {
        return (bool) $this->evaluate($this->shouldOverlayParentActions);
    }

    /**
     * Validates the schema of the action that this one was mounted from, and returns the
     * validated data, without running it.
     *
     * The parent is taken from the mounted stack rather than `getParentAction()`, which is
     * only set for actions registered on a modal, and not for actions registered on a
     * component inside one.
     *
     * @return array<string, mixed>
     */
    public function getValidatedParentActionData(): array
    {
        return $this->getLivewire()
            ->getMountedAction($this->getParentActionNestingIndex())
            ?->getValidatedData() ?? [];
    }

    /**
     * Writes into the schema data of the action that this one was mounted from. The action
     * being written to validates it with its own rules when it is submitted.
     *
     * The parent is taken from the mounted stack rather than `getParentAction()`, which is
     * only set for actions registered on a modal, and not for actions registered on a
     * component inside one.
     *
     * @param  array<string, mixed>  $data
     */
    public function fillParentActionData(array $data): static
    {
        $parentActionNestingIndex = $this->getParentActionNestingIndex();
        $livewire = $this->getLivewire();
        $parentAction = $livewire->getMountedAction($parentActionNestingIndex);

        if (($parentActionComponent = $parentAction?->getSchemaComponent()) instanceof ExposesStateToActionData) {
            foreach ($parentActionComponent->getChildSchemas() as $parentActionComponentChildSchema) {
                $this->fillSchemaData($parentActionComponentChildSchema, $data);
            }
        }

        if ($parentActionSchema = $livewire->getSchema("mountedActionSchema{$parentActionNestingIndex}")) {
            $this->fillSchemaData($parentActionSchema, $data);
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

        $schema->fillPartially($data, $statePaths);
    }

    protected function getParentActionNestingIndex(): int
    {
        $nestingIndex = $this->getNestingIndex();

        if (blank($nestingIndex) || ($nestingIndex < 1)) {
            throw new LogicException("The action [{$this->getName()}] tried to use the data of a parent action, but it was not mounted from one.");
        }

        return $nestingIndex - 1;
    }
}
