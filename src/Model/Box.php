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

    protected bool $optionDAC = false;

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
        if ($this->isFull()) {
            throw new BoxFullException();
        }

        /** finalement on doit gérer les quantité dans la box
         * Donc on a plus besoin de cette vérif
         */
        /*
        if  ($this->items->containsKey($product->getId())) {
            throw new ItemAlreadyInBoxException();
        }
        */

        $this->items->add($product);

        return $this;
    }

    public function removeItem($product, $all = false)
    {
        foreach ($this->items as $item) {
            if ($item->getId() === $product->getId()) {
                $this->items->removeElement($item);
                if (!$all) {
                    break;
                }
            }
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
            $total += (float)$item->getPrice();
        }

        return $total;
    }

    public function getMaintenanceCost($maintenancePrice = null)
    {
        $coeff = 1;
        if ($maintenancePrice === null) {
            $maintenancePrice = 350;
        }

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

    public function getLabel()
    {
        return match ($this->maxItems) {
            3 => "First",
            6 => 'Expert',
            9 => 'Master',
        };
    }

    public function getRemainings()
    {
        return $this->maxItems - $this->items->count();
    }

    public function contains($product)
    {
        return $this->items->containsKey($product->getId());
    }

    /**
     * @return bool
     */
    public function hasOptionDAC(): bool
    {
        return $this->optionDAC;
    }

    /**
     * @param bool $optionDAC
     */
    public function setOptionDAC(bool $optionDAC): void
    {
        $this->optionDAC = $optionDAC;
    }

    public function getOptionDacMonthlyPrice()
    {
        return match ($this->maxItems) {
            3 => 0,
            6 => 160,
            9 => 130,
        };
    }

    public function getMaxMonthsPossible()
    {
        return match ($this->maxItems) {
            3 => 36,
            6 => 48,
            9 => 60,
        };
    }

    public function getCartRows()
    {
        $rows = [];

        foreach ($this->items as $product) {
            $prodId = $product->getId();

            if (isset($rows[$prodId])) {
                $rows[$prodId]['qte'] += 1;
                continue;
            }

            $rows[$product->getId()] = [
                'id' => $prodId,
                'name' => $product->getName(),
                'image' => $product->getImage(),
                'qte' => 1,
            ];
        }

        return $rows;
    }

    public function getOptionDacPrice($amount = null)
    {
        if ($amount === null) {
            $amount = 7200;
        }

        return match ($this->maxItems) {
            3 => 0,
            6 => $amount,
            9 => $amount,
        };
    }
}