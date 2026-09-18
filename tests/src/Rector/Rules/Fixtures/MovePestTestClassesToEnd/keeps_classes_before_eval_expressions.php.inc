<?php

trait LocalTrait
{
}

class LocalRecord
{
    use LocalTrait;
}

it(eval('return get_class(new LocalRecord);'), function () {
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
}

it(eval('return get_class(new LocalRecord);'), function () {
    expect(true)->toBeTrue();
});
