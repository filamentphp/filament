<?php

function localValue(): string
{
    return 'ok';
}

it('reflects a function through a subclass', function () {
    $reflection = new class(
        sprintf('%s%s', 'local', 'Value'),
    ) extends ReflectionFunction {};

    expect($reflection->invoke())->toBe('ok');
});
-----
<?php

function localValue(): string
{
    return 'ok';
}

it('reflects a function through a subclass', function () {
    $reflection = new class(
        sprintf('%s%s', 'local', 'Value'),
    ) extends ReflectionFunction {};

    expect($reflection->invoke())->toBe('ok');
});
