<?php

namespace ConditionalActions\Http\Presenters;

use Illuminate\Support\Arr;

trait ResponseWrapper
{
    protected $responseRoot = 'data';

    protected function jsonResponse($data)
    {
        $response = [];

        return Arr::set($response, $this->responseRoot, $data);
    }
}
