<?php

function doubleValue(int $value): int
{
    return $value * 2;
}

it('invokes a computed callback', function () {
    expect(call_user_func_array(
        sprintf('%s%s', 'double', 'Value'),
        [2],
    ))->toBe(4);
});
-----
<?php

function doubleValue(int $value): int
{
    return $value * 2;
}

it('invokes a computed callback', function () {
    expect(call_user_func_array(
        sprintf('%s%s', 'double', 'Value'),
        [2],
    ))->toBe(4);
});
