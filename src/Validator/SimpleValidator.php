<?php

namespace App\Validator;


interface SimpleValidator
{
    public function validate($data, &$firstError = null);
}