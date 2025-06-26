<?php

namespace Selection\Api\Resource;

use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\GetCollection;
use Propel\Runtime\Map\TableMap;
use Selection\Api\Filters\SelectionByContainerFilter;
use Symfony\Component\Serializer\Annotation\Ignore;
use Symfony\Component\Serializer\Attribute\Groups;
use Thelia\Api\Bridge\Propel\Attribute\Relation;
use Thelia\Api\Bridge\Propel\Filter\BooleanFilter;
use Thelia\Api\Bridge\Propel\Filter\NotInFilter;
use Thelia\Api\Bridge\Propel\Filter\OrderFilter;
use Thelia\Api\Bridge\Propel\Filter\SearchFilter;
use Thelia\Api\Bridge\Propel\State\PropelCollectionProvider;
use Thelia\Api\Resource\AbstractTranslatableResource;
use Selection\Model\Map\SelectionTableMap;
use \Thelia\Api\Resource\I18nCollection;

#[ApiResource(
    operations: [
        new GetCollection(
            uriTemplate: '/selection',
            name: self::ROUTE_NAME_GET_COLLECTION,
            provider: PropelCollectionProvider::class
        )
    ],
    normalizationContext: ['groups' => [self::GROUP_READ]]
)]
#[ApiFilter(
    filterClass: SearchFilter::class,
    properties: [
        'id',
        'code',
        'title',
        'selectionProducts.productId' => [
            'fieldPath' => 'selection_selectionproducts.product_id',
            'strategy' => 'exact',
        ],
    ]
)]
#[ApiFilter(
    filterClass: NotInFilter::class,
    properties: [
        'id',
        'code',
        'title',
        'selectionProducts.productId' => [
            'fieldPath' => 'selection_selectionproducts.product_id',
            'strategy' => 'exact',
        ],
    ]
)]
#[ApiFilter(
    filterClass: BooleanFilter::class,
    properties: [
        'visible',
    ]
)]
#[ApiFilter(
    filterClass: OrderFilter::class,
    properties: [
        'title',
        'position',
        'visible',
        'createdAt',
        'updatedAt',
    ]
)]
#[ApiFilter(
    filterClass: SelectionByContainerFilter::class,
)]
class Selection extends AbstractTranslatableResource
{
    public const ROUTE_NAME_GET_COLLECTION = 'api_selection_get_collection';

    public const GROUP_READ = 'selection:read';

    #[Groups([self::GROUP_READ,SelectionContainer::GROUP_READ])]
    public ?int $id = null;

    #[Groups([self::GROUP_READ,SelectionContainer::GROUP_READ])]
    public ?bool $visible = null;

    #[Groups([self::GROUP_READ,SelectionContainer::GROUP_READ])]
    public ?string $code = null;

    #[Groups([self::GROUP_READ,SelectionContainer::GROUP_READ])]
    public ?int $position = null;

    #[Groups([self::GROUP_READ])]
    public ?\DateTimeInterface $createdAt = null;

    #[Groups([self::GROUP_READ])]
    public ?\DateTimeInterface $updatedAt = null;

    #[Groups([self::GROUP_READ])]
    public I18nCollection $i18ns;
    #[Relation(targetResource: SelectionContainerAssociatedSelection::class)]
    #[Groups([self::GROUP_READ])]
    public array $selectionContainerAssociatedSelections;

    #[Relation(targetResource: SelectionProduct::class,forceJoin: true)]
    #[Groups([self::GROUP_READ])]
    public array $selectionProducts;

    #[Relation(targetResource: SelectionImage::class)]
    #[Groups([self::GROUP_READ])]
    public array $selectionImages;

    #[Relation(targetResource: SelectionContent::class,forceJoin: true)]
    #[Groups([self::GROUP_READ])]
    public array $selectionContents;

    public function getSelectionImages(): array
    {
        return $this->selectionImages;
    }

    public function setSelectionImages(array $selectionImages): Selection
    {
        $this->selectionImages = $selectionImages;
        return $this;
    }

    public function getSelectionContents(): array
    {
        return $this->selectionContents;
    }

    public function setSelectionContents(array $selectionContents): Selection
    {
        $this->selectionContents = $selectionContents;
        return $this;
    }

    public function getSelectionProducts(): array
    {
        return $this->selectionProducts;
    }

    public function setSelectionProducts(array $selectionProducts): Selection
    {
        $this->selectionProducts = $selectionProducts;
        return $this;
    }
    public function getSelectionContainerAssociatedSelections(): array
    {
        return $this->selectionContainerAssociatedSelections;
    }

    public function setSelectionContainerAssociatedSelections(array $selectionContainerAssociatedSelections): Selection
    {
        $this->selectionContainerAssociatedSelections = $selectionContainerAssociatedSelections;
        return $this;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): Selection
    {
        $this->id = $id;
        return $this;
    }

    public function getVisible(): ?bool
    {
        return $this->visible;
    }

    public function setVisible(?bool $visible): Selection
    {
        $this->visible = $visible;
        return $this;
    }

    public function getCode(): ?string
    {
        return $this->code;
    }

    public function setCode(?string $code): Selection
    {
        $this->code = $code;
        return $this;
    }

    public function getPosition(): ?int
    {
        return $this->position;
    }

    public function setPosition(?int $position): Selection
    {
        $this->position = $position;
        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(?\DateTimeInterface $createdAt): Selection
    {
        $this->createdAt = $createdAt;
        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?\DateTimeInterface $updatedAt): Selection
    {
        $this->updatedAt = $updatedAt;
        return $this;
    }

    #[Ignore] public static function getPropelRelatedTableMap(): ?TableMap
    {
        return new SelectionTableMap();
    }

    public static function getI18nResourceClass(): string
    {
        return SelectionI18n::class;
    }
}
