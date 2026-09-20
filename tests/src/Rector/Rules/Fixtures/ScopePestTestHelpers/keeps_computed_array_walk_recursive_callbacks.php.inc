<?php

function doubleValue(int &$value): void
{
    $value *= 2;
}

it('updates nested values', function () {
    $values = [[2]];

    array_walk_recursive($values, sprintf('%s%s', 'double', 'Value'));

    expect($values)->toBe([[4]]);
});
-----
<?php

function doubleValue(int &$value): void
{
    $value *= 2;
}

it('updates nested values', function () {
    $values = [[2]];

    array_walk_recursive($values, sprintf('%s%s', 'double', 'Value'));

    expect($values)->toBe([[4]]);
});
