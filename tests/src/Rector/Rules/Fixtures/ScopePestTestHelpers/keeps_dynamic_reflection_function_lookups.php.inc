<?php

function localValue(): string
{
    return 'ok';
}

it('reflects a local function dynamically', function () {
    $reflectionClass = ReflectionFunction::class;
    $reflection = new $reflectionClass(
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

it('reflects a local function dynamically', function () {
    $reflectionClass = ReflectionFunction::class;
    $reflection = new $reflectionClass(
        sprintf('%s%s', 'local', 'Value'),
    );

    expect($reflection->invoke())->toBe('ok');
});
