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
class LoginController extends APIController
{

    /**
     * @Route("/ping", methods={"GET|HEAD"})
     */
    function getPing()
    {
        return $this->json('pong');
    }

    /**
     * @Route("/code", methods={"POST"})
     * @param Request $request
     * @return JsonResponse
     */
    function postCode(Request $request)
    {
        $phone = trim($request->get('phone'));

        /** @var Buyer $buyer */
        $buyer = $this->entityManager->getRepository(Buyer::class)->findOneBy(['phone' => $phone]);
        $isNew = $buyer === null;

        if ($isNew) {
            $buyer = new Buyer();
            $buyer
                ->setPhone($phone)
                ->setRoles(['ROLE_USER']);
        }

        $buyer->setCode('1111');

        $violations = $this->validator->validate($buyer);
        if ($violations->count())
            return $this->notAcceptable($violations);

        $this->entityManager->persist($buyer);
        $this->entityManager->flush();

        return $this->json([
            'phone' => $buyer->getPhone(),
            'type' => 'sms',
            //'code' => $buyer->getCode(),
            'is_new' => $isNew,
        ]);
    }

    /**
     * @Route("/login", methods={"POST"})
     * @param Request $request
     * @return JsonResponse
     */
    function postLogin(Request $request)
    {
        $phone = trim($request->get('phone'));
        $code = trim($request->get('code'));

        /** @var Buyer $buyer */
        $buyer = $this->entityManager->getRepository(Buyer::class)->findOneBy([
            'phone' => $phone,
            'code' => $code,
        ]);

        if (!$buyer)
            return $this->notFound(Buyer::class);

        $buyer->setToken(bin2hex(openssl_random_pseudo_bytes(64)));

        $errors = $this->validator->validate($buyer);
        if ($errors->count())
            return $this->notAcceptable($errors);

        $this->entityManager->persist($buyer);
        $this->entityManager->flush();

        return $this->json([
            'token' => $buyer->getToken(),
        ]);
    }

    /**
     * @Route("/validate", methods={"GET"})
     * @param Request $request
     * @return JsonResponse
     */
    function getValidate(Request $request)
    {
        return $this->json([
            'phone' => $this->getUser()->getPhone(),
            'is_valid' => true,
        ]);
    }

}