<?php

function doubleValue(int $value): int
{
    return $value * 2;
}

it('maps values', function () {
    expect(array_map('doubleValue', [2]))->toBe([4]);
});
-----
<?php

function doubleValue(int $value): int
{
    return $value * 2;
}

it('maps values', function () {
    expect(array_map('doubleValue', [2]))->toBe([4]);
});
