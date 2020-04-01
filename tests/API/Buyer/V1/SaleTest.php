<?php

namespace App\Tests\API\Buyer\V1;

use App\Entity\Address;
use App\Entity\Buyer;
use App\Entity\Campaign;
use App\Entity\Item;
use App\Entity\Sale;
use App\Tests\APIWebTestCase;
use Symfony\Component\HttpFoundation\Response;


class SaleTest extends APIWebTestCase
{

    public function testGetSale()
    {
        $campaigns = $this->getEntityManager()->getRepository(Campaign::class)->findAll();

        if (!count($campaigns))
            $this->markTestIncomplete('No campaigns found');

        /** @var Campaign $campaign */
        $campaign = end($campaigns);

        $item = new Item();
        $item
            ->setCampaign($campaign)
            ->setPrice($campaign->getProduct()->getPrice())
            ->setQuantity(1);

        $sale = new Sale();
        $sale
            ->setBuyer($this->getSampleBuyer())
            ->setAddress($this->getSampleBuyer()->getAddresses()->last())
            ->setPaymentMethod($this->getSampleBuyer()->getPaymentMethods()->last())
            ->addItem($item);

        $this->getEntityManager()->persist($sale);
        $this->getEntityManager()->flush();

        $this->getTestClient()->request('GET', self::API_URL . '/sale', [
            'id' => $sale->getId(),
        ], [], ['HTTP_X-AUTH-TOKEN' => $this->getSampleBuyer()->getToken()]);

        $content = json_decode($this->getTestClient()->getResponse()->getContent());

        $this->assertSame(Response::HTTP_OK, $content->status);
        $this->assertSame($sale->getId(), $content->data->id);
        $this->assertSame($sale->getAddress()->getId(), $content->data->address->id);
        $this->assertSame($sale->getPaymentMethod()->getId(), $content->data->payment_method->id);
        $this->assertSame($sale->getItems()->count(), count($content->data->items));

        $this->getEntityManager()->remove($item);
        $this->getEntityManager()->remove($sale);
        $this->getEntityManager()->flush();
    }

    public function testPostSale()
    {
        $campaigns = $this->getEntityManager()->getRepository(Campaign::class)->findAll();

        if (!count($campaigns))
            $this->markTestIncomplete('No campaigns found');

        /** @var Campaign $campaign */
        $campaign = end($campaigns);

        $this->getTestClient()->request('POST', self::API_URL . '/sale', [
            'uuid' => $campaign->getUuid(),
            'address_id' => $this->getSampleBuyer()->getAddresses()->last()->getId(),
            'payment_method_id' => $this->getSampleBuyer()->getPaymentMethods()->last()->getId(),
            'quantity' => 7,
        ], [], ['HTTP_X-AUTH-TOKEN' => $this->getSampleBuyer()->getToken()]);

        $content = json_decode($this->getTestClient()->getResponse()->getContent());

        $this->assertSame(Response::HTTP_OK, $content->status);
        $this->assertGreaterThan(0, $content->data->id);
        $this->assertSame($this->getSampleBuyer()->getAddresses()->last()->getId(), $content->data->address->id);
        $this->assertSame($this->getSampleBuyer()->getPaymentMethods()->last()->getId(), $content->data->payment_method->id);
        $this->assertSame(1, count($content->data->items));
        $this->assertSame(7, $content->data->items[0]->quantity);
    }

}