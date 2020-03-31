<?php

namespace App\Utilities\QR;

class QRCode
{
    # https://developers.google.com/chart/infographics/docs/qr_codes?csw=1

    const QR_API_URL_PATTERN = 'https://chart.googleapis.com/chart?cht=qr&choe=UTF-8&chld=M|0&chs=%ux%u&chl=%s';

    const QR_DEFAULT_SIZE = 200;

    private string $data;

    private int $width;

    private int $height;

    public function __construct(?string $data = null, ?int $size = null)
    {
        $this
            ->setData($data)
            ->setWidth($size ? $size : self::QR_DEFAULT_SIZE)
            ->setHeight($size ? $size : self::QR_DEFAULT_SIZE);
    }

    public function getData(): string
    {
        return $this->data;
    }

    public function setData(string $data): self
    {
        $this->data = $data;
        return $this;
    }

    public function getWidth(): int
    {
        return $this->width;
    }

    public function setWidth(int $width): self
    {
        $this->width = $width > 0 ? $width : self::QR_DEFAULT_SIZE;
        return $this;
    }

    public function getHeight(): int
    {
        return $this->height;
    }

    public function setHeight(int $height): self
    {
        $this->height = $height > 0 ? $height : self::QR_DEFAULT_SIZE;
        return $this;
    }

    public function getUrl(): string
    {
        return sprintf(self::QR_API_URL_PATTERN,
            $this->getWidth(),
            $this->getHeight(),
            urlencode($this->getData()));
    }

    public function getBinary(): string
    {
        return file_get_contents($this->getUrl());
    }

}