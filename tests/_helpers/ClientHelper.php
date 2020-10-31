<?php


namespace App\Tests\_helpers;


trait ClientHelper
{
    private function jsonRequest($client, $method, $uri, $content = null)
    {
        if ($content !== null) {
            $content = json_encode($content);
        }

        return $client->request(
            $method, $uri, [], [], ['CONTENT_TYPE' => 'application/json'], $content
        );
    }
}