<?php

function helper(): string
{
    return 'ok';
}

it('preserves the function', function () {
    $variableName = sprintf('%s%s', 'hel', 'per');
    $$variableName = 42;

    expect(helper())->toBe('ok');
});
-----
<?php

function helper(): string
{
    return 'ok';
}

it('preserves the function', function () {
    $variableName = sprintf('%s%s', 'hel', 'per');
    $$variableName = 42;

    expect(helper())->toBe('ok');
});
