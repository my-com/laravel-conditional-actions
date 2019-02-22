<?php

namespace ConditionalActions\Exceptions;

use Exception;
use Illuminate\Contracts\Support\Responsable;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class BaseException extends Exception implements Responsable
{
    public function __construct($message = null, $code = 0, Throwable $previous = null)
    {
        $exceptionMessage = $message ?? Response::$statusTexts[$code] ?? '';

        parent::__construct($exceptionMessage, $code, $previous);
    }

    public function toResponse($request): Response
    {
        $exceptionCode = isset(Response::$statusTexts[$this->getCode()])
            ? $this->getCode()
            : Response::HTTP_INTERNAL_SERVER_ERROR;

        return response([
            'error' => [
                'message' => $this->getMessage(),
            ],
        ], $exceptionCode);
    }
}
