<?php

function _GET(): string
{
    return 'ok';
}

it('uses a helper', function () {
    expect(_GET())->toBe('ok');
});
-----
<?php

function _GET(): string
{
    return 'ok';
}

it('uses a helper', function () {
    expect(_GET())->toBe('ok');
});
