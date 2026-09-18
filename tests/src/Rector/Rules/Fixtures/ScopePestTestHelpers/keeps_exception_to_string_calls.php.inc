<?php

function currentHelperTrace(): string
{
    return (new Exception)->__toString();
}

it('preserves the helper frame name', function () {
    expect(currentHelperTrace())
        ->toContain(sprintf('%s%s()', 'currentHelper', 'Trace'));
});
-----
<?php

function currentHelperTrace(): string
{
    return (new Exception)->__toString();
}

it('preserves the helper frame name', function () {
    expect(currentHelperTrace())
        ->toContain(sprintf('%s%s()', 'currentHelper', 'Trace'));
});
