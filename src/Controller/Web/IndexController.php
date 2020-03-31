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

class IndexController extends WebController
{
    /**
     * @Route("/web/campaign/{uuid}", name="web/campaign")
     * @param Request $request
     * @param string $uuid
     * @return Response
     * @throws Exception
     */
    public function index(Request $request, string $uuid)
    {
        die($uuid);
    }

}