<?php

namespace App\Controller\Admin;

use App\Controller\WebController;
use App\Entity\Campaign;
use App\Entity\Product;
use App\Entity\User;
use App\Form\CampaignType;
use App\Form\ProductType;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProductCampaignQRController extends WebController
{
    /**
     * @Route("/admin/assets/qr/{campaign_id}", name="admin/product/campaigns/qr/index")
     * @param int $campaign_id
     * @return Response
     */
    public function index(int $campaign_id)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $campaign = $entityManager->getRepository(Campaign::class)->findOneBy([
            'id' => $campaign_id,
        ]);
        dd($campaign);

        return $this->render('admin/product/campaign/index.html.twig', [
            'product' => $product,
            'campaigns' => $product->getCampaigns(),
        ]);
    }

}