<?php

function compareValues(int $left, int $right): int
{
    return $left <=> $right;
}

it('sorts values', function () {
    $values = [2, 1];

    usort($values, sprintf('%s%s', 'compare', 'Values'));

    expect($values)->toBe([1, 2]);
});
-----
<?php

function compareValues(int $left, int $right): int
{
    return $left <=> $right;
}

it('sorts values', function () {
    $values = [2, 1];

    usort($values, sprintf('%s%s', 'compare', 'Values'));

    expect($values)->toBe([1, 2]);
});
