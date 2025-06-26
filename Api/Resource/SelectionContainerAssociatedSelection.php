<?php

namespace Selection\Api\Resource;

use Propel\Runtime\Map\TableMap;
use Symfony\Component\Serializer\Annotation\Ignore;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Serializer\Attribute\MaxDepth;
use Thelia\Api\Bridge\Propel\Attribute\Relation;
use Thelia\Api\Resource\PropelResourceInterface;
use Thelia\Api\Resource\PropelResourceTrait;
use Selection\Model\Map\SelectionContainerAssociatedSelectionTableMap;

class SelectionContainerAssociatedSelection implements PropelResourceInterface
{
    use PropelResourceTrait;

    public ?int $id = null;

    #[Relation(targetResource: Selection::class, excludedGroups: [Selection::GROUP_READ])]
    #[Groups([SelectionContainer::GROUP_READ])]
    public Selection $selection;

    #[Relation(targetResource: SelectionContainer::class, excludedGroups: [SelectionContainer::GROUP_READ])]
    #[Groups([Selection::GROUP_READ])]
    public SelectionContainer $selectionContainer;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): SelectionContainerAssociatedSelection
    {
        $this->id = $id;
        return $this;
    }

    public function getSelection(): Selection
    {
        return $this->selection;
    }

    public function setSelection(Selection $selection): SelectionContainerAssociatedSelection
    {
        $this->selection = $selection;
        return $this;
    }

    public function getSelectionContainer(): SelectionContainer
    {
        return $this->selectionContainer;
    }

    public function setSelectionContainer(SelectionContainer $selectionContainer): SelectionContainerAssociatedSelection
    {
        $this->selectionContainer = $selectionContainer;
        return $this;
    }

    #[Ignore] public static function getPropelRelatedTableMap(): ?TableMap
    {
        return new SelectionContainerAssociatedSelectionTableMap;
    }
}
