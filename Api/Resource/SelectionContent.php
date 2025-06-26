<?php

namespace Selection\Api\Resource;

use Propel\Runtime\Map\TableMap;
use Symfony\Component\Serializer\Annotation\Ignore;
use Symfony\Component\Serializer\Attribute\Groups;
use Thelia\Api\Resource\PropelResourceInterface;
use Thelia\Api\Resource\PropelResourceTrait;
use Selection\Model\Map\SelectionContentTableMap;

class SelectionContent implements PropelResourceInterface
{
    use PropelResourceTrait;

    #[Groups([Selection::GROUP_READ])]
    public ?int $contentId = null;

    public ?int $selectionId = null;

    #[Groups([Selection::GROUP_READ])]
    public ?int $position = null;

    public function getContentId(): ?int
    {
        return $this->contentId;
    }

    public function setContentId(?int $contentId): SelectionContent
    {
        $this->contentId = $contentId;
        return $this;
    }

    public function getSelectionId(): ?int
    {
        return $this->selectionId;
    }

    public function setSelectionId(?int $selectionId): SelectionContent
    {
        $this->selectionId = $selectionId;
        return $this;
    }

    public function getPosition(): ?int
    {
        return $this->position;
    }

    public function setPosition(?int $position): SelectionContent
    {
        $this->position = $position;
        return $this;
    }

    #[Ignore] public static function getPropelRelatedTableMap(): ?TableMap
    {
        return new SelectionContentTableMap();
    }
}
