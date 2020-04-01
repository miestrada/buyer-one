<?php

namespace App\Controller\API\Buyer\V1;

use App\Controller\APIController;
use App\Entity\Address;

use App\Entity\Buyer;
use App\Entity\Campaign;
use App\Entity\CreditCard;
use App\Entity\Item;
use App\Entity\Payment;
use App\Entity\PaymentMethod;

use App\Entity\Sale;
use App\Repository\CampaignRepository;
use App\Repository\SaleRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class SaleController
 * @package App\Controller\API
 * @Route("/api/buyer/v1")
 */
class SaleController extends APIController
{

    /**
     * @Route("/sale", methods={"GET"})
     * @param Request $request
     * @return JsonResponse
     */
    function getSale(Request $request)
    {
        $saleId = (int)$request->get('id', 0);

        /** @var Sale $sale */
        $sale = $this->entityManager->getRepository(Sale::class)->findOneBy([
            'id' => $saleId,
            'buyer' => $this->getUser(),
        ]);

        if (!$sale)
            return $this->notFound(Sale::class, ['id' => $sale]);

        return $this->json(
            $sale->cast()
        );
    }

    /**
     * @Route("/sale", methods={"POST"})
     * @param Request $request
     * @return JsonResponse
     */
    function postSale(Request $request)
    {
        $campaignUuid = $request->get('uuid');
        $addressId = $request->get('address_id', 0);
        $paymentMethodId = $request->get('payment_method_id', 0);

        // Campaign
        /** @var Campaign $campaign */
        $campaign = $this->entityManager->getRepository(Campaign::class)->findOneBy([
            'uuid' => $campaignUuid,
        ]);

        if (!$campaign)
            return $this->notFound(Campaign::class, ['uuid' => $campaignUuid]);

        // Item
        $item = new Item();
        $item
            ->setCampaign($campaign)
            ->setPrice($campaign->getProduct()->getPrice())
            ->setQuantity($request->get('quantity', 0));

        $violations = $this->validator->validate($item);
        if ($violations->count())
            return $this->notAcceptable($violations);

        /** @var Address $address */
        $address = $this->entityManager->getRepository(Address::class)->findOneBy([
            'id' => $addressId,
            'buyer' => $this->getUser(),
        ]);

        if (!$address)
            return $this->notFound(Address::class, ['id' => $addressId]);

        /** @var PaymentMethod $paymentMethod */
        $paymentMethod = $this->entityManager->getRepository(PaymentMethod::class)->findOneBy([
            'id' => $paymentMethodId,
            'buyer' => $this->getUser(),
        ]);

        if (!$paymentMethod)
            return $this->notFound(PaymentMethod::class, ['id' => $paymentMethodId]);

        // Sale
        $sale = new Sale();
        $sale
            ->setBuyer($this->getUser())
            ->addItem($item)
            ->setAddress($address)
            ->setPaymentMethod($paymentMethod);

        $violations = $this->validator->validate($sale);
        if ($violations->count())
            return $this->notAcceptable($violations);

        // Process
        $this->entityManager->persist($sale);
        $this->entityManager->flush();

        return $this->json(
            $sale->cast(),
        );
    }

}