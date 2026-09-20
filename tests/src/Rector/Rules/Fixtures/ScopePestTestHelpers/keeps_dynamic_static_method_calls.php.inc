<?php

function doubleValue(int $value): int
{
    return $value * 2;
}

it('creates a helper callback', function () {
    $factoryMethod = 'fromCallable';

    expect(Closure::$factoryMethod(
        sprintf('%s%s', 'double', 'Value'),
    )->__invoke(2))->toBe(4);
});
-----
<?php

function doubleValue(int $value): int
{
    return $value * 2;
}

it('creates a helper callback', function () {
    $factoryMethod = 'fromCallable';

    expect(Closure::$factoryMethod(
        sprintf('%s%s', 'double', 'Value'),
    )->__invoke(2))->toBe(4);
});
