<?php

namespace Tetris\Services;

use Httpful\Response;
use stdClass;
use Tetris\Component;
use Tetris\Exceptions\ApiException;
use Slim\App;
use Slim\Http\Request as ServerRequest;
use Httpful\Request as HttpRequest;
use Httpful\Response as HttpResponse;
use Httpful\Http;

class ApiService extends Component
{
    /**
     * @var ServerRequest
     */
    private $serverRequest;
    /**
     * @var string
     */
    private $accessToken;

    /**
     * @var string $authPrefix
     */
    private $authPrefix = 'Bearer';

    /**
     * ApiService constructor.
     * @param App $app
     */
    public function __construct(App $app)
    {
        parent::__construct($app);

        $this->serverRequest = $this->app->getContainer()['request'];
        $this->accessToken = $this->getAccessTokenFromHeader();

        if (!$this->accessToken) {
            $this->accessToken = $this->getAccessTokenFromCookie();
        }
    }

    /**
     * @return string
     */
    private function getAccessTokenFromCookie(): string
    {
        $cookies = $this->serverRequest->getCookieParams();
        $cookieName = getenv('TOKEN_COOKIE_NAME');

        return empty($cookies[$cookieName])
            ? ''
            : $cookies[$cookieName];
    }

    /**
     * @return string
     */
    private function getAccessTokenFromHeader(): string
    {
        if ($this->serverRequest->hasHeader('Authorization')) {
            $header = $this->serverRequest->getHeader('Authorization')[0];
            $parts = explode(' ', $header);

            if (count($parts) === 1) {
                array_unshift($parts, 'Bearer');
            }

            $this->authPrefix = $parts[0];

            return $parts[1];
        } else {
            return '';
        }
    }

    protected function createRequest(): HttpRequest
    {
        $request = HttpRequest::init();

        if ($this->accessToken) {
            $request->addHeader('Authorization', "{$this->authPrefix} {$this->accessToken}");
        }

        if ($this->serverRequest->hasHeader('Referer')) {
            $request->addHeader('Referer', $this->serverRequest->getHeader('Referer')[0]);
        } else {
            $uri = $this->serverRequest->getUri();
            $referer = getenv('TKM_URL') . $uri->getPath();

            if ($uri->getQuery()) {
                $referer .= '?' . $uri->getQuery();
            }

            $request->addHeader('Referer', $referer);
        }

        return $request;
    }

    /**
     * @param HttpResponse $response
     * @return array
     * @throws ApiException
     */
    protected function parseArrayBody(HttpResponse $response): array
    {
        if (!isset($response->body) || !is_array($response->body)) {
            throw new ApiException($response);
        }
        return $response->body;
    }

    /**
     * @param HttpResponse $response
     * @return \stdClass
     * @throws ApiException
     */
    protected function parseObjectBody(HttpResponse $response): \stdClass
    {
        if (empty($response->body) || $response->body instanceof \stdClass === FALSE) {
            throw new ApiException($response);
        }
        return $response->body;
    }

    /**
     * @param HttpResponse $response
     * @return HttpResponse
     * @throws ApiException
     */
    protected function parseResponse(HttpResponse $response): HttpResponse
    {
        if ($response->code !== 200) {
            throw new ApiException($response);
        }

        return $response;
    }

    function requestUser(): Response
    {
        return $this->createRequest()
            ->method(Http::GET)
            ->uri(getenv('USER_API_URL'))
            ->send();
    }

    function responseToObject(Response $res): stdClass
    {
        return $this->parseObjectBody($this->parseResponse($res));
    }

    function fetchCurrentUser(): stdClass
    {
        return $this->responseToObject($this->requestUser());
    }
}