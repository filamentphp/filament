<?php

namespace Filament\Tests\Upgrade\Fixtures\Concealment;

use Filament\Schemas\Components\Component;
use Filament\Schemas\Components\Contracts\CanConcealComponents;
use RuntimeException;

function check(bool $condition): void
{
    if (! $condition) {
        throw new RuntimeException('Concealment behavior changed');
    }
}

function effect(string $event): string
{
    $GLOBALS['events'][] = $event;

    return $event;
}

trait ReadsConcealment
{
    public function sharedReceiver(): static
    {
        return $this;
    }

    public function sharedConcealment(): array
    {
        $component = $this;

        return [
            $this->ISCONCEALED(effect('shared-direct')),
            ($this->isConcealed(...))(effect('shared-callable')),
            $component->isConcealed(),
            $component->sharedReceiver()->isConcealed(),
            self::ISCONCEALED(),
            static::ISCONCEALED(),
            (self::isConcealed(...))(),
            (static::isConcealed(...))(),
        ];
    }
}

class Container extends Component implements CanConcealComponents
{
    use ReadsConcealment;

    public function relativeConcealment(): array
    {
        return [self::isConcealed(), static::isConcealed(), (self::isConcealed(...))(), (static::isConcealed(...))()];
    }
}

class AlwaysConcealed extends Container
{
    public function isConcealed(): bool
    {
        return true;
    }
}

class CustomContainer extends Container
{
    public function isConcealed($condition = false): bool
    {
        return parent::isConcealed(effect('parent-argument')) || $condition;
    }
}

class ChildContainer extends CustomContainer
{
    public function isConcealed($condition = false): bool
    {
        return parent::isConcealed(condition: $condition);
    }
}

trait UsesParentConcealment
{
    public function isConcealed(): bool
    {
        $callback = parent::isConcealed(...);

        return $callback(effect('trait-argument'));
    }
}

class TraitContainer extends Container
{
    use UsesParentConcealment;
}

class UnrelatedParent
{
    use ReadsConcealment;

    public function isConcealed(): bool
    {
        return true;
    }
}

class MagicReader
{
    use ReadsConcealment;

    public function __call(string $method, array $arguments): bool
    {
        effect($method . ':' . implode(',', $arguments));

        return true;
    }
}

class UnrelatedChild extends UnrelatedParent
{
    use UsesParentConcealment;
}

class Holder
{
    public function __construct(public ?Component $component) {}
}

function receiver(?Component $component): ?Component
{
    effect('receiver');

    return $component;
}

function inspect(Component $component): bool
{
    return $component->isConcealed();
}

$component = new Container;
$GLOBALS['events'] = [];
check(receiver($component)->isConcealed(effect('ignored')) === false);
check($GLOBALS['events'] === ['receiver', 'ignored']);
check($component->sharedConcealment() === array_fill(0, 8, false));
check((new UnrelatedParent)->sharedConcealment() === array_fill(0, 8, true));
check((new MagicReader)->sharedConcealment() === array_fill(0, 8, true));
check($component->relativeConcealment() === array_fill(0, 4, false));
check((new AlwaysConcealed)->relativeConcealment() === [false, true, false, true]);
$sharedEvents = $GLOBALS['events'];
$callback = receiver($component)->isConcealed(...);
check($callback(effect('callable-ignored')) === false);
check(inspect($component) === false);
$custom = new ChildContainer;
check(inspect($custom) === false);
check($custom->isConcealed(condition: true) === true);
$customCallback = $custom->isConcealed(...);
check($customCallback(condition: true) === true);
check($custom->isConcealed(...['condition' => true]) === true);
check((new TraitContainer)->isConcealed() === false);
check((new UnrelatedChild)->isConcealed() === true);
$GLOBALS['events'] = [];
check(receiver(null)?->isConcealed(effect('skipped')) === null);
check($GLOBALS['events'] === ['receiver']);
check((receiver(null)?->isConcealed()[effect('skipped-offset')] ?? 'fallback') === 'fallback');
check($GLOBALS['events'] === ['receiver', 'receiver']);
$holder = null;
check($holder?->component->isConcealed(effect('skipped-holder')) === null);
$holder = new Holder($component);
check($holder?->component?->isConcealed() === false);
check(receiver($component)?->isConcealed(effect('evaluated')) === false);
echo json_encode([$GLOBALS['events'], $sharedEvents], JSON_THROW_ON_ERROR), "\n";
