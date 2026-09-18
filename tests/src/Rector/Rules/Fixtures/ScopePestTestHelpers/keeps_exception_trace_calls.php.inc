<?php

function currentHelperName(): string
{
    return (new Exception)->getTrace()[0]['function'];
}

it('preserves the helper frame name', function () {
    expect(currentHelperName())
        ->toBe(sprintf('%s%s', 'currentHelper', 'Name'));
});
-----
<?php

function currentHelperName(): string
{
    return (new Exception)->getTrace()[0]['function'];
}

it('preserves the helper frame name', function () {
    expect(currentHelperName())
        ->toBe(sprintf('%s%s', 'currentHelper', 'Name'));
});
