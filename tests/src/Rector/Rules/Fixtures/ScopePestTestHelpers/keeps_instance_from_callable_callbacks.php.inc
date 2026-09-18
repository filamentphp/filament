<?php

function doubleValue(int $value): int
{
    return $value * 2;
}

it('creates a helper callback', function () {
    $closureFactory = static fn () => null;

    $callback = $closureFactory->fromCallable(
        sprintf('%s%s', 'double', 'Value'),
    );

    expect($callback->__invoke(2))->toBe(4);
});
-----
<?php

function doubleValue(int $value): int
{
    return $value * 2;
}

it('creates a helper callback', function () {
    $closureFactory = static fn () => null;

    $callback = $closureFactory->fromCallable(
        sprintf('%s%s', 'double', 'Value'),
    );

    expect($callback->__invoke(2))->toBe(4);
});
