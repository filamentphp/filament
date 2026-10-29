<?php

namespace Filament\Tests\Upgrade\Fixtures\BehavioralInset;

use ArrayAccess;
use Closure;
use Filament\Forms\Components\Builder;
use Filament\Forms\Components\Field;
use Filament\Forms\Components\Repeater;
use Filament\Schemas\Components\Component;
use RuntimeException;

function stored(Builder | Repeater $component, bool | Closure $condition): Builder | Repeater
{
    return $component->inset(condition: $condition)->label('configured');
}

function computed(array &$events, Builder | Repeater $component, bool | Closure $condition): Builder | Repeater
{
    return receiver($events, $component)->inset(argument($events, $condition))->label(label($events));
}

function nullable(array &$events, Builder | Repeater | null $component, bool | Closure $condition): Builder | Repeater | null
{
    $filamentInsetComponent = 'existing';
    $result = nullableReceiver($events, $component)?->inset(argument($events, $condition))->label(label($events));
    $events[] = $filamentInsetComponent;

    return $result;
}

function inlineNullable(array &$events, ?Builder $component, object $sentinel): ?Builder
{
    return nullableBuilder($events, $component)?->inset(function () use (&$events, $sentinel): bool {
        $events[] = 'invoked';

        return true;
    })->label(label($events));
}

function precedingNullable(?Holder $holder, array &$events): ?Builder
{
    return $holder?->component()->inset(argument($events, true))->label(label($events));
}

function consecutive(?Builder $component, array &$events): ?Builder
{
    return $component?->inset(argument($events, true))->label('intermediate')->inset(argument($events, false))->label(label($events));
}

function nullableAssignment(?Builder $component, bool &$condition): ?Builder
{
    return $component?->inset($condition = false);
}

function throwing(array &$events, Builder $component): Builder
{
    return nullableBuilder($events, $component)?->inset(failure($events))->label(label($events));
}

function factory(): Builder
{
    return Builder::make('bound')->inset();
}

function inherited(InheritedBuilder $component): InheritedBuilder
{
    return $component->inset(false);
}

function baseField(Field $component, mixed $condition): mixed
{
    return $component->inset($condition);
}

function baseComponent(Component $component): mixed
{
    return $component->inset();
}

function customArgument(Builder $component, mixed $condition): Builder
{
    return $component->inset($condition);
}

function nullableResult(?Holder $holder, array &$events): ?Builder
{
    return $holder?->nullableComponent()->inset(argument($events, true));
}

function contexts(?ContextBuilder $component, string $key, array &$events): array
{
    $components = ['present' => $component];

    return [
        isset($components[$key]?->inset(argument($events, true))->payload['value']),
        empty($components[$key]?->inset(argument($events, true))->payload['value']),
        $components[$key]?->inset(argument($events, true))->payload['value'] ?? label($events),
        isset($missing, $component?->inset(argument($events, true))->payload),
    ];
}

function magicContexts(LazyHolder $holder, array &$events): array
{
    return [
        isset($holder->component?->inset(argument($events, true))->payload['value']),
        empty($holder->component?->inset(argument($events, true))->payload['value']),
        $holder->component?->inset(argument($events, true))->payload['value'] ?? label($events),
    ];
}

function arrayAccessContexts(LazyComponents $components, array &$events): array
{
    return [
        isset($components['component']?->inset(argument($events, true))->payload['value']),
        empty($components['component']?->inset(argument($events, true))->payload['value']),
        $components['component']?->inset(argument($events, true))->payload['value'] ?? label($events),
    ];
}

function receiver(array &$events, Builder | Repeater $component): Builder | Repeater
{
    $events[] = 'receiver';

    return $component;
}

function nullableReceiver(array &$events, Builder | Repeater | null $component): Builder | Repeater | null
{
    $events[] = 'receiver';

    return $component;
}

function nullableBuilder(array &$events, ?Builder $component): ?Builder
{
    $events[] = 'receiver';

    return $component;
}

function argument(array &$events, bool | Closure $condition): bool | Closure
{
    $events[] = 'argument';

    return $condition;
}

function label(array &$events): string
{
    $events[] = 'label';

    return 'configured';
}

function failure(array &$events): bool
{
    $events[] = 'argument';

    throw new RuntimeException('argument failed');
}

class Holder
{
    public function __construct(public Builder $builder) {}

    public function component(): Builder
    {
        return $this->builder;
    }

    public function nullableComponent(): ?Builder
    {
        return null;
    }
}

class InheritedBuilder extends Builder {}

class ContextBuilder extends Builder
{
    public array $payload = ['value' => 'present'];
}

class LazyHolder
{
    public ?ContextBuilder $component;

    public array $events = [];

    public function __construct(private ?ContextBuilder $value)
    {
        unset($this->component);
    }

