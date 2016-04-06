<?php

namespace Tetris\Services;

use Tetris\Component;

class FlagsService extends Component
{
    /**
     * @var bool
     */
    private $debugMode = FALSE;

    /**
     * @var string|null
     */
    private $redirectUrl = NULL;

    public function enableDebugMode()
    {
        $this->debugMode = true;
    }

    public function isDebugMode(): bool
    {
        return $this->debugMode;
    }

    public function setRedirectUrl(string $url)
    {
        $this->redirectUrl = $url;
    }

    public function isRedirectMode(): bool
    {
        return isset($this->redirectUrl);
    }

    public function getRedirectUrl()
    {
        return $this->redirectUrl;
    }
}