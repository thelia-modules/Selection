<?php

namespace Selection\Api\Resource;

use Propel\Runtime\Map\TableMap;
use Symfony\Component\Serializer\Annotation\Ignore;
use Symfony\Component\Serializer\Attribute\Groups;
use Thelia\Api\Resource\PropelResourceInterface;
use Thelia\Api\Resource\PropelResourceTrait;
use Selection\Model\Map\SelectionProductTableMap;

class SelectionProduct implements PropelResourceInterface
{
    use PropelResourceTrait;

    #[Groups([Selection::GROUP_READ])]
    public ?int $productId = null;

    public ?int $selectionId = null;

    #[Groups([Selection::GROUP_READ])]
    public ?int $position = null;

    public function getProductId(): ?int
    {
        return $this->productId;
    }

    public function setProductId(?int $productId): SelectionProduct
    {
        $this->productId = $productId;
        return $this;
    }

    public function getSelectionId(): ?int
    {
        return $this->selectionId;
    }

    public function setSelectionId(?int $selectionId): SelectionProduct
    {
        $this->selectionId = $selectionId;
        return $this;
    }

    public function getPosition(): ?int
    {
        return $this->position;
    }

    public function setPosition(?int $position): SelectionProduct
    {
        $this->position = $position;
        return $this;
    }

    #[Ignore] public static function getPropelRelatedTableMap(): ?TableMap
    {
        return new SelectionProductTableMap();
    }
}
