<?php

namespace Filament\Tests\Upgrade\Fixtures\ImportExample;

use Filament\Actions\Imports\ImportColumn;
use RuntimeException;

function check(bool $condition): void
{
    if (! $condition) {
        throw new RuntimeException('Import example behavior changed');
    }
}

function effect(string $event): string
{
    $GLOBALS['events'][] = $event;

    return $event;
}

trait ReadsExamples
{
    public function sharedReceiver(): static
    {
        return $this;
    }

    public function sharedExamples(): array
    {
        $component = $this;

        return [
            $this->GETEXAMPLE(effect('shared-direct')),
            ($this->getExample(...))(effect('shared-callable')),
            $component->getExample(),
            $component->sharedReceiver()->getExample(),
            self::GETEXAMPLE(),
            static::GETEXAMPLE(),
            (self::getExample(...))(),
            (static::getExample(...))(),
        ];
    }
}

class Column extends ImportColumn
{
    use ReadsExamples;

    public array $values = ['associative' => 'first', 12 => 'second'];

    public function getExamples(): array
    {
        effect('examples');

        return $this->values;
    }

    public function relativeExamples(): array
    {
        return [self::getExample(), static::getExample(), (self::getExample(...))(), (static::getExample(...))()];
    }
}

class CustomColumn extends Column
{
    public function getExample($suffix = ''): mixed
    {
        return parent::getExample(effect('parent-argument')) . $suffix;
    }
}

class ChildColumn extends CustomColumn
{
    public function getExample($suffix = ''): mixed
    {
        return parent::getExample(suffix: $suffix) . '-child';
    }
}

trait UsesParentExample
{
    public function getExample(): mixed
    {
        $callback = parent::getExample(...);

        return $callback(effect('trait-argument'));
    }
}

class TraitColumn extends Column
{
    use UsesParentExample;
}

class UnrelatedParent
{
    use ReadsExamples;

    public function getExample(): mixed
    {
        return 'unrelated';
    }
}

class MagicReader
{
    use ReadsExamples;

    public function __call(string $method, array $arguments): string
    {
        effect($method . ':' . implode(',', $arguments));

        return 'magic';
    }
}

class UnrelatedChild extends UnrelatedParent
{
    use UsesParentExample;
}

class Holder
{
    public function __construct(public ?ImportColumn $column) {}
}

function receiver(?ImportColumn $column): ?ImportColumn
{
    effect('receiver');

    return $column;
}

function inspect(ImportColumn $column): mixed
{
    return $column->getExample();
}

$column = new Column('name');
$GLOBALS['events'] = [];
check(receiver($column)->getExample(effect('ignored')) === 'first');
check($GLOBALS['events'] === ['receiver', 'ignored', 'examples']);
check($column->sharedExamples() === array_fill(0, 8, 'first'));
check((new UnrelatedParent)->sharedExamples() === array_fill(0, 8, 'unrelated'));
check((new MagicReader)->sharedExamples() === array_fill(0, 8, 'magic'));
check($column->relativeExamples() === array_fill(0, 4, 'first'));
$sharedEvents = $GLOBALS['events'];
$callback = receiver($column)->getExample(...);
check($callback(effect('callable-ignored')) === 'first');
$column->values = [];
check(inspect($column) === null);
$column->values = ['key' => null, 'later' => 'not-first'];
check(inspect($column) === null);
$column->values = ['key' => new Holder($column)];
check($column->getExample()->column === $column);
check(isset($column->getExample()->column));
check(! empty($column->getExample()->column));
$column->values = ['key' => 'first'];
$custom = new ChildColumn('custom');
check(inspect($custom) === 'first-child');
check($custom->relativeExamples() === ['first', 'first-child', 'first', 'first-child']);
check($custom->getExample(suffix: effect('-named')) === 'first-named-child');
$customCallback = $custom->getExample(...);
check($customCallback(suffix: '-callable') === 'first-callable-child');
check($custom->getExample(...['suffix' => '-unpacked']) === 'first-unpacked-child');
check((new TraitColumn('trait'))->getExample() === 'first');
check((new UnrelatedChild)->getExample() === 'unrelated');
$configured = (new ImportColumn('configured'))->examples(static function (): array {
    effect('configuration');

    return ['associated' => 'configured-first'];
});
$GLOBALS['events'] = [];
check($configured->getExample() === 'configured-first');
check($GLOBALS['events'] === ['configuration']);
$GLOBALS['events'] = [];
check(receiver(null)?->getExample(effect('skipped'))->column === null);
check($GLOBALS['events'] === ['receiver']);
check((receiver(null)?->getExample()[effect('skipped-offset')] ?? 'fallback') === 'fallback');
check($GLOBALS['events'] === ['receiver', 'receiver']);
$holder = null;
check($holder?->column->getExample(effect('skipped-holder')) === null);
$holder = new Holder($column);
check($holder?->column?->getExample() === 'first');
check(receiver($column)?->getExample(effect('evaluated')) === 'first');
echo json_encode([$GLOBALS['events'], $sharedEvents], JSON_THROW_ON_ERROR), "\n";
