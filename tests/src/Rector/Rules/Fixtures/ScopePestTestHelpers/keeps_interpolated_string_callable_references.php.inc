<?php

function doubleValue(int $value): int
{
    return $value * 2;
}

it('maps values', function () {
    $prefix = 'double';

    expect(doubleValue(2))->toBe(4);
    expect(array_map("{$prefix}Value", [2]))->toBe([4]);
});
-----
<?php

function doubleValue(int $value): int
{
    return $value * 2;
}

it('maps values', function () {
    $prefix = 'double';

    expect(doubleValue(2))->toBe(4);
    expect(array_map("{$prefix}Value", [2]))->toBe([4]);
});
