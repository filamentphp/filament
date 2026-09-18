<?php

function keyedValue(int $value): array
{
    return [$value => $value * 2];
}

it('maps values with keys', function () {
    expect(collect([2])
        ->mapWithKeys(sprintf('%s%s', 'keyed', 'Value'))
        ->all())->toBe([2 => 4]);
});
-----
<?php

function keyedValue(int $value): array
{
    return [$value => $value * 2];
}

it('maps values with keys', function () {
    expect(collect([2])
        ->mapWithKeys(sprintf('%s%s', 'keyed', 'Value'))
        ->all())->toBe([2 => 4]);
});
