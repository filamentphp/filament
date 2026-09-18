<?php

function continueIteration(): bool
{
    return true;
}

it('iterates with a computed callback', function () {
    expect(iterator_apply(
        new ArrayIterator([1, 2]),
        sprintf('%s%s', 'continue', 'Iteration'),
    ))->toBe(2);
});
-----
<?php

function continueIteration(): bool
{
    return true;
}

it('iterates with a computed callback', function () {
    expect(iterator_apply(
        new ArrayIterator([1, 2]),
        sprintf('%s%s', 'continue', 'Iteration'),
    ))->toBe(2);
});
