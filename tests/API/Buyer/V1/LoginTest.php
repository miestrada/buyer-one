<?php

namespace App\Tests\API\Buyer\V1;

use App\Tests\APIWebTestCase;


class LoginTest extends APIWebTestCase
{

    public function testGetPing()
    {
        $this->getTestClient()->request('GET', self::API_URL . '/ping');

        $content = json_decode($this->getTestClient()->getResponse()->getContent());

        $this->assertSame('pong', $content->data);
    }

    public function testPostLogin()
    {
        $this->getTestClient()->request('POST', self::API_URL . '/login', [
            'phone' => $this->getSamplePhone(),
        ]);

        $content = json_decode($this->getTestClient()->getResponse()->getContent());

        $this->assertSame($this->getSamplePhone(), $content->data->phone);
        $this->assertSame($this->getSampleCode(), $content->data->code);
    }

    public function testPostToken()
    {
        $this->getTestClient()->request('POST', self::API_URL . '/token', [
            'phone' => $this->getSamplePhone(),
            'code' => $this->getSampleCode(),
        ]);

        $content = json_decode($this->getTestClient()->getResponse()->getContent());

        $this->assertSame($this->getSampleBuyer()->getToken(), $content->data->token);
    }

}