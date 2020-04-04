<?php

namespace App\Tests\API\Buyer\V1;

use App\Security\BuyerAuthenticator;
use App\Tests\APIWebTestCase;
use Symfony\Component\HttpFoundation\Response;


class LoginTest extends APIWebTestCase
{

    public function testGetPing()
    {
        $this->getTestClient()->request('GET', self::API_URL . '/ping');

        $content = json_decode($this->getTestClient()->getResponse()->getContent());

        $this->assertSame('pong', $content->data);
    }

    public function testPostCode()
    {
        $this->getTestClient()->request('POST', self::API_URL . '/code', [
            'phone' => $this->getSamplePhone(),
        ]);

        $content = json_decode($this->getTestClient()->getResponse()->getContent());

        $this->assertSame($this->getSamplePhone(), $content->data->phone);
        $this->assertSame('sms', $content->data->type);
    }

    public function testPostLogin()
    {
        $this->getTestClient()->request('POST', self::API_URL . '/login', [
            'phone' => $this->getSamplePhone(),
            'code' => $this->getSampleCode(),
        ]);

        $content = json_decode($this->getTestClient()->getResponse()->getContent());

        $this->assertSame($this->getSampleBuyer()->getToken(), $content->data->token);
    }

    public function testGetValidateSuccess()
    {
        $this->getTestClient()->request('GET', self::API_URL . '/validate', [], [], ['HTTP_X-AUTH-TOKEN' => $this->getSampleBuyer()->getToken()]);

        $content = json_decode($this->getTestClient()->getResponse()->getContent());

        $this->assertSame(Response::HTTP_OK, $content->status);
        $this->assertEquals($this->getSamplePhone(), $content->data->phone);
        $this->assertTrue($content->data->is_valid);
    }

    public function testGetValidateForbidden()
    {
        $this->getTestClient()->request('GET', self::API_URL . '/validate', [], [], ['HTTP_X-AUTH-TOKEN' => 'INVALID-TOKEN' . uniqid()]);

        $content = json_decode($this->getTestClient()->getResponse()->getContent());

        $this->assertSame(Response::HTTP_FORBIDDEN, $content->status);
        $this->assertSame('BuyerAuthenticator', $content->errors[0]->name);
    }

    public function testGetValidateUnauthorized()
    {
        $this->getTestClient()->request('GET', self::API_URL . '/validate', [], [], []);
        $content = json_decode($this->getTestClient()->getResponse()->getContent());

        $this->assertSame(Response::HTTP_UNAUTHORIZED, $content->status);
        $this->assertSame('BuyerAuthenticator', $content->errors[0]->name);
    }

}