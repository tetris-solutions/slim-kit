<?php

namespace Tetris\Exceptions;

use Httpful\Request;
use Exception;

class ApiException extends SafeException
{
    public function __construct($response, $message = 'API Error', $code = 500, Exception $previous = null)
    {
        parent::__construct($response, $message, $code, $previous);

        /**
         * @var Request
         */
        $request = $response->request;

        $this->parentException = [
            'message' => $this->getMessage(),
            'statusCode' => $response->code,
            'request' => [
                'method' => $request->method,
                'uri' => $request->uri,
                'headers' => explode("\n", $request->raw_headers)
            ],
            'response' => [
                'headers' => explode("\n", $response->raw_headers),
                'statusCode' => $response->code,
                'body' => $response->body
            ]
        ];
    }
}
