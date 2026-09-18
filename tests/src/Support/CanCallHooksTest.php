<?php

namespace Filament\Tests\Support\CanCallHooks\Concerns;

trait RecordsParentLifecycleHooks
{
    protected function afterSaveRecordsParentLifecycleHooks(): void
    {
        $this->lifecycleHookInvocations[] = 'afterSaveRecordsParentLifecycleHooks';
    }
}

trait RecordsNestedLifecycleHooks
{
    protected function afterSaveRecordsNestedLifecycleHooks(): void
    {
        $this->lifecycleHookInvocations[] = 'afterSaveRecordsNestedLifecycleHooks';
    }
}

trait RecordsLifecycleHooks
{
    use RecordsNestedLifecycleHooks;

    protected function beforeCreateRecordsLifecycleHooks(): void
    {
        $this->lifecycleHookInvocations[] = 'beforeCreateRecordsLifecycleHooks';
    }

    protected function afterSaveRecordsLifecycleHooks(): void
    {
        $this->lifecycleHookInvocations[] = 'afterSaveRecordsLifecycleHooks';
    }
}

namespace Filament\Tests\Support\CanCallHooks\First;

trait RecordsDuplicateLifecycleHooks
{
    protected function beforeSaveRecordsDuplicateLifecycleHooks(): void
    {
        $this->lifecycleHookInvocations[] = 'beforeSaveRecordsDuplicateLifecycleHooks';
    }
}

namespace Filament\Tests\Support\CanCallHooks\Second;

trait RecordsDuplicateLifecycleHooks {}

namespace Filament\Tests\Support;

use Filament\Support\Concerns\CanCallHooks;
use Filament\Tests\Support\CanCallHooks\Concerns\RecordsLifecycleHooks;
use Filament\Tests\Support\CanCallHooks\Concerns\RecordsParentLifecycleHooks;
use Filament\Tests\Support\CanCallHooks\First\RecordsDuplicateLifecycleHooks as FirstRecordsDuplicateLifecycleHooks;
use Filament\Tests\Support\CanCallHooks\Second\RecordsDuplicateLifecycleHooks as SecondRecordsDuplicateLifecycleHooks;
use Filament\Tests\TestCase;

uses(TestCase::class);

it('calls class and recursive trait hooks in order with `callHook()`', function (): void {
    $caller = new HookCaller;

    $caller->callHookByName('afterSave');

    expect($caller->lifecycleHookInvocations)->toBe([
        'afterSave',
        'afterSaveRecordsParentLifecycleHooks',
        'afterSaveRecordsLifecycleHooks',
        'afterSaveRecordsNestedLifecycleHooks',
    ]);
});

it('calls trait hooks when the class hook does not exist with `callHook()`', function (): void {
    $caller = new HookCaller;

    $caller->callHookByName('beforeCreate');

    expect($caller->lifecycleHookInvocations)->toBe([
        'beforeCreateRecordsLifecycleHooks',
    ]);
});

it('calls duplicate trait hook methods once with `callHook()`', function (): void {
    $caller = new HookCaller;

    $caller->callHookByName('beforeSave');

    expect($caller->lifecycleHookInvocations)->toBe([
        'beforeSaveRecordsDuplicateLifecycleHooks',
    ]);
});

class ParentHookCaller
{
    use CanCallHooks;
    use RecordsParentLifecycleHooks;

    /**
     * @var array<string>
     */
    public array $lifecycleHookInvocations = [];

    public function callHookByName(string $hook): void
    {
        $this->callHook($hook);
    }
}

class HookCaller extends ParentHookCaller
{
    use FirstRecordsDuplicateLifecycleHooks;
    use RecordsLifecycleHooks;
    use SecondRecordsDuplicateLifecycleHooks;

    protected function afterSave(): void
    {
        $this->lifecycleHookInvocations[] = 'afterSave';
    }
}
