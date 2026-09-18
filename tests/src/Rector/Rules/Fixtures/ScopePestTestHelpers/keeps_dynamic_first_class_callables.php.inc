<?php

function doubleValue(int $value): int
{
    return $value * 2;
}

it('maps values', function () {
    $callback = sprintf('%s%s', 'double', 'Value');

    expect(array_map($callback(...), [2]))->toBe([4]);
});
-----
<?php

function doubleValue(int $value): int
{
    return $value * 2;
}

it('maps values', function () {
    $callback = sprintf('%s%s', 'double', 'Value');

    expect(array_map($callback(...), [2]))->toBe([4]);
});
