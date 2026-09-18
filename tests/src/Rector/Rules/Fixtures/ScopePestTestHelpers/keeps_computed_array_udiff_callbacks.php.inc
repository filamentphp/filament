<?php

function compareValues(int $left, int $right): int
{
    return $left <=> $right;
}

it('compares values', function () {
    expect(array_udiff(
        [1, 2],
        [2],
        sprintf('%s%s', 'compare', 'Values'),
    ))->toBe([1]);
});
-----
<?php

function compareValues(int $left, int $right): int
{
    return $left <=> $right;
}

it('compares values', function () {
    expect(array_udiff(
        [1, 2],
        [2],
        sprintf('%s%s', 'compare', 'Values'),
    ))->toBe([1]);
});
