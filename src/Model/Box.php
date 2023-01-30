<?php

namespace App\Model;

use Doctrine\Common\Collections\ArrayCollection;

class Box
{
    public const BOX_SESSION_KEY = 'box';

    protected int $maxItems;

    protected ArrayCollection $items;

    protected Customer $user;

    public function __construct($maxItems)
    {
        $this->maxItems = $maxItems;
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
    public function getItems(): array
    {
        return $this->items;
    }

    /**
     * @param array $items
     * @return Box
     */
    public function setItems(array $items): Box
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


}