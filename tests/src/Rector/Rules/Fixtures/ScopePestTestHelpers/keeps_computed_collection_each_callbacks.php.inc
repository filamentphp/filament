<?php

function acceptValue(int $value): bool
{
    return true;
}

it('visits values', function () {
    expect(collect([2])
        ->each(sprintf('%s%s', 'accept', 'Value'))
        ->all())->toBe([2]);
});
-----
<?php

function acceptValue(int $value): bool
{
    return true;
}

it('visits values', function () {
    expect(collect([2])
        ->each(sprintf('%s%s', 'accept', 'Value'))
        ->all())->toBe([2]);
});
