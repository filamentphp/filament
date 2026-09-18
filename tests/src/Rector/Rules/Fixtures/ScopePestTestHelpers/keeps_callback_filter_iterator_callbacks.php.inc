<?php

function acceptValue(int $value): bool
{
    return $value > 1;
}

it('filters values with a computed callback', function () {
    $filtered = new CallbackFilterIterator(
        new ArrayIterator([1, 2]),
        sprintf('%s%s', 'accept', 'Value'),
    );

    expect(iterator_to_array($filtered))->toBe([1 => 2]);
});
-----
<?php

function acceptValue(int $value): bool
{
    return $value > 1;
}

it('filters values with a computed callback', function () {
    $filtered = new CallbackFilterIterator(
        new ArrayIterator([1, 2]),
        sprintf('%s%s', 'accept', 'Value'),
    );

    expect(iterator_to_array($filtered))->toBe([1 => 2]);
});
