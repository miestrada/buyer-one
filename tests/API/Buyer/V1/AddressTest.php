<?php

namespace App\Tests\API\Buyer\V1;

use App\Entity\Address;
use App\Entity\Buyer;
use App\Tests\APIWebTestCase;
use Symfony\Component\HttpFoundation\Response;


class AddressTest extends APIWebTestCase
{

    public function testGetAddress()
    {
        $address = new Address();
        $address
            ->setBuyer($this->getSampleBuyer())
            ->setName('Get Address Test')
            ->setCountry('ES')
            ->setPostCode('08840')
            ->setLine1('Line1')
            ->setCity('Viladecans');

        $this->getEntityManager()->persist($address);
        $this->getEntityManager()->flush();

        $this->getTestClient()->request('GET', self::API_URL . '/address', [
            'id' => $address->getId(),
        ], [], ['HTTP_X-AUTH-TOKEN' => $this->getSampleBuyer()->getToken()]);

        $content = json_decode($this->getTestClient()->getResponse()->getContent());

        $this->assertSame(Response::HTTP_OK, $content->status);
        $this->assertSame($address->getId(), $content->data->id);
        $this->assertSame($address->getName(), $content->data->name);

        $this->getEntityManager()->remove($address);
        $this->getEntityManager()->flush();
    }

    public function testPostAddress()
    {
        $this->getTestClient()->request('POST', self::API_URL . '/address', [
            'name' => 'Post Address Test',
            'country' => 'ES',
            'post_code' => '08840',
            'line1' => 'Address Line 1',
            'line2' => 'Address Line 2',
            'city' => 'Viladecans',
            'state' => 'Barcelona',
            'notes' => 'Leave parcel to the kindest neighbour',
        ], [], ['HTTP_X-AUTH-TOKEN' => $this->getSampleBuyer()->getToken()]);

        $content = json_decode($this->getTestClient()->getResponse()->getContent());

        $this->assertSame(Response::HTTP_OK, $content->status);
        $this->assertGreaterThan(0, $content->data->id);
        $this->assertSame('Post Address Test', $content->data->name);

        $address = $this->getEntityManager()->getRepository(Address::class)->find($content->data->id);
        $this->getEntityManager()->remove($address);
        $this->getEntityManager()->flush();
    }

    public function testPutAddress()
    {
        $address = new Address();
        $address
            ->setBuyer($this->getSampleBuyer())
            ->setName('Put Address Test')
            ->setCountry('ES')
            ->setPostCode('08840')
            ->setLine1('Line1')
            ->setCity('Viladecans');

        $this->getEntityManager()->persist($address);
        $this->getEntityManager()->flush();

        $this->getTestClient()->request('PUT', self::API_URL . '/address', [
            'id' => $address->getId(),
            'name' => 'Modified',
            'post_code' => '12345',
        ], [], ['HTTP_X-AUTH-TOKEN' => $this->getSampleBuyer()->getToken()]);

        $content = json_decode($this->getTestClient()->getResponse()->getContent());

        /** @var Address $address */
        $address = $this->getEntityManager()->getRepository(Address::class)->find($address->getId());

        $this->assertSame(Response::HTTP_OK, $content->status);
        $this->assertSame($address->getId(), $content->data->id);
        $this->assertSame($address->getName(), $content->data->name);
        $this->assertSame($address->getPostCode(), $content->data->post_code);

        $this->getEntityManager()->remove($address);
        $this->getEntityManager()->flush();
    }
}