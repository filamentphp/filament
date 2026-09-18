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

it('has a record', function ($record) {
    expect($record)->toBeInstanceOf(LocalRecord::class);
})->with([makeRecord()]);
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


it('has a record', function ($record) {
    expect($record)->toBeInstanceOf(LocalRecord::class);
})->with([$makeRecord()]);
