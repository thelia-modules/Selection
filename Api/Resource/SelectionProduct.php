<?php

declare(strict_types=1);

namespace Selection\Api\Resource;

use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use Propel\Runtime\Map\TableMap;
use Selection\Model\Map\SelectionProductTableMap;
use Symfony\Component\Serializer\Annotation\Groups;
use Thelia\Api\Bridge\Propel\Attribute\CompositeIdentifiers;
use Thelia\Api\Bridge\Propel\Attribute\Relation;
use Thelia\Api\Bridge\Propel\Filter\OrderFilter;
use Thelia\Api\Bridge\Propel\Filter\SearchFilter;
use Thelia\Api\Resource\Product;
use Thelia\Api\Resource\PropelResourceInterface;
use Thelia\Api\Resource\PropelResourceTrait;

#[ApiResource(
    operations: [
        new Get(
            uriTemplate: '/front/selection_products/{selection}/products/{product}',
            normalizationContext: ['groups' => [self::GROUP_FRONT_READ, self::GROUP_FRONT_READ_SINGLE]],
        ),
    ],
    normalizationContext: ['groups' => [self::GROUP_FRONT_READ]],
)]
#[ApiFilter(
    filterClass: SearchFilter::class,
    properties: [
        'selection.id',
        'selection.code',
        'product.id',
    ],
)]
#[ApiFilter(
    filterClass: OrderFilter::class,
    properties: [
        'position',
    ],
)]
#[CompositeIdentifiers(['selection', 'product'])]
class SelectionProduct implements PropelResourceInterface
{
    use PropelResourceTrait;

    public const GROUP_FRONT_READ = 'front:selection_product:read';
    public const GROUP_FRONT_READ_SINGLE = 'front:selection_product:read:single';

    #[Relation(targetResource: Selection::class, excludedGroups: [Selection::GROUP_FRONT_READ, Selection::GROUP_FRONT_READ_SINGLE])]
    #[Groups([self::GROUP_FRONT_READ])]
    public Selection $selection;

    #[Relation(targetResource: Product::class)]
    #[Groups([self::GROUP_FRONT_READ, Selection::GROUP_FRONT_READ_SINGLE])]
    public Product $product;

    #[Groups([self::GROUP_FRONT_READ, Selection::GROUP_FRONT_READ_SINGLE])]
    public int $position;

    #[Groups([self::GROUP_FRONT_READ])]
    public ?\DateTime $createdAt = null;

    #[Groups([self::GROUP_FRONT_READ])]
    public ?\DateTime $updatedAt = null;

    public function getSelection(): Selection
    {
        return $this->selection;
    }

    public function setSelection(Selection $selection): self
    {
        $this->selection = $selection;

        return $this;
    }

    public function getProduct(): Product
    {
        return $this->product;
    }

    public function setProduct(Product $product): self
    {
        $this->product = $product;

        return $this;
    }

    public function getPosition(): int
    {
        return $this->position;
    }

    public function setPosition(int $position): self
    {
        $this->position = $position;

        return $this;
    }

    public function getCreatedAt(): ?\DateTime
    {
        return $this->createdAt;
    }

    public function setCreatedAt(?\DateTime $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTime
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?\DateTime $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    public static function getPropelRelatedTableMap(): ?TableMap
    {
        return new SelectionProductTableMap();
    }
}
