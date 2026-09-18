<?php

function helper(): string
{
    return 'ok';
}

function localVariables(): array
{
    helper();

    return get_defined_vars();
}

it('preserves function locals', function () {
    expect(localVariables())->toBe([]);
});
-----
<?php

function helper(): string
{
    return 'ok';
}

function localVariables(): array
{
    helper();

    return get_defined_vars();
}

it('preserves function locals', function () {
    expect(localVariables())->toBe([]);
});
