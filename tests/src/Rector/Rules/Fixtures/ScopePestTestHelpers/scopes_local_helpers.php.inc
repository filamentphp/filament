<?php

function localHelper(string $value): string
{
    return strtoupper($value);
}

function unusedHelper(): string
{
    return 'unused';
}

it('uses the helper', function () {
    expect(localHelper('value'))->toBe('VALUE');
});

test('also scopes test callbacks', function () {
    expect(localHelper('test'))->toBe('TEST');
});
-----
<?php

$localHelper = function (string $value): string {
    return strtoupper($value);
};

$unusedHelper = function (): string {
    return 'unused';
};

it('uses the helper', function () use ($localHelper) {
    expect($localHelper('value'))->toBe('VALUE');
});

test('also scopes test callbacks', function () use ($localHelper) {
    expect($localHelper('test'))->toBe('TEST');
});