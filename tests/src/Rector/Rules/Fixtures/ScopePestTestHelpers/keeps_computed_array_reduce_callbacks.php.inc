<?php

function addValues(int $carry, int $value): int
{
    return $carry + $value;
}

it('reduces values', function () {
    expect(array_reduce(
        [1, 2],
        sprintf('%s%s', 'add', 'Values'),
        0,
    ))->toBe(3);
});
-----
<?php

function addValues(int $carry, int $value): int
{
    return $carry + $value;
}

it('reduces values', function () {
    expect(array_reduce(
        [1, 2],
        sprintf('%s%s', 'add', 'Values'),
        0,
    ))->toBe(3);
});
