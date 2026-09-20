<?php

it('uses recursive helpers', function () {
    expect(factorial(3))->toBe(6)
        ->and(isEven(4))->toBeTrue();
});

function factorial(int $value): int
{
    return $value <= 1 ? 1 : $value * decrement($value) * factorial($value - 1);
}

function decrement(int $value): int
{
    return $value - 1;
}

function isEven(int $value): bool
{
    return $value === 0 || isOdd($value - 1);
}

function isOdd(int $value): bool
{
    return $value !== 0 && isEven($value - 1);
}
-----
<?php

$decrement = function (int $value): int {
    return $value - 1;
};

$factorial = function (int $value) use ($decrement, &$factorial): int {
    return $value <= 1 ? 1 : $value * $decrement($value) * $factorial($value - 1);
};

$isEven = function (int $value) use (&$isOdd): bool {
    return $value === 0 || $isOdd($value - 1);
};

$isOdd = function (int $value) use (&$isEven): bool {
    return $value !== 0 && $isEven($value - 1);
};

it('uses recursive helpers', function () use ($factorial, $isEven) {
    expect($factorial(3))->toBe(6)
        ->and($isEven(4))->toBeTrue();
});
