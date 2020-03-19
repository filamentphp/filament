<?php

namespace Filament\Traits;

use Illuminate\Support\Facades\Validator;

trait ConsoleValidation {

    /**
     * Validate asking for input.
     *
     * @param  mixed   $method
     * @param  array   $rules
     * @return string
     */
    public function validateInput($method, $rules)
    {
        $value = $method();
        $validate = $this->doValidation($rules, $value);

        if ($validate !== true) {
            $this->error($validate);
            $value = $this->validateInput($method, $rules);
        }

        return $value;
    }

    /**
     * Validate input.
     * 
     * @access private
     * @param array   $rules
     * @param mixed   $value
     * @return bool|string
     */
    private function doValidation($rules, $value)
    {
        $validator = Validator::make([$rules[0] => $value], [ $rules[0] => $rules[1] ]);

        if ($validator->fails()) {
            $error = $validator->errors();
            return $error->first($rules[0]);
        } else {
            return true;
        }
    }
}