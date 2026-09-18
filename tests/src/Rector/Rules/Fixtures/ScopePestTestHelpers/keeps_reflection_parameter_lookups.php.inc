<?php

function localValue(string $value = 'ok'): string
{
    return $value;
}

it('reflects a local function parameter', function () {
    $reflection = new ReflectionParameter(
        sprintf('%s%s', 'local', 'Value'),
        0,
    );

    expect($reflection->getDefaultValue())->toBe('ok');
});
-----
<?php

function localValue(string $value = 'ok'): string
{
    return $value;
}

it('reflects a local function parameter', function () {
    $reflection = new ReflectionParameter(
        sprintf('%s%s', 'local', 'Value'),
        0,
    );

    expect($reflection->getDefaultValue())->toBe('ok');
});
