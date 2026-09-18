<?php

function localValue(): string
{
    return 'ok';
}

it('reflects a local function', function () {
    $reflection = new ReflectionFunction(
        sprintf('%s%s', 'local', 'Value'),
    );

    expect($reflection->invoke())->toBe('ok');
});
-----
<?php

function localValue(): string
{
    return 'ok';
}

it('reflects a local function', function () {
    $reflection = new ReflectionFunction(
        sprintf('%s%s', 'local', 'Value'),
    );

    expect($reflection->invoke())->toBe('ok');
});
