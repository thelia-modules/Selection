<?php

namespace Selection\Api\Resource;

use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\GetCollection;
use Propel\Runtime\Map\TableMap;
use Selection\Api\Filters\ContainerBySelectionFilter;
use Symfony\Component\Serializer\Annotation\Ignore;
use Symfony\Component\Serializer\Attribute\Groups;
use Thelia\Api\Bridge\Propel\Attribute\Relation;
use Thelia\Api\Bridge\Propel\Filter\BooleanFilter;
use Thelia\Api\Bridge\Propel\Filter\NotInFilter;
use Thelia\Api\Bridge\Propel\Filter\OrderFilter;
use Thelia\Api\Bridge\Propel\Filter\SearchFilter;
use Thelia\Api\Bridge\Propel\State\PropelCollectionProvider;
use Thelia\Api\Resource\AbstractTranslatableResource;
use Selection\Model\Map\SelectionContainerTableMap;
use Thelia\Api\Resource\I18nCollection;

#[ApiResource(
    operations: [
        new GetCollection(
            uriTemplate: '/selection/container',
            name: self::ROUTE_NAME_GET_COLLECTION,
            provider: PropelCollectionProvider::class,

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
    ]
)]
#[ApiFilter(
    filterClass: NotInFilter::class,
    properties: [
        'id',
        'code',
        'title',
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
        'id',
        'title',
        'position',
        'visible',
        'createdAt',
        'updatedAt',
    ]
)]
#[ApiFilter(
    filterClass: ContainerBySelectionFilter::class,
)]
class SelectionContainer extends AbstractTranslatableResource
{
    public const ROUTE_NAME_GET_COLLECTION = 'api_selection_container_get_collection';
    public const GROUP_READ = 'selection_container:read';

    #[Groups([Selection::GROUP_READ,self::GROUP_READ])]
    public ?int $id = null;

    #[Groups([Selection::GROUP_READ,self::GROUP_READ])]
    public ?bool $visible = null;

    #[Groups([Selection::GROUP_READ,self::GROUP_READ])]
    public ?string $code = null;

    #[Groups([Selection::GROUP_READ,self::GROUP_READ])]
    public ?int $position = null;

    #[Relation(targetResource: SelectionContainerAssociatedSelection::class)]
    #[Groups([self::GROUP_READ])]
    public ?array $selectionContainerAssociatedSelections = null;

    #[Relation(targetResource: SelectionContainerImage::class)]
    #[Groups([self::GROUP_READ])]
    public ?array $selectionContainerImages;

    public ?\DateTimeInterface $createdAt = null;
    public ?\DateTimeInterface $updatedAt = null;

    #[Groups([self::GROUP_READ,Selection::GROUP_READ])]
    public I18nCollection $i18ns;

    public function getSelectionContainerImages(): ?array
    {
        return $this->selectionContainerImages;
    }

    public function setSelectionContainerImages(?array $selectionContainerImages): SelectionContainer
    {
        $this->selectionContainerImages = $selectionContainerImages;
        return $this;
    }
    public function getSelectionContainerAssociatedSelections(): ?array
    {
        return $this->selectionContainerAssociatedSelections;
    }

    public function setSelectionContainerAssociatedSelections(?array $selectionContainerAssociatedSelections): SelectionContainer
    {
        $this->selectionContainerAssociatedSelections = $selectionContainerAssociatedSelections;
        return $this;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): SelectionContainer
    {
        $this->id = $id;
        return $this;
    }

    public function getVisible(): ?bool
    {
        return $this->visible;
    }

    public function setVisible(?bool $visible): SelectionContainer
    {
        $this->visible = $visible;
        return $this;
    }

    public function getCode(): ?string
    {
        return $this->code;
    }

    public function setCode(?string $code): SelectionContainer
    {
        $this->code = $code;
        return $this;
    }

    public function getPosition(): ?int
    {
        return $this->position;
    }

    public function setPosition(?int $position): SelectionContainer
    {
        $this->position = $position;
        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(?\DateTimeInterface $createdAt): SelectionContainer
    {
        $this->createdAt = $createdAt;
        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?\DateTimeInterface $updatedAt): SelectionContainer
    {
        $this->updatedAt = $updatedAt;
        return $this;
    }

    #[Ignore] public static function getPropelRelatedTableMap(): ?TableMap
    {
        return new SelectionContainerTableMap();
    }

    public static function getI18nResourceClass(): string
    {
        return SelectionContainerI18n::class;
    }
}
