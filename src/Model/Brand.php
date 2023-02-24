<?php

namespace App\Model;

use Symfony\Component\Serializer\Annotation\SerializedName;

class Brand
{
    #[SerializedName('term_id')]
    public int $termId;

    public string $name;

    public string $slug;

    public ?string $image;

    public function toEntity(?\App\Entity\Brand $brandToPopulate): \App\Entity\Brand
    {
        if ($brandToPopulate === null) {
            $brandToPopulate = new \App\Entity\Brand();
        }

        $brandToPopulate->setName($this->name);
        $brandToPopulate->setWoocommerceId($this->termId);
        $brandToPopulate->setSlug($this->slug);
        $brandToPopulate->setImage($this->image);

        return $brandToPopulate;
    }
}