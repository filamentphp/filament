<?php

namespace Filament\Actions\Enums;

/**
 * The stages that an action passes through when it is called, in order. When an
 * action is paused, the stage that it paused in is recorded, so that resuming it
 * does not repeat the stages that already completed.
 */
enum ActionCallStage: int
{
    case RateLimit = 1;

    case Validation = 2;

    case PauseConditions = 3;

    case Before = 4;

    case Call = 5;

    public function isAfter(?self $stage): bool
    {
        if (! $stage) {
            return true;
        }

        return $this->value > $stage->value;
    }
}
