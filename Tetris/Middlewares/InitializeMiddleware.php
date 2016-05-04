<?php

namespace Tetris\Middlewares;

use Tetris\Component;
use Slim\Http\Response;
use Slim\Http\Request;
use Tetris\Services\FlagsService;

class InitializeMiddleware extends Component
{
    public function __invoke(Request $req, Response $res, callable $next): Response
    {
        $debugPwd = getenv('DEBUG_PWD');
        /**
         * @var FlagsService $flags
         */
        $flags = $this->container['flags'];

        if (
            getenv('NODE_ENV') !== 'production' ||
            $req->getQueryParam('_debug') === $debugPwd || (
                $req->hasHeader('Referer')
                &&
                strpos($req->getHeader('Referer')[0], "_debug={$debugPwd}") !== FALSE
            )
        ) {
            $flags->enableDebugMode();
        }

        if ($req->hasHeader('accept-language')) {
            $flags->setLocale($req->getHeader('accept-language')[0]);
        }

        return $next($req, $res);
    }
}