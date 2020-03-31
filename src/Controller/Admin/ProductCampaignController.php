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

class ProductCampaignController extends WebController
{
    /**
     * @Route("/admin/product/{product_id}/campaigns", name="admin/product/campaigns/index")
     * @param int $product_id
     * @return Response
     */
    public function index(int $product_id)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $product = $entityManager->getRepository(Product::class)->findOneBy([
            'id' => $product_id,
            'user' => $this->getUser(),
        ]);

        return $this->render('admin/product/campaign/index.html.twig', [
            'product' => $product,
            'campaigns' => $product->getCampaigns(),
        ]);
    }

    /**
     * @Route("/admin/product/{product_id}/campaign/new", name="admin/product/campaign/new")
     * @param Request $request
     * @param int $product_id
     * @return Response
     */
    public function new(Request $request, int $product_id)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $product = $entityManager->getRepository(Product::class)->findOneBy([
            'id' => $product_id,
            'user' => $this->getUser(),
        ]);

        $campaign = new Campaign();
        $form = $this->createForm(CampaignType::class, $campaign);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $campaign = $form->getData();
            $campaign->setMaster(false);

            $product->addCampaign($campaign);
            $entityManager->persist($product);
            $entityManager->flush();
            $this->addFlash('success', sprintf('%s campaign has been added', $campaign->getName()));
            return $this->redirectToRoute('admin/product/campaigns/index', [
                'product_id' => $product->getId()
            ]);
        }

        return $this->render('admin/product/campaign/new.html.twig', [
            'form' => $form->createView(),
            'product' => $product,
            'campaigns' => $product->getCampaigns(),
        ]);
    }

    /**
     * @Route("/admin/product/{product_id}/campaign/{campaign_id}/edit", name="admin/product/campaign/edit")
     * @param Request $request
     * @param int $product_id
     * @param int $campaign_id
     * @return Response
     */
    public function edit(Request $request, int $product_id, int $campaign_id)
    {
        $entityManager = $this->getDoctrine()->getManager();

        $product = $entityManager->getRepository(Product::class)->findOneBy([
            'id' => $product_id,
            'user' => $this->getUser(),
        ]);

        $campaign = $entityManager->getRepository(Campaign::class)->findOneBy([
            'id' => $campaign_id,
            'product' => $product,
        ]);

        $form = $this->createForm(CampaignType::class, $campaign);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var Campaign $campaign */
            $campaign = $form->getData();

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($campaign);
            $entityManager->flush();

            $this->addFlash('success', sprintf('%s campaign has been updated', $campaign->getName()));
            return $this->redirectToRoute('admin/product/campaigns/index', [
                'product_id' => $product->getId()
            ]);
        }

        return $this->render('admin/product/campaign/edit.html.twig', [
            'form' => $form->createView(),
            'product' => $product,
            'campaign' => $campaign,
        ]);
    }

}