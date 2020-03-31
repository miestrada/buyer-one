<?php

namespace App\Controller\API\Buyer\V1;

use App\Controller\APIController;
use App\Entity\Address;
use App\Entity\Buyer;
use App\Entity\Campaign;
use App\Entity\Product;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * Class IndexController
 * @package App\Controller\API
 * @Route("/api/buyer/v1")
 */
class CampaignController extends APIController
{

    /**
     * @Route("/campaign", methods={"GET"})
     * @param Request $request
     * @return JsonResponse
     */
    function getCampaign(Request $request)
    {
        $campaignUuid = (int)$request->get('uuid');

        /** @var Campaign $campaign */
        $campaign = $this->entityManager->getRepository(Campaign::class)->findOneBy([
            'uuid' => $campaignUuid,
        ]);

        if (!$campaign)
            return $this->notFound(Campaign::class, ['uuid' => $campaignUuid]);

        /** @var Product $product */
        $product = $campaign->getProduct();

        /** @var User $user */
        $user = $product->getUser();

        return $this->json([
            'id' => $campaign->getId(),
            'uuid' => $campaign->getUuid(),
            'name' => $campaign->getName(),
            'product' => [
                'id' => $product->getId(),
                'name' => $product->getName(),
                'description' => $product->getDescription(),
                'price' => $product->getPrice(),
                'images' => [
                    $product->getImage(),
                ],
                'seller' => [
                    'name' => $user->getName(),
                ]
            ],
        ]);
    }

}