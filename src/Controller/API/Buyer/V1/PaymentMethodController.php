<?php

namespace App\Controller\API\Buyer\V1;

use App\Controller\APIController;
use App\Entity\Address;

use App\Entity\Buyer;
use App\Entity\CreditCard;
use App\Entity\PaymentMethod;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class IndexController
 * @package App\Controller\API
 * @Route("/api/buyer/v1")
 */
class PaymentMethodController extends APIController
{

    /**
     * @Route("/payment_method", methods={"GET"})
     * @param Request $request
     * @return JsonResponse
     */
    function getPaymentMethod(Request $request)
    {
        $paymentMethodId = (int)$request->get('id', 0);

        /** @var Address $address */
        $paymentMethod = $this->entityManager->getRepository(PaymentMethod::class)->findOneBy([
            'id' => $paymentMethodId,
            'buyer' => $this->getUser(),
        ]);

        if (!$paymentMethod)
            return $this->notFound(PaymentMethod::class, ['id' => $paymentMethodId]);

        return $this->json(
            $paymentMethod->cast()
        );
    }

    /**
     * @Route("/credit_card", methods={"POST"})
     * @param Request $request
     * @return JsonResponse
     */
    function postCreditCard(Request $request)
    {
        $creditCard = new CreditCard();

        $creditCard
            ->setBuyer($this->getUser())
            ->setName($request->get('name', ""))
            ->setNumber($request->get('number', ""))
            ->setExpires($request->get('expires', ""))
            ->setCvv($request->get('cvv', ""));

        $violations = $this->validator->validate($creditCard);
        if ($violations->count())
            return $this->notAcceptable($violations);

        $this->entityManager->persist($creditCard);
        $this->entityManager->flush();

        return $this->json(
            $creditCard->cast()
        );
    }

    /**
     * @Route("/credit_card", methods={"PUT"})
     * @param Request $request
     * @return JsonResponse
     */
    function putCreditCard(Request $request)
    {
        $creditCardId = (int)$request->get('id', 0);

        /** @var CreditCard $creditCard */
        $creditCard = $this->entityManager->getRepository(CreditCard::class)->findOneBy([
            'id' => $creditCardId,
            'buyer' => $this->getUser(),
        ]);

        if (!$creditCard)
            return $this->notFound(CreditCard::class, ['id' => $creditCardId]);

        $creditCard
            ->setName($request->get('name', ""))
            ->setNumber($request->get('number', ""))
            ->setExpires($request->get('expires', ""))
            ->setCvv($request->get('cvv', ""));

        $violations = $this->validator->validate($creditCard);
        if ($violations->count())
            return $this->notAcceptable($violations);

        $this->entityManager->persist($creditCard);
        $this->entityManager->flush();

        return $this->json(
            $creditCard->cast()
        );
    }
}