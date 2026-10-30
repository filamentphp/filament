<?php

// Load only in the baseline subprocess, before the real product traits autoload.

namespace Filament\Actions\Concerns {
    use Filament\Actions\Action;
    use Filament\Schemas\Schema;

    trait InteractsWithActions
    {
        public function mountedActionHasForm(?Action $mountedAction = null): bool
        {
            return $this->mountedActionHasSchema($mountedAction);
        }

        protected function getMountedActionForm(?int $actionNestingIndex = null, ?Action $mountedAction = null): ?Schema
        {
            return $this->getMountedActionSchema($actionNestingIndex, $mountedAction);
        }
    }
}

namespace Filament\Tables\Concerns {
    trait HasColumnManager
    {
        public function updatedToggledTableColumns(): void
        {
            $this->applyTableColumnManager();
        }

        protected function getToggledTableColumnsSessionKey(): string
        {
            return $this->getTableColumnsSessionKey();
        }
    }

    trait CanGroupRecords
    {
        public function updatedTableGroupColumn(): void
        {
            $this->updatedTableGrouping();
        }
    }
}
