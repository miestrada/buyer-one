<?php

namespace App\Utilities\User;

use Exception;

class UserDevice
{

    public const DEVICE_DEFAULT_WEB = 'LINK_DEFAULT_WEB';

    public const DEVICE_GOOGLE_PLAY = 'LINK_GOOGLE_PLAY';

    public const DEVICE_APPLE_STORE = 'LINK_APPLE_STORE';

    private string $userAgent;

    public function __construct(string $userAgent)
    {
        $this->setUserAgent($userAgent);
    }

    private function setUserAgent(string $userAgent)
    {
        $this->userAgent = trim($userAgent);
    }

    private function getUserAgent(): string
    {
        return $this->userAgent;
    }

    public function getStore(): string
    {
        if ($this->isAndroid())
            return self::DEVICE_GOOGLE_PLAY;
        elseif ($this->isIos())
            return self::DEVICE_APPLE_STORE;
        else
            return self::DEVICE_DEFAULT_WEB;
    }

    private function isAndroid(): bool
    {
        return stripos($this->getUserAgent(), "Android");
    }

    private function isIos(): bool
    {
        return $this->isIPhone() || $this->isIPad() || $this->isIPod();
    }

    private function isIPhone(): bool
    {
        return stripos($this->getUserAgent(), "iPhone");
    }

    private function isIPad(): bool
    {
        return stripos($this->getUserAgent(), "iPad");
    }

    private function isIPod(): bool
    {
        return stripos($this->getUserAgent(), "iPad");
    }

}