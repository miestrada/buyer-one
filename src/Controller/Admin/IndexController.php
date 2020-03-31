<?php

namespace App\Controller\Admin;

use App\Controller\WebController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class IndexController extends WebController
{
    /**
     * @Route("/admin", name="admin/index")
     */
    public function index()
    {
        return $this->render('admin/index.html.twig', []);
    }
}