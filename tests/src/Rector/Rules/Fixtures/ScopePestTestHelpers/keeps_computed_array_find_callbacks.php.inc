<?php

function acceptValue(int $value): bool
{
    return $value > 1;
}

it('finds a value through `array_find()`', function () {
    expect(array_find(
        [1, 2],
        sprintf('%s%s', 'accept', 'Value'),
    ))->toBe(2);
});
-----
<?php

function acceptValue(int $value): bool
{
    return $value > 1;
}

it('finds a value through `array_find()`', function () {
    expect(array_find(
        [1, 2],
        sprintf('%s%s', 'accept', 'Value'),
    ))->toBe(2);
});
