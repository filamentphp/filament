<?php

function doubleValue(int $value): int
{
    return $value * 2;
}

$closureClass = Closure::class;

it('creates a helper callback', function () use ($closureClass) {
    expect($closureClass::fromCallable(
        sprintf('%s%s', 'double', 'Value'),
    )->__invoke(2))->toBe(4);
});
-----
<?php

function doubleValue(int $value): int
{
    return $value * 2;
}

$closureClass = Closure::class;

it('creates a helper callback', function () use ($closureClass) {
    expect($closureClass::fromCallable(
        sprintf('%s%s', 'double', 'Value'),
    )->__invoke(2))->toBe(4);
});
