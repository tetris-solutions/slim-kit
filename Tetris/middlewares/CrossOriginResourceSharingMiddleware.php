<?php

namespace Tetris;

use Slim\Http\Response;
use Slim\Http\Request;

class CrossOriginResourceSharingMiddleware extends Component
{
    public function __invoke(Request $req, Response $res, callable $next): Response
    {
        if ($req->hasHeader('Access-Control-Expose-Headers')) {
            $res = $res->withHeader(
                'Access-Control-Expose-Headers',
                $req->getHeader('Access-Control-Expose-Headers')[0]
            );
        }

        return $next($req, $res);
    }
}