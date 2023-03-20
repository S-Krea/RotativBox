<?php

namespace App\Controller;

use App\Model\Box;
use App\Repository\BrandRepository;
use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[Route(path: '/products', name: 'products')]
class ProductController extends AbstractController
{

    #[Route(path: '/', name: "_list")]
    public function listProducts(Request $request, ProductRepository $productRepository, BrandRepository $brandRepository, ?Box $box)
    {
        if (!$box) {
            $this->addFlash('warning', 'Selectionnez d\'abord une box');
            return $this->redirectToRoute('app_home');
        }

        $brandSlug = $request->query->get('brand', false);
        $products = $productRepository->findAllEnabled($brandSlug);
        $brands = $brandRepository->findAllEnabled();

        return $this->render('front/products/list.html.twig', [
            'products' => $products,
            'brands' => $brands,
            'box' => $box,
            'filterSlug' => $brandSlug,
        ]);
    }

}