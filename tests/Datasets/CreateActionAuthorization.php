<?php

dataset('create action authorization subjects', [
    'cross-model denial overrides owner grant' => [true, true, false, null, false],
    'cross-model grant overrides owner denial' => [true, false, true, null, true],
    'same-model grant' => [false, true, false, null, true],
    'same-model denial' => [false, false, true, null, false],
    'explicit grant overrides default denial' => [true, false, false, true, true],
    'explicit denial overrides default grant' => [true, true, true, false, false],
]);
