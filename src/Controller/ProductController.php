<?php

namespace App\Controller;

use App\Model\Product;
use App\Repository\ProductRepository;
use App\Service\ProductFetcher;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[Route(path: '/products', name: 'products')]
class ProductController extends AbstractController
{

    #[Route(path: '/', name: "_list")]
    public function listProducts(ProductRepository $productRepository, Request $request)
    {

        $products = $productRepository->findAll();

        return $this->render('front/products/list.html.twig', ['products' => $products]);
    }

}