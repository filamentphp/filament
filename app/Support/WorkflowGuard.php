<?php

namespace App\Support;

class WorkflowGuard
{
    protected static bool $isWorkflowAction = false;

    /**
     * Run a callback within a workflow context.
     *
     * @param callable $callback
     * @return mixed
     */
    public static function runAsWorkflow(callable $callback): mixed
    {
        self::$isWorkflowAction = true;

        try {
            return $callback();
        } finally {
            self::$isWorkflowAction = false;
        }
    }

    /**
     * Check if the current execution is within a workflow context.
     *
     * @return bool
     */
    public static function isWorkflowAction(): bool
    {
        return self::$isWorkflowAction;
    }
}
