<?php

namespace App\Controller\Web;

use App\Controller\WebController;
use App\Entity\Campaign;
use App\Entity\Product;
use App\Entity\User;
use App\Form\CampaignType;
use App\Form\ProductType;
use App\Repository\ProductRepository;
use App\Utilities\User\DeviceStore;
use App\Utilities\User\UserDevice;
use Doctrine\ORM\EntityManager;
use Exception;
use Psr\Cache\InvalidArgumentException;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Cache\ItemInterface;
use Twig\Cache\FilesystemCache;

class AppLinkController extends WebController
{
    /**
     * @Route("/go/{uuid}", name="web/go")
     * @param Request $request
     * @param string $uuid
     * @return Response
     * @throws Exception
     */
    public function index(Request $request, string $uuid)
    {
        $campaign = $this->entityManager->getRepository(Campaign::class)->findOneBy([
            'uuid' => $uuid,
        ]);

        if (!$campaign)
            return new Response('Not found', 404, []);

        $userDevice = new UserDevice($request->server->get('HTTP_USER_AGENT', ""));

        $storeRedirect = null;

        switch ($userDevice->getStore()) {
            case UserDevice::DEVICE_GOOGLE_PLAY:
                $storeRedirect = sprintf($_ENV[UserDevice::DEVICE_GOOGLE_PLAY], $uuid);
                break;
            case UserDevice::DEVICE_APPLE_STORE:
                $storeRedirect = sprintf($_ENV[UserDevice::DEVICE_APPLE_STORE], $uuid);
                break;
            case UserDevice::DEVICE_DEFAULT_WEB:
            default:
                $storeRedirect = $this->generateUrl($_ENV[UserDevice::DEVICE_DEFAULT_WEB], ['uuid' => $campaign->getUuid()]);
                break;
        }

        echo $storeRedirect;
        return $this->redirect($storeRedirect);
    }

}