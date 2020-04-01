<?php

namespace App\Tests\API\Buyer\V1;

use App\Entity\Address;
use App\Entity\Buyer;
use App\Entity\CreditCard;
use App\Tests\APIWebTestCase;
use Symfony\Component\HttpFoundation\Response;


class PaymentMethodTest extends APIWebTestCase
{

    public function testGetPaymentMethod()
    {
        $creditCard = new CreditCard();
        $creditCard
            ->setBuyer($this->getSampleBuyer())
            ->setName('Get Name')
            ->setNumber('4111111111111111')
            ->setExpires('205012')
            ->setCvv('000');

        $this->getEntityManager()->persist($creditCard);
        $this->getEntityManager()->flush();

        $this->getTestClient()->request('GET', self::API_URL . '/payment_method', [
            'id' => $creditCard->getId(),
        ], [], ['HTTP_X-AUTH-TOKEN' => $this->getSampleBuyer()->getToken()]);

        $content = json_decode($this->getTestClient()->getResponse()->getContent());

        $this->assertSame(Response::HTTP_OK, $content->status);
        $this->assertSame($creditCard->getId(), $content->data->id);
        $this->assertSame($creditCard->getCvv(), $content->data->cvv);

        $this->getEntityManager()->remove($creditCard);
        $this->getEntityManager()->flush();
    }

    public function testPostCreditCard()
    {
        $this->getTestClient()->request('POST', self::API_URL . '/credit_card', [
            'name' => 'Post Name',
            'number' => '4111111111111111',
            'expires' => '205012',
            'cvv' => '000'
        ], [], ['HTTP_X-AUTH-TOKEN' => $this->getSampleBuyer()->getToken()]);

        $content = json_decode($this->getTestClient()->getResponse()->getContent());

        $this->assertSame(Response::HTTP_OK, $content->status);
        $this->assertGreaterThan(0, $content->data->id);
        $this->assertSame('Post Name', $content->data->name);

        $creditCard = $this->getEntityManager()->getRepository(CreditCard::class)->find($content->data->id);
        $this->getEntityManager()->remove($creditCard);
        $this->getEntityManager()->flush();
    }

    public function testPutCreditCard()
    {
        $creditCard = new CreditCard();
        $creditCard
            ->setBuyer($this->getSampleBuyer())
            ->setName('Get Name')
            ->setNumber('4111111111111111')
            ->setExpires('2050/12')
            ->setCvv('000');

        $this->getEntityManager()->persist($creditCard);
        $this->getEntityManager()->flush();

        $this->getTestClient()->request('PUT', self::API_URL . '/credit_card', [
            'id' => $creditCard->getId(),
            'name' => 'Modified',
            'number' => '4012888888881881',
            'expires' => '205001',
            'cvv' => '111',
        ], [], ['HTTP_X-AUTH-TOKEN' => $this->getSampleBuyer()->getToken()]);

        $content = json_decode($this->getTestClient()->getResponse()->getContent());

        /** @var CreditCard $creditCard */
        $creditCard = $this->getEntityManager()->getRepository(CreditCard::class)->find($creditCard->getId());

        $this->assertSame(Response::HTTP_OK, $content->status);
        $this->assertSame($creditCard->getId(), $content->data->id);
        $this->assertSame($creditCard->getName(), $content->data->name);
        $this->assertSame($creditCard->getNumber(), $content->data->number);
        $this->assertSame($creditCard->getExpires(), $content->data->expires);
        $this->assertSame($creditCard->getCvv(), $content->data->cvv);

        $this->getEntityManager()->remove($creditCard);
        $this->getEntityManager()->flush();
    }
}