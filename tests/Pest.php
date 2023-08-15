<?php

use Illuminate\Database\Eloquent\Model;

expect()->extend('toBeSameModel', fn (Model $model) => $this
    ->is($model)->toBeTrue());
