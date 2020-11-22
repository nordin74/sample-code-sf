<?php

namespace App\Validator;


interface SimpleValidatorInterface
{
    public function validate($data, &$firstError = null): bool;
}