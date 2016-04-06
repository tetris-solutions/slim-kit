<?php

namespace Tetris\Services;

use Slim\Http\Uri;
use Tetris\Exceptions\ApiException;
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

            if ($exception instanceof ApiException) {
                $thrown['message'] = $exception->getMessage();
            }

            /**
             * @var FlagsService $flags
             */
            $flags = $this->container['flags'];

            if ($flags->isDebugMode()) {
                $thrown['stack'] = $exception->getTraceAsString();
                $thrown['code'] = $exception->getCode();
                $thrown['message'] = $exception->getMessage();

                if (!empty($exception->parentException)) {
                    $thrown['parentException'] = $exception->parentException;
                }
            }

            $code = 500;

            if ($exception->getCode() >= 400 && $exception->getCode() < 600) {
                $code = $exception->getCode();
            }
            /**
             * @var Response $response
             */
            $response = $container['response'];

            if ($flags->isRedirectMode()) {

                $uri = Uri::createFromString($flags->getRedirectUrl())
                    ->withQuery("error=" . base64_encode("{$exception->getCode()} - {$exception->getMessage()}"));

                return $res->withRedirect($uri);
            }

            return $response->withJson($thrown, $code);
        };
    }
}
