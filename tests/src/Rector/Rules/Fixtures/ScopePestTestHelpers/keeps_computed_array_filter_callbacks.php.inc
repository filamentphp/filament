<?php

function doubleValue(int $value): int
{
    return $value * 2;
}

it('filters with a computed callback', function () {
    expect(array_filter(
        [2],
        sprintf('%s%s', 'double', 'Value'),
    ))->toBe([2]);
});
-----
<?php

function doubleValue(int $value): int
{
    return $value * 2;
}

it('filters with a computed callback', function () {
    expect(array_filter(
        [2],
        sprintf('%s%s', 'double', 'Value'),
    ))->toBe([2]);
});
