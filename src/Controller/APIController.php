<?php

namespace App\Controller;

use App\Entity\Buyer;
use App\Entity\User;
use App\Utilities\Payment\PaymentTypeManager;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\Persistence\ObjectManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class APIController extends AbstractController implements APIJsonRequestInterface
{

    /** @var EntityManagerInterface $entityManager */
    protected EntityManagerInterface $entityManager;

    /** @var ValidatorInterface $validator */
    protected ValidatorInterface $validator;

    /** @var PaymentTypeManager $paymentTypeManager */
    protected PaymentTypeManager $paymentTypeManager;


    public function __construct(
        EntityManagerInterface $entityManager = null,
        ValidatorInterface $validator = null,
        PaymentTypeManager $paymentTypeManager = null
    )
    {
        $this->entityManager = $entityManager;
        $this->validator = $validator;
        $this->paymentTypeManager = $paymentTypeManager;
    }

    /**
     * @return Buyer
     */
    public function getUser()
    {
        return parent::getUser();
    }


    function json($data, int $status = Response::HTTP_OK, array $headers = [], array $context = []): JsonResponse
    {
        /** @var Request $request */
        $request = $this->get('request_stack')->getCurrentRequest();

        $data = [
            'response' => (float)number_format(microtime(true) - $request->server->get('REQUEST_TIME_FLOAT'), 8),
            'status' => $status,
            'data' => $data,
        ];

        return parent::json($data, $status, $headers, $context);
    }

    public function error($error, int $status = Response::HTTP_NOT_ACCEPTABLE, array $headers = [], array $context = []): JsonResponse
    {
        /** @var Request $request */
        $request = $this->get('request_stack')->getCurrentRequest();

        $data = [
            'response' => (float)number_format(microtime(true) - $request->server->get('REQUEST_TIME_FLOAT'), 8),
            'status' => $status,
            'errors' => array_key_exists(0, $error) && is_array($error[0]) ? $error : [$error],
        ];

        return parent::json($data, $status, $headers, $context);
    }

    public function notFound(string $class, array $details = [], int $status = Response::HTTP_NOT_FOUND): JsonResponse
    {
        try {
            $class = (new \ReflectionClass($class))->getShortName();
        } catch (\ReflectionException $e) {
            $class = '?';
        }

        return $this->error([
            'type' => 'object',
            'name' => $class,
            'message' => sprintf('%s not found', $class),
            'details' => $details,
        ], $status, [], []);
    }

    public function notAcceptable(ConstraintViolationListInterface $violations, int $status = Response::HTTP_NOT_ACCEPTABLE): JsonResponse
    {
        $errors = [];
        foreach ($violations as $violation)
            $errors[] = [
                'type' => 'param',
                'name' => $violation->getPropertyPath(),
                'code' => $violation->getCode(),
                'message' => $violation->getMessage(),
            ];

        return $this->error($errors, $status, [], []);
    }
}