<?php

namespace App\Controller\Admin;

use App\Controller\WebController;
use App\Entity\Campaign;
use App\Entity\Product;
use App\Entity\User;
use App\Form\ProductType;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProductController extends WebController
{
    /**
     * @Route("/admin/products", name="admin/product/index")
     */
    public function index()
    {
        $products = $this->getUser()->getProducts();
        return $this->render('admin/product/index.html.twig', [
            'products' => $products,
        ]);
    }

    /**
     * @Route("/admin/product/{id}/view", name="admin/product/view",  requirements={"id"="\d+"})
     * @param Request $request
     * @return Response
     */
    public function view(Request $request, $id)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $product = $entityManager->getRepository(Product::class)->findOneBy([
            'id' => $id,
            'user' => $this->getUser(),
        ]);
    }


    /**
     * @Route("/admin/product/new", name="admin/product/new")
     * @param Request $request
     * @return Response
     * @throws \Doctrine\DBAL\ConnectionException
     */
    public function new(Request $request)
    {
        $product = new Product();
        $form = $this->createForm(ProductType::class, $product);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            /** @var EntityManager $entityManager */
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->getConnection()->beginTransaction();

            try {
                /** @var Product $product */
                $product = $form->getData();
                $product->setUser($this->getUser());

                $entityManager->persist($product);
                $entityManager->flush();

                // add generic campaign
                $campaign = new Campaign();
                $campaign
                    ->setName('Generic')
                    ->setProduct($product)
                    ->setMaster(true);

                $entityManager->persist($campaign);
                $entityManager->flush();

                $entityManager->getConnection()->commit();
                $this->addFlash('success', sprintf('%s has been added', $product->getName()));

            } catch (\Exception $e) {
                $entityManager->getConnection()->rollBack();
                $this->addFlash('danger', 'Unable to add product');
            }

            return $this->redirectToRoute('admin/product/index');

        }

        return $this->render('admin/product/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/admin/product/{id}/edit", name="admin/product/edit",  requirements={"id"="\d+"})
     * @param Request $request
     * @return Response
     */
    public function edit(Request $request, $id)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $product = $entityManager->getRepository(Product::class)->find($id);

        $form = $this->createForm(ProductType::class, $product);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            /** @var Product $product */
            $product = $form->getData();

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($product);
            $entityManager->flush();

            $this->addFlash('success', sprintf('%s has been updated', $product->getName()));
            return $this->redirectToRoute('admin/product/index');
        }

        return $this->render('admin/product/edit.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}