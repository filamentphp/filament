<?php

function currentHelperTrace(): string
{
    return (string) new Exception;
}

it('preserves the helper frame name', function () {
    expect(currentHelperTrace())
        ->toContain(sprintf('%s%s()', 'currentHelper', 'Trace'));
});
-----
<?php

function currentHelperTrace(): string
{
    return (string) new Exception;
}

it('preserves the helper frame name', function () {
    expect(currentHelperTrace())
        ->toContain(sprintf('%s%s()', 'currentHelper', 'Trace'));
});
