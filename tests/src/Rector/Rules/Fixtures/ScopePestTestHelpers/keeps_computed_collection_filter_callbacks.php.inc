<?php

function doubleValue(int $value): int
{
    return $value * 2;
}

it('filters values through `filter()`', function () {
    expect(collect([2])
        ->filter(sprintf('%s%s', 'double', 'Value'))
        ->all())->toBe([2]);
});
-----
<?php

function doubleValue(int $value): int
{
    return $value * 2;
}

it('filters values through `filter()`', function () {
    expect(collect([2])
        ->filter(sprintf('%s%s', 'double', 'Value'))
        ->all())->toBe([2]);
});
