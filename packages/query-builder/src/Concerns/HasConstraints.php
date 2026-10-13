<?php

namespace Filament\QueryBuilder\Concerns;

use Filament\QueryBuilder\Constraints\Constraint;

trait HasConstraints
{
    /** @var array<string, Constraint> */
    protected array $constraints = [];

    /** @var array<string, array<string, true>> */
    protected array $constraintGroups = [];

    /**
     * @param  array<Constraint | array<Constraint>>  $constraints
     */
    public function constraints(array $constraints): static
    {
        foreach ($constraints as $group => $groupConstraints) {
            $hasGroup = is_string($group) && is_array($groupConstraints);

            if ($hasGroup) {
                $this->constraintGroups[$group] ??= [];
            }

            foreach (is_array($groupConstraints) ? $groupConstraints : [$groupConstraints] as $constraint) {
                $name = $constraint->getName();
                $this->constraints[$name] = $constraint;

                foreach (array_keys($this->constraintGroups) as $registeredGroup) {
                    unset($this->constraintGroups[$registeredGroup][$name]);
                }

                if ($hasGroup) {
                    $this->constraintGroups[$group][$name] = true;
                }
            }
        }

        return $this;
    }

    /**
     * @return array<string, Constraint>
     */
    public function getConstraints(): array
    {
        return $this->constraints;
    }

    /**
     * @return array<Constraint | array<Constraint>>
     */
    public function getGroupedConstraints(): array
    {
        $constraints = $this->getConstraints();
        $groups = [];

        foreach ($this->constraintGroups as $group => $names) {
            foreach (array_keys($names) as $name) {
                $groups[$group][] = $constraints[$name];
                unset($constraints[$name]);
            }
        }

        return [...array_values($constraints), ...$groups];
    }
}