    public function __get(string $name): ?ContextBuilder
    {
        $this->events[] = 'get';

        return $this->value;
    }

    public function __isset(string $name): bool
    {
        $this->events[] = 'isset';

        return false;
    }
}

/** @implements ArrayAccess<string, ContextBuilder | null> */
class LazyComponents implements ArrayAccess
{
    public array $events = [];

    public function __construct(private ?ContextBuilder $component) {}

    public function offsetExists(mixed $offset): bool
    {
        $this->events[] = 'exists';

        return false;
    }

    public function offsetGet(mixed $offset): ?ContextBuilder
    {
        $this->events[] = 'get';

        return $this->component;
    }

    public function offsetSet(mixed $offset, mixed $value): void {}

    public function offsetUnset(mixed $offset): void {}
}

class CustomBuilder extends Builder
{
    public array $conditions = [];

    public array $argumentCounts = [];

    public ?self $replacement = null;

    public function inset(mixed $condition = 'custom-default', mixed $optional = 'optional'): static
    {
        $this->conditions[] = $condition;
        $this->argumentCounts[] = func_num_args();

        return $this->replacement ?? $this;
    }
}

class CloningRepeater extends Repeater
{
    public function inset(bool | Closure $condition = true): static
    {
        return clone $this;
    }
}

class Unrelated
{
    public function inset(bool $condition = true): static
    {
        return $this;
    }
}

function manual(Builder $component, Unrelated $unrelated, array $arguments, string $method): void
{
    $component->inset(unknown: true);
    $component->inset(true, false);
    $component->inset(...$arguments);
    $component->inset(...);
    $component->{$method}(true);
    $unrelated->inset();
}
-----
<?php

namespace Filament\Tests\Upgrade\Fixtures\BehavioralInset;

use ArrayAccess;
use Closure;
use Filament\Forms\Components\Builder;
use Filament\Forms\Components\Field;
use Filament\Forms\Components\Repeater;
use Filament\Schemas\Components\Component;
use RuntimeException;

function stored(Builder | Repeater $component, bool | Closure $condition): Builder | Repeater
{
    return (static fn($component) => ($component instanceof Builder || $component instanceof Repeater) && !\method_exists($component, 'inset') ? static fn(bool|Closure $condition = true) => $component : $component->{'inset'}(...))($component)(condition: $condition)->label('configured');
}

function computed(array &$events, Builder | Repeater $component, bool | Closure $condition): Builder | Repeater
{
    return (static fn($component) => ($component instanceof Builder || $component instanceof Repeater) && !\method_exists($component, 'inset') ? static fn(bool|Closure $condition = true) => $component : $component->{'inset'}(...))(receiver($events, $component))(argument($events, $condition))->label(label($events));
}

function nullable(array &$events, Builder | Repeater | null $component, bool | Closure $condition): Builder | Repeater | null
{
    $filamentInsetComponent = 'existing';
    $result = ($filamentInsetComponent_ = nullableReceiver($events, $component)) === null ? null : (static fn($component) => ($component instanceof Builder || $component instanceof Repeater) && !\method_exists($component, 'inset') ? static fn(bool|Closure $condition = true) => $component : $component->{'inset'}(...))($filamentInsetComponent_)(argument($events, $condition))->label(label($events));
    $events[] = $filamentInsetComponent;

    return $result;
}

function inlineNullable(array &$events, ?Builder $component, object $sentinel): ?Builder
{
    return ($filamentInsetComponent__ = nullableBuilder($events, $component)) === null ? null : (static fn($component) => ($component instanceof Builder || $component instanceof Repeater) && !\method_exists($component, 'inset') ? static fn(bool|Closure $condition = true) => $component : $component->{'inset'}(...))($filamentInsetComponent__)(function () use (&$events, $sentinel): bool {
        $events[] = 'invoked';

        return true;
    })->label(label($events));
}

function precedingNullable(?Holder $holder, array &$events): ?Builder
{
    return ($filamentInsetComponent___ = $holder) === null ? null : (static fn($component) => ($component instanceof Builder || $component instanceof Repeater) && !\method_exists($component, 'inset') ? static fn(bool|Closure $condition = true) => $component : $component->{'inset'}(...))($filamentInsetComponent___?->component())(argument($events, true))->label(label($events));
}

function consecutive(?Builder $component, array &$events): ?Builder
{
    return ($filamentInsetComponent____ = $component) === null ? null : (static fn($component) => ($component instanceof Builder || $component instanceof Repeater) && !\method_exists($component, 'inset') ? static fn(bool|Closure $condition = true) => $component : $component->{'inset'}(...))((static fn($component) => ($component instanceof Builder || $component instanceof Repeater) && !\method_exists($component, 'inset') ? static fn(bool|Closure $condition = true) => $component : $component->{'inset'}(...))($filamentInsetComponent____)(argument($events, true))->label('intermediate'))(argument($events, false))->label(label($events));
}

