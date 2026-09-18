<?php

trait LocalTrait
{
}

class LocalRecord
{
    use LocalTrait;

    public const DESCRIPTION = 'has a record';
}

it(('LocalRecord')::DESCRIPTION, function () {
    expect(true)->toBeTrue();
});
-----
<?php

trait LocalTrait
{
}

class LocalRecord
{
    use LocalTrait;

    public const DESCRIPTION = 'has a record';
}

it(('LocalRecord')::DESCRIPTION, function () {
    expect(true)->toBeTrue();
});
