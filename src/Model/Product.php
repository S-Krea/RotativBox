<?php

namespace App\Model;

class Product
{
    private ?int $id = null;

    private ?string $name = null;

    private ?string $shortDescription = null;

    private ?string $price = null;
    private ?string $sku = null;

    private array $images = [];

    private string $permalink;

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @param int|null $id
     * @return Product
     */
    public function setId(?int $id): Product
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * @param string|null $name
     * @return Product
     */
    public function setName(?string $name): Product
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getShortDescription(): ?string
    {
        return $this->shortDescription;
    }

    /**
     * @param string|null $shortDescription
     * @return Product
     */
    public function setShortDescription(?string $shortDescription): Product
    {
        $this->shortDescription = $shortDescription;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getPrice(): ?string
    {
        return $this->price;
    }

    /**
     * @param string|null $price
     * @return Product
     */
    public function setPrice(?string $price): Product
    {
        $this->price = $price;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getSku(): ?string
    {
        return $this->sku;
    }

    /**
     * @param string|null $sku
     * @return Product
     */
    public function setSku(?string $sku): Product
    {
        $this->sku = $sku;
        return $this;
    }

    /**
     * @return array
     */
    public function getImages(): array
    {
        return $this->images;
    }

    /**
     * @param array $images
     * @return Product
     */
    public function setImages(array $images): Product
    {
        $this->images = $images;
        return $this;
    }


    public function toEntity(?\App\Entity\Product $entityToPopulate = null)
    {
        if (!$entityToPopulate) {
            $entityToPopulate = new \App\Entity\Product();
        }

        $entityToPopulate->setName($this->name);
        $entityToPopulate->setPrice($this->price);
        $entityToPopulate->setShortDescription($this->shortDescription);
        $entityToPopulate->setRef($this->sku);
        $entityToPopulate->setWooCommerceId($this->id);
        $entityToPopulate->setPermalink($this->permalink);

        if (count($this->images) >0){
            $img = $this->images[0];
            $entityToPopulate->setImage($img['src']);
            $entityToPopulate->setImageAlt($img['alt']);
        }

        return $entityToPopulate;
    }

    /**
     * @return string
     */
    public function getPermalink(): string
    {
        return $this->permalink;
    }

    /**
     * @param string $permalink
     * @return Product
     */
    public function setPermalink(string $permalink): Product
    {
        $this->permalink = $permalink;
        return $this;
    }

}