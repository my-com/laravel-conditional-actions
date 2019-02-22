<?php

namespace ConditionalActions\Exceptions;

use Symfony\Component\HttpFoundation\Response;

class ActionNotFoundException extends BaseException
{
    protected $code = Response::HTTP_NOT_FOUND;
}
