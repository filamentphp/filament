<?php

function globalPestHelper(): string
{
    return 'global';
}

it('uses a global Pest helper', function () {
    expect(globalPestHelper())->toBe('global');
});
-----
<?php

function globalPestHelper(): string
{
    return 'global';
}

it('uses a global Pest helper', function () {
    expect(globalPestHelper())->toBe('global');
});
