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
     * @var string $locale
     */
    private $locale = 'en';

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

    public function setLocale($locale)
    {
        $availableLocales = defined('AVAILABLE_LOCALES')
            ? constant('AVAILABLE_LOCALES')
            : ['en', 'pt-BR'];

        if (in_array($locale, $availableLocales)) {
            $this->locale = $locale;
        }
    }

    public function getLocale(): string
    {
        return $this->locale;
    }
}