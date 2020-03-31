<?php

namespace App\Controller\Web;

use App\Controller\WebController;
use App\Entity\Campaign;
use App\Entity\Product;
use App\Entity\User;
use App\Form\CampaignType;
use App\Form\ProductType;
use App\Repository\ProductRepository;
use App\Utilities\QR\QRCode;
use Doctrine\ORM\EntityManager;
use Psr\Cache\InvalidArgumentException;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Contracts\Cache\ItemInterface;
use Twig\Cache\FilesystemCache;

class QRCodeController extends WebController
{
    /**
     * @Route("/qr/{campaign_uuid}/{size}", name="web/qr")
     * @param string $campaign_uuid
     * @param int $size
     * @return Response
     * @throws InvalidArgumentException
     */
    public function index(string $campaign_uuid, int $size = QRCode::QR_DEFAULT_SIZE)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $campaign = $entityManager->getRepository(Campaign::class)->findOneBy([
            'uuid' => $campaign_uuid,
        ]);

        if (!$campaign)
            return new Response('Not found', 404, []);

        /* ToDo: change to public asset */
        $imageKey = sprintf('qr_%s_%s', $campaign->getUuid(), $size);

        $cache = new FilesystemAdapter();
        $image = $cache->get($imageKey,
            function (ItemInterface $item) use ($campaign, $size) {
                $item->expiresAfter(3600 * 4);

                // Generate QR
                $QRCode = new QRCode(
                    $this->generateUrl('web/go', [
                        'uuid' => $campaign->getUuid()
                    ], UrlGeneratorInterface::ABSOLUTE_URL), $size);

                return $QRCode->getBinary();
            });

        return new Response($image, 200, [
            'Content-Type' => 'image/png',
            'Content-Disposition' => sprintf('inline; filename="%s.png"', $imageKey),
        ]);
    }

}