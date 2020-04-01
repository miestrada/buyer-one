<?php

namespace App\Tests\API\Buyer\V1;

use App\Entity\Address;
use App\Entity\Buyer;
use App\Tests\APIWebTestCase;
use Symfony\Component\HttpFoundation\Response;


class UserTest extends APIWebTestCase
{

    public function testGetCurrentUser()
    {
        $this->getTestClient()->request('GET', self::API_URL . '/current_user', [], [], ['HTTP_X-AUTH-TOKEN' => $this->getSampleBuyer()->getToken()]);

        $content = json_decode($this->getTestClient()->getResponse()->getContent());

        $this->assertSame(Response::HTTP_OK, $content->status);
        $this->assertSame($this->getSampleBuyer()->getId(), $content->data->id);
        $this->assertSame($this->getSampleBuyer()->getPhone(), $content->data->phone);
        $this->assertSame($this->getSampleBuyer()->getAddresses()->count(), count($content->data->addresses));
        $this->assertSame($this->getSampleBuyer()->getPaymentMethods()->count(), count($content->data->payment_methods));
    }

}