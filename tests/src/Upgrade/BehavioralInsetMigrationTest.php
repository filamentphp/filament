<?php

namespace Filament\Tests\Upgrade;

use Error;
use Filament\Forms\Components\Builder;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\TextInput;
use Filament\Tests\TestCase;
use RuntimeException;
use stdClass;
use TypeError;
use WeakReference;

use function Filament\Tests\Upgrade\Fixtures\BehavioralInset\arrayAccessContexts;
use function Filament\Tests\Upgrade\Fixtures\BehavioralInset\baseComponent;
use function Filament\Tests\Upgrade\Fixtures\BehavioralInset\baseField;
use function Filament\Tests\Upgrade\Fixtures\BehavioralInset\computed;
use function Filament\Tests\Upgrade\Fixtures\BehavioralInset\consecutive;
use function Filament\Tests\Upgrade\Fixtures\BehavioralInset\contexts;
use function Filament\Tests\Upgrade\Fixtures\BehavioralInset\customArgument;
use function Filament\Tests\Upgrade\Fixtures\BehavioralInset\factory;
use function Filament\Tests\Upgrade\Fixtures\BehavioralInset\inherited;
use function Filament\Tests\Upgrade\Fixtures\BehavioralInset\inlineNullable;
use function Filament\Tests\Upgrade\Fixtures\BehavioralInset\magicContexts;
use function Filament\Tests\Upgrade\Fixtures\BehavioralInset\nullable;
use function Filament\Tests\Upgrade\Fixtures\BehavioralInset\nullableAssignment;
use function Filament\Tests\Upgrade\Fixtures\BehavioralInset\nullableResult;
use function Filament\Tests\Upgrade\Fixtures\BehavioralInset\precedingNullable;
use function Filament\Tests\Upgrade\Fixtures\BehavioralInset\stored;
use function Filament\Tests\Upgrade\Fixtures\BehavioralInset\throwing;

class BehavioralInsetMigrationTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        if (! function_exists('Filament\\Tests\\Upgrade\\Fixtures\\BehavioralInset\\stored')) {
            [, $migrated] = explode("-----\n", file_get_contents(__DIR__ . '/Fixtures/behavioral_configuration_inset.php.inc'), 2);
            eval('?>' . $migrated);
        }
    }

    public function test_preserves_identity_argument_order_and_closure_lifetime(): void
    {
        foreach ([Builder::make('blocks'), Repeater::make('items')] as $component) {
            foreach ([true, false] as $condition) {
                $events = [];
                $this->assertSame($component, stored($component, $condition));
                $this->assertSame($component, computed($events, $component, $condition));
                $this->assertSame(['receiver', 'argument', 'label'], $events);
                $this->assertSame('configured', $component->getLabel());
            }

            $sentinel = new stdClass;
            $reference = WeakReference::create($sentinel);
            $condition = static function () use ($sentinel): bool {
                $sentinel->invoked = true;

                throw new RuntimeException('The supplied closure must never execute.');
            };
            $closureReference = WeakReference::create($condition);
            $events = [];

            $this->assertSame($component, computed($events, $component, $condition));
            $this->assertSame(['receiver', 'argument', 'label'], $events);
            unset($condition, $sentinel);
            $this->assertNull($closureReference->get());
            $this->assertNull($reference->get());
        }
    }

    public function test_preserves_nullsafe_short_circuiting_and_inline_capture_scope(): void
    {
        foreach ([null, Builder::make('blocks'), Repeater::make('items')] as $component) {
            $events = [];
            $condition = static function (): bool {
                throw new RuntimeException('The supplied closure must never execute.');
            };
            $this->assertSame($component, nullable($events, $component, $condition));
            $this->assertSame($component ? ['receiver', 'argument', 'label', 'existing'] : ['receiver', 'existing'], $events);
        }

        foreach ([null, Builder::make('inline')] as $component) {
            $events = [];
            $sentinel = new stdClass;
            $reference = WeakReference::create($sentinel);
            $this->assertSame($component, inlineNullable($events, $component, $sentinel));
            $this->assertSame($component ? ['receiver', 'label'] : ['receiver'], $events);
            unset($sentinel);
            $this->assertNull($reference->get());

            $condition = true;
            $this->assertSame($component, nullableAssignment($component, $condition));
            $this->assertSame($component === null, $condition);

            $events = [];
            $this->assertSame($component, consecutive($component, $events));
            $this->assertSame($component ? ['argument', 'argument', 'label'] : [], $events);
        }

        $events = [];
        $this->assertNull(precedingNullable(null, $events));
        $this->assertSame([], $events);
        $component = Builder::make('holder');
        $this->assertSame($component, precedingNullable(new Fixtures\BehavioralInset\Holder($component), $events));
        $this->assertSame(['argument', 'label'], $events);
    }

    public function test_preserves_genuine_custom_dispatch_and_its_returned_identity(): void
    {
        $component = Fixtures\BehavioralInset\CustomBuilder::make('custom');
        $replacement = Fixtures\BehavioralInset\CustomBuilder::make('replacement');
        $component->replacement = $replacement;
        $condition = static fn (): bool => false;
        $events = [];

        $this->assertSame($replacement, computed($events, $component, $condition));
        $this->assertSame([$condition], $component->conditions);
        $this->assertSame('configured', $replacement->getLabel());
        $this->assertSame(['receiver', 'argument', 'label'], $events);

        $events = [];
        $this->assertSame($replacement, nullable($events, $component, false));
        $this->assertSame([$condition, false], $component->conditions);
        $this->assertSame(['receiver', 'argument', 'label', 'existing'], $events);

        $this->app->bind(Builder::class, static fn (): Builder => $component);
        $this->assertSame($replacement, factory());
        $this->assertSame([$condition, false, 'custom-default'], $component->conditions);
        $this->assertSame([1, 1, 0], $component->argumentCounts);
        $this->assertSame($replacement, customArgument($component, ['custom']));
        $this->assertSame(['custom'], $component->conditions[3]);

        $inherited = Fixtures\BehavioralInset\InheritedBuilder::make('inherited');
        $this->assertSame($inherited, inherited($inherited));

        $cloning = Fixtures\BehavioralInset\CloningRepeater::make('clone')->label('original');
        $clone = stored($cloning, true);
        $this->assertNotSame($cloning, $clone);
        $this->assertSame('original', $cloning->getLabel());
        $this->assertSame('configured', $clone->getLabel());
    }

    public function test_base_typed_dispatch_preserves_other_components_and_argument_validation(): void
    {
        foreach ([Builder::make('blocks'), Repeater::make('items')] as $component) {
            $this->assertSame($component, baseField($component, false));
            $this->assertSame($component, baseComponent($component));

            try {
                baseField($component, []);
                $this->fail('The removed method argument contract must still be validated.');
            } catch (TypeError $exception) {
                $this->assertStringContainsString('bool', $exception->getMessage());
            }
        }

        TextInput::macro('inset', function (mixed $condition): array {
            return [$this, $condition];
        });

        try {
            $input = TextInput::make('custom-macro');
            $this->assertSame([$input, ['custom']], baseField($input, ['custom']));
        } finally {
            TextInput::flushMacros();
        }
    }

    public function test_does_not_hide_an_ordinary_call_on_a_nullable_method_result(): void
    {
        $events = [];
        $this->assertNull(nullableResult(null, $events));
        $this->assertSame([], $events);

        try {
            nullableResult(new Fixtures\BehavioralInset\Holder(Builder::make('nullable-result')), $events);
            $this->fail('A non-null holder returning null must not short-circuit an ordinary call.');
        } catch (Error $exception) {
            $this->assertSame('Call to a member function inset() on null', $exception->getMessage());
        }

        $this->assertSame([], $events);
    }

    public function test_builtin_inset_methods_are_absent(): void
    {
        $this->assertFalse(method_exists(Builder::class, 'inset'));
        $this->assertFalse(method_exists(Repeater::class, 'inset'));
    }

    public function test_preserves_lazy_consuming_contexts_and_receiver_warnings(): void
    {
        $component = Fixtures\BehavioralInset\ContextBuilder::make('contexts');
        $events = [];
        $this->assertSame([true, false, 'present', false], contexts($component, 'present', $events));
        $this->assertSame(['argument', 'argument', 'argument'], $events);

        $events = [];
        $this->assertSame([false, true, 'configured', false], contexts(null, 'present', $events));
        $this->assertSame(['label'], $events);

        $events = [];
        $warnings = [];
        set_error_handler(static function (int $severity, string $message) use (&$warnings): bool {
            $warnings[] = [$severity, $message];

            return true;
        });

        try {
            $result = contexts($component, 'missing', $events);
        } finally {
            restore_error_handler();
        }

        $this->assertSame([false, true, 'configured', false], $result);
        $this->assertSame(['label'], $events);
        $this->assertSame(array_fill(0, 3, [E_WARNING, 'Undefined array key "missing"']), $warnings);
    }

    public function test_preserves_magic_receiver_reads(): void
    {
        foreach ([Fixtures\BehavioralInset\ContextBuilder::make('lazy'), null] as $component) {
            $holder = new Fixtures\BehavioralInset\LazyHolder($component);
            $events = [];
            $this->assertSame($component ? [true, false, 'present'] : [false, true, 'configured'], magicContexts($holder, $events));
            $this->assertSame(['get', 'get', 'get'], $holder->events);
            $this->assertSame($component ? ['argument', 'argument', 'argument'] : ['label'], $events);
        }
    }

    public function test_preserves_array_access_receiver_reads(): void
    {
        foreach ([Fixtures\BehavioralInset\ContextBuilder::make('lazy'), null] as $component) {
            $components = new Fixtures\BehavioralInset\LazyComponents($component);
            $events = [];
            $this->assertSame($component ? [true, false, 'present'] : [false, true, 'configured'], arrayAccessContexts($components, $events));
            $this->assertSame(['get', 'get', 'get'], $components->events);
            $this->assertSame($component ? ['argument', 'argument', 'argument'] : ['label'], $events);
        }
    }

    public function test_argument_failure_stops_fluent_configuration(): void
    {
        $component = Builder::make('failure')->label('original');
        $events = [];

        try {
            throwing($events, $component);
            $this->fail('The argument exception should propagate.');
        } catch (RuntimeException $exception) {
            $this->assertSame('argument failed', $exception->getMessage());
        }

        $this->assertSame(['receiver', 'argument'], $events);
        $this->assertSame('original', $component->getLabel());
    }
}
