<?php

namespace Filament\Tables\Table\Concerns;

use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkAction;
use Filament\Support\Enums\Alignment;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;

trait CanRenderRecordActions
{
    /**
     * @var array{actionsByRecordKey: array<string, array<Action | ActionGroup>>, hasVisibleActions: bool} | null
     */
    protected ?array $cachedRenderRecordActionScan = null;

    /**
     * @internal Never call or override this method. It is not part of the public API and may change in minor and patch releases.
     *
     * @return array{actionsByRecordKey: array<string, array<Action | ActionGroup>>, hasVisibleActions: bool}
     */
    protected function getRenderRecordActionScan(): array
    {
        if ($this->cachedRenderRecordActionScan !== null) {
            return $this->cachedRenderRecordActionScan;
        }

        $actionsByRecordKey = [];

        if ($this->isLoaded() && (! $this->isReordering())) {
            // Retain the hidden prefix and first visible clones for the row loop.
            foreach ($this->getRecords() as $record) {
                $actionsByRecordKey[$this->getRecordKey($record)] = $actions = $this->getVisibleRenderRecordActions($record);

                if ($actions !== []) {
                    return $this->cachedRenderRecordActionScan = ['actionsByRecordKey' => $actionsByRecordKey, 'hasVisibleActions' => true];
                }
            }
        }

        return $this->cachedRenderRecordActionScan = ['actionsByRecordKey' => $actionsByRecordKey, 'hasVisibleActions' => false];
    }

    /**
     * @internal Never call or override this method. It is not part of the public API and may change in minor and patch releases.
     */
    protected function hasRenderRecordActions(): bool
    {
        return $this->getRenderRecordActionScan()['hasVisibleActions'];
    }

    /**
     * @internal Never call or override this method. It is not part of the public API and may change in minor and patch releases.
     *
     * @param  Model | array<string, mixed>  $record
     * @return array<Action | ActionGroup>
     */
    protected function getRenderRecordActions(Model | array $record, string $recordKey): array
    {
        $scan = $this->getRenderRecordActionScan();

        return $scan['actionsByRecordKey'][$recordKey]
            ?? ($scan['hasVisibleActions'] ? $this->getVisibleRenderRecordActions($record) : []);
    }

    /**
     * @internal Never call or override this method. It is not part of the public API and may change in minor and patch releases.
     *
     * @param  Model | array<string, mixed>  $record
     * @return array<Action | ActionGroup>
     */
    protected function getVisibleRenderRecordActions(Model | array $record): array
    {
        $actions = [];

        foreach ($this->getRecordActions() as $action) {
            $action = $action->getClone();

            if (! $action instanceof BulkAction) {
                $action->record($record);
            }

            if ($action->isHidden()) {
                continue;
            }

            $actions[] = $action;
        }

        return $actions;
    }

    /**
     * @internal Never call or override this method. It is not part of the public API and may change in minor and patch releases.
     */
    protected function getRenderRecordActionsAlignment(): Alignment | string | null
    {
        $alignment = $this->getRecordActionsAlignment();

        return filled($alignment) ? (Alignment::tryFrom($alignment) ?? $alignment) : null;
    }

    /**
     * @internal Never call or override this method. It is not part of the public API and may change in minor and patch releases.
     *
     * @param  array<Action | ActionGroup>  $recordActions
     */
    protected function generateRecordActionsHtml(array $recordActions): string
    {
        ob_start(); ?>
        <div
            class="<?= Arr::toCssClasses([
                'fi-ta-actions',
                match ($recordActionsAlignment = $this->getRenderRecordActionsAlignment()) {
                    Alignment::Center => 'fi-align-center',
                    Alignment::Start, Alignment::Left => 'fi-align-start',
                    Alignment::Between, Alignment::Justify => 'fi-align-between',
                    Alignment::End, Alignment::Right => '',
                    default => is_string($recordActionsAlignment) ? $recordActionsAlignment : '',
                },
            ]); ?>"
        >
            <?php foreach ($this->iterateRenderLoop($recordActions) as $action) { ?>
                <?= e($action); ?>
            <?php } ?>
        </div>
        <?php return ob_get_clean();
    }
}
