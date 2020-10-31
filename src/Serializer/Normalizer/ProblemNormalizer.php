<?php

namespace App\Serializer\Normalizer;

use Symfony\Component\Serializer\Normalizer\ProblemNormalizer as DefaultProblemNormalizer;


class ProblemNormalizer extends DefaultProblemNormalizer
{
    public function normalize($exception, $format = null, array $context = [])
    {
        $debug = ($context['debug'] ?? true);

        $data = [
            'title' => $exception->getStatusText(),
            'status' => $exception->getStatusCode(),
            'detail' => $exception->getMessage()
        ];

        if ($debug) {
            $data['class'] = $exception->getClass();
            $data['trace'] = $exception->getTrace();
        }

        return $data;
    }
}