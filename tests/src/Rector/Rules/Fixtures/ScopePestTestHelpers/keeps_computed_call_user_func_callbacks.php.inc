<?php

function doubleValue(int $value): int
{
    return $value * 2;
}

it('calls the helper', function () {
    expect(call_user_func(
        sprintf('%s%s', 'double', 'Value'),
        2,
    ))->toBe(4);
});
-----
<?php

function doubleValue(int $value): int
{
    return $value * 2;
}

it('calls the helper', function () {
    expect(call_user_func(
        sprintf('%s%s', 'double', 'Value'),
        2,
    ))->toBe(4);
});
