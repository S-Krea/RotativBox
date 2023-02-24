<?php

namespace App\Service;

use App\Entity\Brand;
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

    private array $unFlushedBrands;


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
        $this->unFlushedBrands = [];

        $prods = $this->serializer->deserialize($products, sprintf('%s[]', \App\Model\Product::class), 'json');

        //Disabled all products and brands.
        $this->em->createQueryBuilder()->update(Product::class, 'p')->set('p.enabled', ':enabled')->setParameter('enabled', false)->getQuery()->execute();
        $this->em->createQueryBuilder()->update(Brand::class, 'b')->set('b.enabled', ':enabled')->setParameter('enabled', false)->getQuery()->execute();

        /** @var \App\Model\Product $prod */
        foreach ($prods as $prod) {
            $brands = $prod->getMarques();
            $brand = null;

            if (count($brands) > 0) {
                $brand = $this->getBrandEntity(reset($brands));
            }

            $entityProd = $this->em->getRepository(Product::class)->findOneBy(['woocommerceId' => $prod->getId()]);
            $entityProd = $prod->toEntity($entityProd);
            $entityProd->setBrand($brand);
            $entityProd->setEnabled(true);
            $this->em->persist($entityProd);

            $this->count++;
        }

        $this->em->flush();

        if ($nextUrl) {
            return $this->fetchProductList($nextUrl);
        }

        return;
    }

    private function getBrandEntity(\App\Model\Brand $brand): Brand
    {
        $marqueId = $brand->termId;

        $brandEntity = $this->unFlushedBrands[$marqueId] ?? null;

        if (!$brandEntity) {
            $brandEntity = $this->em->getRepository(Brand::class)->findOneBy(['woocommerceId' => $marqueId]);
        }

        $entity = $brand->toEntity($brandEntity);
        $entity->setEnabled(true);

        $this->unFlushedBrands[$marqueId] = $entity;

        return $entity;
    }
}