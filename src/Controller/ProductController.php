<?php

namespace App\Controller;

use App\Service\ProductFetcher;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[Route(path: '/products', name: 'products')]
class ProductController extends AbstractController
{

    #[Route(path: '/', name: "_get")]
    public function getProducts(ProductFetcher $productFetcher, Request $request)
    {
        $content = $request->getContent();
        $content = json_decode($content);
        $url = 'products';
        if ($content) {
            $url = $content->nextUrl;
        }

        $productResponse = $productFetcher->fetchProducts($url);
        $products = $productResponse['products'];
        $fetchUrl = $productResponse['next'];

        $html = $this->renderView('front/products/_list.html.twig', ['products' => $products]);


        return new JsonResponse([
            'next' => $fetchUrl,
            'html' => $html
        ]);
    }

}