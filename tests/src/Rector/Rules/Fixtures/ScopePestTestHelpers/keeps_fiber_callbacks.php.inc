<?php

function localValue(): string
{
    return 'ok';
}

it('runs a fiber', function () {
    $fiber = new Fiber(sprintf('%s%s', 'local', 'Value'));
    $fiber->start();

    expect($fiber->getReturn())->toBe('ok');
});
-----
<?php

function localValue(): string
{
    return 'ok';
}

it('runs a fiber', function () {
    $fiber = new Fiber(sprintf('%s%s', 'local', 'Value'));
    $fiber->start();

    expect($fiber->getReturn())->toBe('ok');
});
