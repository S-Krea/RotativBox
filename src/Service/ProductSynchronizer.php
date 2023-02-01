<?php

namespace App\Service;

use App\Entity\Product;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Serializer\SerializerInterface;

class ProductSynchronizer
{

    private ProductFetcher $productFetcher;

    private int $count = 0;

    private SerializerInterface $serializer;

    private EntityManagerInterface $em;


    public function __construct(ProductFetcher $productFetcher, SerializerInterface $serializer, EntityManagerInterface $em)
    {
        $this->productFetcher = $productFetcher;
        $this->serializer = $serializer;
        $this->em = $em;
    }

    public function synchronizeAll()
    {
        $this->fetchProductList('products');

        return $this->count;
    }

    private function fetchProductList($url = null)
    {
        $response = $this->productFetcher->fetchProducts($url);
        $products = $response['products'];
        $nextUrl = $response['next'];

        $prods = $this->serializer->deserialize($products, sprintf('%s[]', \App\Model\Product::class), 'json');

        foreach ($prods as $prod) {
            $entityProd = $this->em->getRepository(Product::class)->findOneBy(['woocommerceId' => $prod->getId()]);
            $entityProd = $prod->toEntity($entityProd);
            $this->em->persist($entityProd);

            $this->count++;
        }

        $this->em->flush();

        if ($nextUrl) {
            return $this->fetchProductList($nextUrl);
        }

        return;
    }
}