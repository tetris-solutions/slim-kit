<?php

namespace Tetris\Services;

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
            return str_replace('Bearer ', '', $this->serverRequest->getHeader('Authorization')[0]);
        } else {
            return '';
        }
    }

    protected function createRequest(): HttpRequest
    {
        $request = HttpRequest::init();

        if ($this->accessToken) {
            $request->addHeader('Authorization', "Bearer {$this->accessToken}");
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
     * @return \stdClass
     * @throws ApiException
     */
    protected function parseBody(HttpResponse $response): \stdClass
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

    public function fetchCurrentUser(): \stdClass
    {
        $response = $this->createRequest()
            ->method(Http::GET)
            ->uri(getenv('USER_API_URL'))
            ->send();

        return $this->parseBody($this->parseResponse($response));
    }
}