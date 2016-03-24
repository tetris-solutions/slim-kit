<?php

namespace Tetris\Services;

use Tetris\Component;
use Slim\Http\Response;
use Slim\Http\Request;
use Slim\Container;

class ErrorHandlerService extends Component
{
    /**
     * @param Container $container
     * @return callable
     */
    public function __invoke(Container $container): callable
    {
        /**
         * @param Request $req
         * @param Response $res
         * @param \Exception $exception
         * @returns Response
         */
        return function (Request $req, Response $res, $exception) use ($container) : Response {
            $thrown = ['message' => 'Application error'];

            if ($container['flags']->isDebugMode) {
                $thrown['stack'] = $exception->getTraceAsString();
                $throw['code'] = $exception->getCode();

                if ($exception instanceof ApiException) {
                    $thrown['parentException'] = $exception->parentException;
                    $thrown['message'] = $exception->getMessage();
                }
            }

            $code = 500;

            if ($exception->getCode() >= 400 && $exception->getCode() < 600) {
                $code = $exception->getCode();
            }

            return $container['response']->withJson($thrown, $code);
        };
    }
}
