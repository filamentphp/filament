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

it('keeps repeated `callHook()` invocations live across instances and hook names', function (): void {
    $first = new HookCaller;
    $second = new HookCaller;

    $first->callHookByName('afterSave');
    $first->callHookByName('beforeCreate');
    $first->callHookByName('afterSave');
    $second->callHookByName('beforeSave');

    expect($first->lifecycleHookInvocations)->toBe([
        'afterSave',
        'afterSaveRecordsParentLifecycleHooks',
        'afterSaveRecordsLifecycleHooks',
        'afterSaveRecordsNestedLifecycleHooks',
        'beforeCreateRecordsLifecycleHooks',
        'afterSave',
        'afterSaveRecordsParentLifecycleHooks',
        'afterSaveRecordsLifecycleHooks',
        'afterSaveRecordsNestedLifecycleHooks',
    ])->and($second->lifecycleHookInvocations)->toBe([
        'beforeSaveRecordsDuplicateLifecycleHooks',
    ]);
});

it('isolates trait discovery by concrete class and keeps overrides live in `callHook()`', function (): void {
    $parent = new ParentHookCaller;
    $child = new HookCaller;
    $overridden = new class extends HookCaller
    {
        protected function afterSaveRecordsNestedLifecycleHooks(): void
        {
            $this->lifecycleHookInvocations[] = 'overridden';
        }
    };

    $child->callHookByName('afterSave');
    $parent->callHookByName('afterSave');
    $overridden->callHookByName('afterSave');

    expect($parent->lifecycleHookInvocations)->toBe(['afterSaveRecordsParentLifecycleHooks'])
        ->and($overridden->lifecycleHookInvocations)->toBe([
            'afterSave',
            'afterSaveRecordsParentLifecycleHooks',
            'afterSaveRecordsLifecycleHooks',
            'overridden',
        ]);
});

it('does not retain hook caller instances after caching trait discovery in `callHook()`', function (): void {
    $caller = new HookCaller;
    $reference = \WeakReference::create($caller);
    $caller->callHookByName('missing');
    unset($caller);

    expect($reference->get())->toBeNull();
});

it('propagates hook exceptions without suppressing later `callHook()` attempts', function (): void {
    $caller = new class extends HookCaller
    {
        protected function beforeCreateRecordsLifecycleHooks(): void
        {
            throw new \RuntimeException('lifecycle failure');
        }
    };

    expect(fn () => $caller->callHookByName('beforeCreate'))->toThrow(\RuntimeException::class, 'lifecycle failure')
        ->and(fn () => $caller->callHookByName('beforeCreate'))->toThrow(\RuntimeException::class, 'lifecycle failure');
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
