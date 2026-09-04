<?php

declare(strict_types=1);

namespace Selection\Api\Resource;

use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use Propel\Runtime\Map\TableMap;
use Selection\Model\Map\SelectionContainerAssociatedSelectionTableMap;
use Symfony\Component\Serializer\Annotation\Groups;
use Thelia\Api\Bridge\Propel\Attribute\Relation;
use Thelia\Api\Bridge\Propel\Filter\SearchFilter;
use Thelia\Api\Resource\PropelResourceInterface;
use Thelia\Api\Resource\PropelResourceTrait;

#[ApiResource(
    operations: [
        new Get(
            uriTemplate: '/front/selection_container_associated_selections/{id}',
            normalizationContext: ['groups' => [self::GROUP_FRONT_READ, self::GROUP_FRONT_READ_SINGLE]],
        ),
    ],
    normalizationContext: ['groups' => [self::GROUP_FRONT_READ]],
)]
#[ApiFilter(
    filterClass: SearchFilter::class,
    properties: [
        'selectionContainer.id',
        'selectionContainer.code',
        'selection.id',
        'selection.code',
    ],
)]
class SelectionContainerAssociatedSelection implements PropelResourceInterface
{
    use PropelResourceTrait;

    public const GROUP_FRONT_READ = 'front:selection_container_associated_selection:read';
    public const GROUP_FRONT_READ_SINGLE = 'front:selection_container_associated_selection:read:single';

    #[Groups([self::GROUP_FRONT_READ])]
    public ?int $id = null;

    #[Relation(targetResource: SelectionContainer::class)]
    #[Groups([self::GROUP_FRONT_READ, Selection::GROUP_FRONT_READ_SINGLE])]
    public SelectionContainer $selectionContainer;

    #[Relation(targetResource: Selection::class)]
    #[Groups([self::GROUP_FRONT_READ, SelectionContainer::GROUP_FRONT_READ_SINGLE])]
    public Selection $selection;

    #[Groups([self::GROUP_FRONT_READ])]
    public ?\DateTime $createdAt = null;

    #[Groups([self::GROUP_FRONT_READ])]
    public ?\DateTime $updatedAt = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): self
    {
        $this->id = $id;

        return $this;
    }

    public function getSelectionContainer(): SelectionContainer
    {
        return $this->selectionContainer;
    }

    public function setSelectionContainer(SelectionContainer $selectionContainer): self
    {
        $this->selectionContainer = $selectionContainer;

        return $this;
    }

    public function getSelection(): Selection
    {
        return $this->selection;
    }

    public function setSelection(Selection $selection): self
    {
        $this->selection = $selection;

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
        return new SelectionContainerAssociatedSelectionTableMap();
    }
}
