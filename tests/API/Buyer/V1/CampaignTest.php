<?php

namespace App\Tests\API\Buyer\V1;

use App\Entity\Campaign;
use App\Tests\APIWebTestCase;
use Symfony\Component\HttpFoundation\Response;


class CampaignTest extends APIWebTestCase
{

    public function testGetCampaign()
    {
        $campaigns = $this->getEntityManager()->getRepository(Campaign::class)->findAll();

        if (!count($campaigns))
            $this->markTestIncomplete('No campaigns found');

        /** @var Campaign $campaign */
        $campaign = end($campaigns);

        $this->getTestClient()->request('GET', self::API_URL . '/campaign', [
            'uuid' => $campaign->getUuid(),
        ], [], ['HTTP_X-AUTH-TOKEN' => $this->getSampleBuyer()->getToken()]);

        $content = json_decode($this->getTestClient()->getResponse()->getContent());

        $this->assertSame(Response::HTTP_OK, $content->status);
        $this->assertSame($campaign->getId(), $content->data->id);
        $this->assertSame($campaign->getUuid(), $content->data->uuid);
        $this->assertSame($campaign->getName(), $content->data->name);
        $this->assertSame($campaign->getProduct()->getId(), $content->data->product->id);
        $this->assertSame($campaign->getProduct()->getUser()->getName(), $content->data->product->seller->name);
    }

}