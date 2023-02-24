<?php

namespace App\Controller;

use App\Model\Box;
use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[Route(path: '/products', name: 'products')]
class ProductController extends AbstractController
{

    #[Route(path: '/', name: "_list")]
    public function listProducts(ProductRepository $productRepository, ?Box $box)
    {

        $products = $productRepository->findAll();

        return $this->render('front/products/list.html.twig', ['products' => $products, 'box' => $box]);
    }

}