<?php

function doubleValue(int $value): int
{
    return $value * 2;
}

it('creates a closure from a computed callback', function () {
    expect(Closure::fromCallable(
        sprintf('%s%s', 'double', 'Value'),
    )->__invoke(2))->toBe(4);
});
-----
<?php

function doubleValue(int $value): int
{
    return $value * 2;
}

it('creates a closure from a computed callback', function () {
    expect(Closure::fromCallable(
        sprintf('%s%s', 'double', 'Value'),
    )->__invoke(2))->toBe(4);
});
