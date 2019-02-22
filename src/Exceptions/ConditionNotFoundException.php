<?php

namespace ConditionalActions\Exceptions;

use Symfony\Component\HttpFoundation\Response;

class ConditionNotFoundException extends BaseException
{
    protected $code = Response::HTTP_NOT_FOUND;
}
