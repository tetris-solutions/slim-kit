<?php

namespace Tetris;

use Httpful\Request;
use Httpful\Response;
use Exception;

class ApiException extends \Exception
{
    public $parentException;

    public function __construct(Response $response, $message = 'API Error', $code = 500, Exception $previous = null)
    {
        $responseMessage = !empty($response->body->message) && is_string($response->body->message)
            ? $response->body->message
            : NULL;

        if ($response->code >= 400 && $response->code < 600) {
            $code = $response->code;
        }

        if ($responseMessage) {
            $message = $responseMessage;
        }

        parent::__construct($message, $code, $previous);

        /**
         * @var Request
         */
        $request = $response->request;

        $this->parentException = [
            'message' => $responseMessage,
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