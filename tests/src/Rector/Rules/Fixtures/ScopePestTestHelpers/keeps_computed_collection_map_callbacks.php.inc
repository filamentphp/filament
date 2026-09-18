<?php

function doubleValue(int $value): int
{
    return $value * 2;
}

it('maps values through `map()`', function () {
    expect(collect([2])
        ->map(sprintf('%s%s', 'double', 'Value'))
        ->all())->toBe([4]);
});
-----
<?php

function doubleValue(int $value): int
{
    return $value * 2;
}

it('maps values through `map()`', function () {
    expect(collect([2])
        ->map(sprintf('%s%s', 'double', 'Value'))
        ->all())->toBe([4]);
});
