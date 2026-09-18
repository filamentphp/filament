<?php

function compareValues(int $left, int $right): int
{
    return $left <=> $right;
}

it('intersects values', function () {
    expect(array_uintersect(
        [1, 2],
        [2],
        sprintf('%s%s', 'compare', 'Values'),
    ))->toBe([1 => 2]);
});
-----
<?php

function compareValues(int $left, int $right): int
{
    return $left <=> $right;
}

it('intersects values', function () {
    expect(array_uintersect(
        [1, 2],
        [2],
        sprintf('%s%s', 'compare', 'Values'),
    ))->toBe([1 => 2]);
});
