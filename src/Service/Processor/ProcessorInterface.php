<?php

namespace App\Service\Processor;


interface ProcessorInterface
{
    public function __invoke(int $limit): int;
}