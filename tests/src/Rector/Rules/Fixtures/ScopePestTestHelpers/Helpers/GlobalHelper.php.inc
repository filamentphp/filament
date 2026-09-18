<?php

function globalDirectoryHelper(): string
{
    return 'global';
}

it('uses a global helper directory helper', function () {
    expect(globalDirectoryHelper())->toBe('global');
});
-----
<?php

function globalDirectoryHelper(): string
{
    return 'global';
}

it('uses a global helper directory helper', function () {
    expect(globalDirectoryHelper())->toBe('global');
});
