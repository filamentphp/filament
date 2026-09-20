<?php

function localValue(): string
{
    return 'ok';
}

it('preserves function availability', function () {
    expect(function_exists(sprintf('%s%s', 'local', 'Value')))
        ->toBeTrue();
});
-----
<?php

function localValue(): string
{
    return 'ok';
}

it('preserves function availability', function () {
    expect(function_exists(sprintf('%s%s', 'local', 'Value')))
        ->toBeTrue();
});
