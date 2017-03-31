<?php

namespace Tetris\Exceptions;

use Exception;
use Throwable;

class SafeException extends Exception
{
    public $parentException;

    public function __construct($response, $message = 'API Error', $code = 500, Throwable $previous = null)
    {
        $responseMessage = !empty($response->body->message) && is_string($response->body->message)
            ? $response->body->message
            : NULL;

        if ($response->code >= 400 && $response->code < 600) {
            $code = $response->code;
        }

        parent::__construct(
            $responseMessage ? $responseMessage : $message,
            $code,
            $previous
        );
    }
}