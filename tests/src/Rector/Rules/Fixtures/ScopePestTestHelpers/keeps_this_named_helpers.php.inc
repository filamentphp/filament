<?php

function this(): string
{
    return 'ok';
}

it('uses a helper', function () {
    expect(this())->toBe('ok');
});
-----
<?php

function this(): string
{
    return 'ok';
}

it('uses a helper', function () {
    expect(this())->toBe('ok');
});