function nullableAssignment(?Builder $component, bool &$condition): ?Builder
{
    return ($filamentInsetComponent_____ = $component) === null ? null : (static fn($component) => ($component instanceof Builder || $component instanceof Repeater) && !\method_exists($component, 'inset') ? static fn(bool|Closure $condition = true) => $component : $component->{'inset'}(...))($filamentInsetComponent_____)($condition = false);
}

function throwing(array &$events, Builder $component): Builder
{
    return ($filamentInsetComponent______ = nullableBuilder($events, $component)) === null ? null : (static fn($component) => ($component instanceof Builder || $component instanceof Repeater) && !\method_exists($component, 'inset') ? static fn(bool|Closure $condition = true) => $component : $component->{'inset'}(...))($filamentInsetComponent______)(failure($events))->label(label($events));
}

function factory(): Builder
{
    return (static fn($component) => ($component instanceof Builder || $component instanceof Repeater) && !\method_exists($component, 'inset') ? static fn(bool|Closure $condition = true) => $component : $component->{'inset'}(...))(Builder::make('bound'))();
}

function inherited(InheritedBuilder $component): InheritedBuilder
{
    return (static fn($component) => ($component instanceof Builder || $component instanceof Repeater) && !\method_exists($component, 'inset') ? static fn(bool|Closure $condition = true) => $component : $component->{'inset'}(...))($component)(false);
}

function baseField(Field $component, mixed $condition): mixed
{
    return (static fn($component) => ($component instanceof Builder || $component instanceof Repeater) && !\method_exists($component, 'inset') ? static fn(bool|Closure $condition = true) => $component : $component->{'inset'}(...))($component)($condition);
}

function baseComponent(Component $component): mixed
{
    return (static fn($component) => ($component instanceof Builder || $component instanceof Repeater) && !\method_exists($component, 'inset') ? static fn(bool|Closure $condition = true) => $component : $component->{'inset'}(...))($component)();
}

function customArgument(Builder $component, mixed $condition): Builder
{
    return (static fn($component) => ($component instanceof Builder || $component instanceof Repeater) && !\method_exists($component, 'inset') ? static fn(bool|Closure $condition = true) => $component : $component->{'inset'}(...))($component)($condition);
}

function nullableResult(?Holder $holder, array &$events): ?Builder
{
    return ($filamentInsetComponent_______ = $holder) === null ? null : (static fn($component) => ($component instanceof Builder || $component instanceof Repeater) && !\method_exists($component, 'inset') ? static fn(bool|Closure $condition = true) => $component : $component->{'inset'}(...))($filamentInsetComponent_______?->nullableComponent())(argument($events, true));
}

function contexts(?ContextBuilder $component, string $key, array &$events): array
{
    $components = ['present' => $component];

    return [
        ($filamentInsetComponent________ = $components[$key]) === null ? false : isset((static fn($component) => ($component instanceof Builder || $component instanceof Repeater) && !\method_exists($component, 'inset') ? static fn(bool|Closure $condition = true) => $component : $component->{'inset'}(...))($filamentInsetComponent________)(argument($events, true))->payload['value']),
        ($filamentInsetComponent_________ = $components[$key]) === null ? true : empty((static fn($component) => ($component instanceof Builder || $component instanceof Repeater) && !\method_exists($component, 'inset') ? static fn(bool|Closure $condition = true) => $component : $component->{'inset'}(...))($filamentInsetComponent_________)(argument($events, true))->payload['value']),
        ($filamentInsetComponent__________ = $components[$key]) === null ? null ?? label($events) : (static fn($component) => ($component instanceof Builder || $component instanceof Repeater) && !\method_exists($component, 'inset') ? static fn(bool|Closure $condition = true) => $component : $component->{'inset'}(...))($filamentInsetComponent__________)(argument($events, true))->payload['value'] ?? label($events),
        isset($missing) && (($filamentInsetComponent___________ = $component) === null ? false : isset((static fn($component) => ($component instanceof Builder || $component instanceof Repeater) && !\method_exists($component, 'inset') ? static fn(bool|Closure $condition = true) => $component : $component->{'inset'}(...))($filamentInsetComponent___________)(argument($events, true))->payload)),
    ];
}

