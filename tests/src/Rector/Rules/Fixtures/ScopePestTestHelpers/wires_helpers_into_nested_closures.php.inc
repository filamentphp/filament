<?php

it('uses a helper with a nested closure', function () {
    expect(mapDouble([1, 2, 3]))->toBe([2, 4, 6]);
});

function mapDouble(array $values): array
{
    return array_map(function (int $value): int {
        return doubleIt($value);
    }, $values);
}

function doubleIt(int $value): int
{
    return $value * 2;
}
-----
<?php

$doubleIt = function (int $value): int {
    return $value * 2;
};

$mapDouble = function (array $values) use ($doubleIt): array {
    return array_map(function (int $value) use ($doubleIt): int {
        return $doubleIt($value);
    }, $values);
};

it('uses a helper with a nested closure', function () use ($mapDouble) {
    expect($mapDouble([1, 2, 3]))->toBe([2, 4, 6]);
});
