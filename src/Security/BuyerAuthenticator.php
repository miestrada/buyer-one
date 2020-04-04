<?php

namespace App\Security;

use App\Controller\APIController;
use App\Entity\Buyer;
use App\Entity\Campaign;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Guard\AbstractGuardAuthenticator;

class BuyerAuthenticator extends AbstractGuardAuthenticator
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * Called on every request to decide if this authenticator should be
     * used for the request. Returning false will cause this authenticator
     * to be skipped.
     * @param Request $request
     * @return bool
     */
    public function supports(Request $request)
    {
        return $request->headers->has('X-AUTH-TOKEN');
    }

    /**
     * Called on every request. Return whatever credentials you want to
     * be passed to getUser() as $credentials.
     * @param Request $request
     * @return array
     */
    public function getCredentials(Request $request)
    {
        return [
            'token' => $request->headers->get('X-AUTH-TOKEN'),
        ];
    }

    public function getUser($credentials, UserProviderInterface $userProvider)
    {
        $token = $credentials['token'];

        if (null === $token) {
            return;
        }

        // if a User object, checkCredentials() is called
        return $this->entityManager->getRepository(Buyer::class)
            ->findOneBy(['token' => $token]);
    }

    public function checkCredentials($credentials, UserInterface $user)
    {
        // check credentials - e.g. make sure the password is valid
        // no credential check is needed in this case

        // return true to cause authentication success
        return true;
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, $providerKey)
    {
        // on success, let the request continue
        return null;
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception)
    {
        try {
            $class = (new \ReflectionClass(self::class))->getShortName();
        } catch (\ReflectionException $e) {
            $class = '?';
        }

        $data = [
            'response' => '',
            'status' => Response::HTTP_FORBIDDEN,
            'errors' =>
                [
                    [
                        'type' => 'auth',
                        'name' => $class,
                        'message' => strtr($exception->getMessageKey(), $exception->getMessageData()),
                        'details' => [],
                    ],
                ],
            // or to translate this message
            // $this->translator->trans($exception->getMessageKey(), $exception->getMessageData())
        ];

        return new JsonResponse($data, Response::HTTP_FORBIDDEN);
    }

    /**
     * Called when authentication is needed, but it's not sent
     * @param Request $request
     * @param AuthenticationException|null $authException
     * @return JsonResponse
     */
    public function start(Request $request, AuthenticationException $authException = null)
    {
        try {
            $class = (new \ReflectionClass(self::class))->getShortName();
        } catch (\ReflectionException $e) {
            $class = '?';
        }

        $data = [
            'response' => 0,
            'status' => Response::HTTP_UNAUTHORIZED,
            'errors' =>
                [
                    [
                        'type' => 'auth',
                        'name' => $class,
                        'message' => 'Authentication required',
                        'details' => [],
                    ],
                ],
        ];

        return new JsonResponse($data, Response::HTTP_UNAUTHORIZED);
    }

    public function supportsRememberMe()
    {
        return false;
    }
}
