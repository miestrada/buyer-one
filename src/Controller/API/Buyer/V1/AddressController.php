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
class AddressController extends APIController
{

    /**
     * @Route("/address", methods={"GET"})
     * @param Request $request
     * @return JsonResponse
     */
    function getAddress(Request $request)
    {
        $addressId = (int)$request->get('id', 0);

        /** @var Address $address */
        $address = $this->entityManager->getRepository(Address::class)->findOneBy([
            'id' => $addressId,
            'buyer' => $this->getUser(),
        ]);

        if (!$address)
            return $this->notFound(Address::class, ['id' => $addressId]);

        return $this->json(
            $address->cast()
        );
    }

    /**
     * @Route("/address", methods={"POST"})
     * @param Request $request
     * @return JsonResponse
     */
    function postAddress(Request $request)
    {
        $address = new Address();

        $address
            ->setBuyer($this->getUser())
            ->setName($request->get('name'))
            ->setCountry($request->get('country', ""))
            ->setPostCode($request->get('post_code', ""))
            ->setLine1($request->get('line1', ""))
            ->setLine2($request->get('line2'))
            ->setCity($request->get('city', ""))
            ->setState($request->get('state'))
            ->setNotes($request->get('notes'));

        $violations = $this->validator->validate($address);
        if ($violations->count())
            return $this->notAcceptable($violations);

        $this->entityManager->persist($address);
        $this->entityManager->flush();

        return $this->json(
            $address->cast()
        );
    }

    /**
     * @Route("/address", methods={"PUT"})
     * @param Request $request
     * @return JsonResponse
     */
    function putAddress(Request $request)
    {
        $addressId = (int)$request->get('id', 0);

        /** @var Address $address */
        $address = $this->entityManager->getRepository(Address::class)->findOneBy([
            'id' => $addressId,
            'buyer' => $this->getUser(),
        ]);

        if (!$address)
            return $this->notFound(Address::class, ['id' => $addressId]);

        $address
            ->setName($request->get('name', $address->getName()))
            ->setCountry($request->get('country', $address->getCountry()))
            ->setPostCode($request->get('post_code', $address->getPostCode()))
            ->setLine1($request->get('line1', $address->getLine1()))
            ->setLine2($request->get('line2', $address->getLine2()))
            ->setCity($request->get('city', $address->getCity()))
            ->setState($request->get('state', $address->getState()))
            ->setNotes($request->get('notes', $address->getNotes()));

        $violations = $this->validator->validate($address);
        if ($violations->count())
            return $this->notAcceptable($violations);

        $this->entityManager->persist($address);
        $this->entityManager->flush();

        return $this->json(
            $address->cast()
        );
    }

}