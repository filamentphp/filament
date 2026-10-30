<?php

use Filament\Actions\Action;
use Filament\Tests\Upgrade\Fixtures\ThinActionAndTableAliases\CustomDescendant;
use Filament\Tests\Upgrade\Fixtures\ThinActionAndTableAliases\InheritedOwner;
use Filament\Tests\Upgrade\Fixtures\ThinActionAndTableAliases\MagicMixedConsumer;
use Filament\Tests\Upgrade\Fixtures\ThinActionAndTableAliases\Owner;
use Filament\Tests\Upgrade\Fixtures\ThinActionAndTableAliases\SupportedMixedConsumer;
use Filament\Tests\Upgrade\Fixtures\ThinActionAndTableAliases\Unrelated;
use Filament\Tests\Upgrade\Fixtures\ThinActionAndTableAliases\UnrelatedMixedConsumer;

use function Filament\Tests\Upgrade\Fixtures\ThinActionAndTableAliases\calls;
use function Filament\Tests\Upgrade\Fixtures\ThinActionAndTableAliases\customCalls;
use function Filament\Tests\Upgrade\Fixtures\ThinActionAndTableAliases\invalidCalls;
use function Filament\Tests\Upgrade\Fixtures\ThinActionAndTableAliases\unrelatedCalls;

require $argv[1] . '/vendor/autoload.php';

if ($argv[3] === 'legacy') {
    require __DIR__ . '/legacy-runtime.php';
}

require $argv[2] . '/Owner.php';
require $argv[2] . '/Consumers.php';

$owner = new InheritedOwner;
$action = (new ReflectionClass(Action::class))->newInstanceWithoutConstructor();
$customOwner = new CustomDescendant;
$callables = [...calls($owner, null, $action), ...$owner->inspect($action), ...$customOwner->parentCallables()];
$owner->inspectOther($owner);
$signatures = [];

foreach ($callables as $callable) {
    $reflection = new ReflectionFunction($callable);
    $signatures[] = [
        array_map(static fn (ReflectionParameter $parameter): array => [$parameter->getName(), (string) $parameter->getType(), $parameter->getDefaultValue()], $reflection->getParameters()),
        (string) $reflection->getReturnType(),
    ];
}

$results = [
    $callables[0](),
    $callables[0](mountedAction: $action),
    $callables[0](...['mountedAction' => $action]),
    $callables[0](null, 'ignored'),
    $callables[1](['ignored'], true),
    $callables[2](),
    $callables[2](mountedAction: $action),
    $callables[2](1, $action, 'ignored'),
    $callables[3]('ignored'),
    $callables[4]('ignored'),
    $callables[5](mountedAction: $action),
    $callables[6](mountedAction: $action),
    $callables[7](['ignored'], true),
    customCalls($customOwner),
    unrelatedCalls(new Unrelated),
    invalidCalls($owner),
    (new SupportedMixedConsumer)->update(),
    (new UnrelatedMixedConsumer)->update(),
    (new MagicMixedConsumer)->update(),
    (new SupportedMixedConsumer)->nullableCalls(null, null),
    (new SupportedMixedConsumer)->nullableControl($owner),
];

echo json_encode([$results, $signatures, Owner::$events], JSON_THROW_ON_ERROR);
