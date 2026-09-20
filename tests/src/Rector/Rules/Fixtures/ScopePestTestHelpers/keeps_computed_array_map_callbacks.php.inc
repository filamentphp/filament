<?php

function doubleValue(int $value): int
{
    return $value * 2;
}

it('maps values', function () {
    expect(doubleValue(2))->toBe(4);

    expect(array_map(
        sprintf('%s%s', 'double', 'Value'),
        [2],
    ))->toBe([4]);
});
-----
<?php

function doubleValue(int $value): int
{
    return $value * 2;
}

it('maps values', function () {
    expect(doubleValue(2))->toBe(4);

    expect(array_map(
        sprintf('%s%s', 'double', 'Value'),
        [2],
    ))->toBe([4]);
});
