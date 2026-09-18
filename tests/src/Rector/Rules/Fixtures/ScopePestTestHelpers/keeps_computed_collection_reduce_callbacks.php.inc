<?php

function addValues(int $carry, int $value): int
{
    return $carry + $value;
}

it('reduces values through `reduce()`', function () {
    expect(collect([1, 2])
        ->reduce(sprintf('%s%s', 'add', 'Values'), 0))->toBe(3);
});
-----
<?php

function addValues(int $carry, int $value): int
{
    return $carry + $value;
}

it('reduces values through `reduce()`', function () {
    expect(collect([1, 2])
        ->reduce(sprintf('%s%s', 'add', 'Values'), 0))->toBe(3);
});
