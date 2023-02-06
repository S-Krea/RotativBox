<?php

namespace App\Model;

use App\Exception\BoxFullException;
use App\Exception\ItemAlreadyInBoxException;
use Doctrine\Common\Collections\ArrayCollection;

class Box
{
    public const BOX_SESSION_KEY = 'box';

    protected int $maxItems;

    protected ArrayCollection $items;

    protected Customer $user;

    public function __construct($maxItems = 3)
    {
        $this->maxItems = $maxItems;
        $this->items = new ArrayCollection();
    }

    /**
     * @return int
     */
    public function getMaxItems(): int
    {
        return $this->maxItems;
    }

    /**
     * @param int $maxItems
     * @return Box
     */
    public function setMaxItems(int $maxItems): Box
    {
        $this->maxItems = $maxItems;
        return $this;
    }

    /**
     * @return array
     */
    public function getItems(): ArrayCollection
    {
        return $this->items;
    }

    /**
     * @param array $items
     * @return Box
     */
    public function setItems(ArrayCollection $items): Box
    {
        $this->items = $items;
        return $this;
    }

    /**
     * @return Customer
     */
    public function getUser(): Customer
    {
        return $this->user;
    }

    /**
     * @param Customer $user
     * @return Box
     */
    public function setUser(Customer $user): Box
    {
        $this->user = $user;
        return $this;
    }

    public function addItem($product)
    {
        if  ($this->isFull()) {
            throw new BoxFullException();
        }

        if  ($this->items->containsKey($product->getId())) {
            throw new ItemAlreadyInBoxException();
        }

        $this->items->set($product->getId(), $product);

        return $this;
    }

    public function removeItem($product)
    {
        if ($this->items->containsKey($product->getId())) {
            $this->items->remove($product->getId());
        }

        return $this;
    }

    public function isFull()
    {
        return ($this->items->count() >= $this->maxItems);
    }

    public function getProductTotal()
    {
        $total = 0;
        foreach ($this->items as $item) {
            $total+= (float)$item->getPrice();
        }

        return $total;
    }

    public function getMaintenanceCost()
    {
        $coeff = 1;
        $maintenancePrice = 350;

        switch ($this->maxItems) {
            case 6:
                $coeff = 3;
                break;
            case 9:
                $coeff = 4;
                break;
        }

        return $coeff * $maintenancePrice;
    }
}