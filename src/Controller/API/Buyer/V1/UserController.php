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
class UserController extends APIController
{

    /**
     * @Route("/current_user", methods={"GET"})
     * @return JsonResponse
     */
    function getCurrentUser()
    {
        /** @var Buyer $buyer */
        $buyer = $this->getUser();
        if (!$buyer)
            return $this->notFound(Buyer::class);

        $addresses = [];
        foreach ($buyer->getAddresses() as $address)
            $addresses[] = $address->cast();

        $paymentMethods = [];
        foreach ($buyer->getPaymentMethods() as $paymentMethod)
            $paymentMethods[] = $paymentMethod->cast();

        return $this->json([
            'id' => $buyer->getId(),
            'phone' => $buyer->getPhone(),
            'email' => $buyer->getEmail(),
            'addresses' => $addresses,
            'payment_methods' => $paymentMethods
        ]);
    }

}