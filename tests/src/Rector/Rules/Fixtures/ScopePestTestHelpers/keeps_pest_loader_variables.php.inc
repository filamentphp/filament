<?php

function suiteClassFile(): string
{
    return __FILE__;
}

it('resolves its source file', function () {
    expect(suiteClassFile())->toBe(__FILE__);
});
-----
<?php

function suiteClassFile(): string
{
    return __FILE__;
}

it('resolves its source file', function () {
    expect(suiteClassFile())->toBe(__FILE__);
});
