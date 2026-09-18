<?php

trait LocalTrait
{
}

class LocalRecord
{
    use LocalTrait;
}

function recordDescription(): string
{
    return get_class(new LocalRecord);
}

it(recordDescription(), function () {
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
$recordDescription = function (): string {
    return get_class(new LocalRecord);
};


it($recordDescription(), function () {
    expect(true)->toBeTrue();
});
