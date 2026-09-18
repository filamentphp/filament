<?php

function globalHelpersFileHelper(): string
{
    return 'global';
}

it('uses a global Helpers file helper', function () {
    expect(globalHelpersFileHelper())->toBe('global');
});
-----
<?php

function globalHelpersFileHelper(): string
{
    return 'global';
}

it('uses a global Helpers file helper', function () {
    expect(globalHelpersFileHelper())->toBe('global');
});
