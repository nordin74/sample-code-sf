<?php

namespace App\Validator;


final class SubscribeRequestValidator implements SimpleValidator
{
    private array $args;


    public function __construct()
    {
        $this->args = [
            'msisdn'     => function ($msisdn) {
                return is_int($msisdn);
            },
            'operatorid' => function ($operatorId) {
                return is_int($operatorId) && $operatorId > 0 && $operatorId < 11;
            },
            'text'       => function ($text) {
                $length = mb_strlen($text, 'UTF-8');

                return $length > 5 && $length < 15;
            }
        ];
    }


    public function validate($data, &$firstError = null): bool
    {
        foreach ($this->args as $key => $function) {
            if ($function($data[$key]) === false) {
                $firstError = $key;

                return false;
            }
        }

        return true;
    }
}
