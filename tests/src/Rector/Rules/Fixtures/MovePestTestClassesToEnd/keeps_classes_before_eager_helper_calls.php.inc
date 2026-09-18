<?php

trait LocalTrait
{
}

class LocalRecord
{
    use LocalTrait;
}

function makeRecord(): LocalRecord
{
    return new LocalRecord;
}

$record = makeRecord();

it('has a record', fn () => expect($record)->toBeInstanceOf(LocalRecord::class));
-----
<?php

trait LocalTrait
{
}

class LocalRecord
{
    use LocalTrait;
}
$makeRecord = function (): LocalRecord {
    return new LocalRecord;
};


$record = $makeRecord();

it('has a record', fn () => expect($record)->toBeInstanceOf(LocalRecord::class));
