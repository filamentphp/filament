<?php

function countdown(int $value): int
{
    return $value === 0
        ? 0
        : 1 + call_user_func(__FUNCTION__, $value - 1);
}

function helperName(): string
{
    return __METHOD__;
}

it('counts down', function () {
    expect(countdown(2))->toBe(2)
        ->and(helperName())->toBe('helperName');
});
-----
<?php

function countdown(int $value): int
{
    return $value === 0
        ? 0
        : 1 + call_user_func(__FUNCTION__, $value - 1);
}

function helperName(): string
{
    return __METHOD__;
}

it('counts down', function () {
    expect(countdown(2))->toBe(2)
        ->and(helperName())->toBe('helperName');
});
