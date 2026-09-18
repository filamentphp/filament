<?php

function helper(): string
{
    return 'value';
}

it('calls the helper', function () {
    function nestedHelper(): string
    {
        return helper();
    }

    expect(nestedHelper())->toBe('value');
});
-----
<?php

function helper(): string
{
    return 'value';
}

it('calls the helper', function () {
    function nestedHelper(): string
    {
        return helper();
    }

    expect(nestedHelper())->toBe('value');
});
