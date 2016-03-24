<?php

namespace Tetris\Middlewares;

use Tetris\Component;
use Slim\Http\Response;
use Slim\Http\Request;

class InitializeMiddleware extends Component
{
    public function __invoke(Request $req, Response $res, callable $next): Response
    {
        $debugPwd = getenv('DEBUG_PWD');

        if (
            $req->getQueryParam('_debug') === $debugPwd
            || (
                $req->hasHeader('Referer')
                &&
                strpos($req->getHeader('Referer')[0], "_debug={$debugPwd}") !== FALSE
            )
        ) {
            $this->container['flags']->isDebugMode = true;
        }

        return $next($req, $res);
    }
}