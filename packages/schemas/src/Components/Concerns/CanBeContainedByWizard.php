<?php

namespace Filament\Schemas\Components\Concerns;

use Filament\Schemas\Components\Wizard;

trait CanBeContainedByWizard
{
    protected Wizard | bool | null $cachedParentWizard = null;

    public function getParentWizard(): ?Wizard
    {
        if ($this->cachedParentWizard !== null) {
            return $this->cachedParentWizard ?: null;
        }

        $parentComponent = $this->getContainer()->getParentComponent();

        if (! $parentComponent) {
            $this->cachedParentWizard = false;
        } elseif ($parentComponent instanceof Wizard) {
            $this->cachedParentWizard = $parentComponent;
        } else {
            $this->cachedParentWizard = $parentComponent->getParentWizard() ?? false;
        }

        return $this->cachedParentWizard ?: null;
    }

    public function getActiveWizardStepIndex(): ?int
    {
        return $this->getParentWizard()?->getCurrentStepIndex();
    }
}