function magicContexts(LazyHolder $holder, array &$events): array
{
    return [
        ($filamentInsetComponent____________ = $holder->component) === null ? false : isset((static fn($component) => ($component instanceof Builder || $component instanceof Repeater) && !\method_exists($component, 'inset') ? static fn(bool|Closure $condition = true) => $component : $component->{'inset'}(...))($filamentInsetComponent____________)(argument($events, true))->payload['value']),
        ($filamentInsetComponent_____________ = $holder->component) === null ? true : empty((static fn($component) => ($component instanceof Builder || $component instanceof Repeater) && !\method_exists($component, 'inset') ? static fn(bool|Closure $condition = true) => $component : $component->{'inset'}(...))($filamentInsetComponent_____________)(argument($events, true))->payload['value']),
        ($filamentInsetComponent______________ = $holder->component) === null ? null ?? label($events) : (static fn($component) => ($component instanceof Builder || $component instanceof Repeater) && !\method_exists($component, 'inset') ? static fn(bool|Closure $condition = true) => $component : $component->{'inset'}(...))($filamentInsetComponent______________)(argument($events, true))->payload['value'] ?? label($events),
    ];
}

function arrayAccessContexts(LazyComponents $components, array &$events): array
{
    return [
        ($filamentInsetComponent_______________ = $components['component']) === null ? false : isset((static fn($component) => ($component instanceof Builder || $component instanceof Repeater) && !\method_exists($component, 'inset') ? static fn(bool|Closure $condition = true) => $component : $component->{'inset'}(...))($filamentInsetComponent_______________)(argument($events, true))->payload['value']),
        ($filamentInsetComponent________________ = $components['component']) === null ? true : empty((static fn($component) => ($component instanceof Builder || $component instanceof Repeater) && !\method_exists($component, 'inset') ? static fn(bool|Closure $condition = true) => $component : $component->{'inset'}(...))($filamentInsetComponent________________)(argument($events, true))->payload['value']),
        ($filamentInsetComponent_________________ = $components['component']) === null ? null ?? label($events) : (static fn($component) => ($component instanceof Builder || $component instanceof Repeater) && !\method_exists($component, 'inset') ? static fn(bool|Closure $condition = true) => $component : $component->{'inset'}(...))($filamentInsetComponent_________________)(argument($events, true))->payload['value'] ?? label($events),
    ];
}

function receiver(array &$events, Builder | Repeater $component): Builder | Repeater
{
    $events[] = 'receiver';

    return $component;
}

function nullableReceiver(array &$events, Builder | Repeater | null $component): Builder | Repeater | null
{
    $events[] = 'receiver';

    return $component;
}

function nullableBuilder(array &$events, ?Builder $component): ?Builder
{
    $events[] = 'receiver';

    return $component;
}

function argument(array &$events, bool | Closure $condition): bool | Closure
{
    $events[] = 'argument';

    return $condition;
}

function label(array &$events): string
{
    $events[] = 'label';

    return 'configured';
}

function failure(array &$events): bool
{
    $events[] = 'argument';

    throw new RuntimeException('argument failed');
}

class Holder
{
    public function __construct(public Builder $builder) {}

    public function component(): Builder
    {
        return $this->builder;
    }

    public function nullableComponent(): ?Builder
    {
        return null;
    }
}

class InheritedBuilder extends Builder {}

class ContextBuilder extends Builder
{
    public array $payload = ['value' => 'present'];
}

class LazyHolder
{
    public ?ContextBuilder $component;

    public array $events = [];

    public function __construct(private ?ContextBuilder $value)
    {
        unset($this->component);
    }

    public function __get(string $name): ?ContextBuilder
    {
        $this->events[] = 'get';

        return $this->value;
    }

    public function __isset(string $name): bool
    {
        $this->events[] = 'isset';

        return false;
    }
}

/** @implements ArrayAccess<string, ContextBuilder | null> */
class LazyComponents implements ArrayAccess
{
    public array $events = [];

    public function __construct(private ?ContextBuilder $component) {}

    public function offsetExists(mixed $offset): bool
    {
        $this->events[] = 'exists';

        return false;
    }

    public function offsetGet(mixed $offset): ?ContextBuilder
    {
        $this->events[] = 'get';

        return $this->component;
    }

    public function offsetSet(mixed $offset, mixed $value): void {}

    public function offsetUnset(mixed $offset): void {}
}

class CustomBuilder extends Builder
{
    public array $conditions = [];

    public array $argumentCounts = [];

    public ?self $replacement = null;

    public function inset(mixed $condition = 'custom-default', mixed $optional = 'optional'): static
    {
        $this->conditions[] = $condition;
        $this->argumentCounts[] = func_num_args();

        return $this->replacement ?? $this;
    }
}

class CloningRepeater extends Repeater
{
    public function inset(bool | Closure $condition = true): static
    {
        return clone $this;
    }
}

class Unrelated
{
    public function inset(bool $condition = true): static
    {
        return $this;
    }
}

function manual(Builder $component, Unrelated $unrelated, array $arguments, string $method): void
{
    $component->inset(unknown: true);
    $component->inset(true, false);
    $component->inset(...$arguments);
    $component->inset(...);
    $component->{$method}(true);
    $unrelated->inset();
}
