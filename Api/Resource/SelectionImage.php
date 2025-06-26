<?php

namespace Selection\Api\Resource;

use Propel\Runtime\Map\TableMap;
use Symfony\Component\Serializer\Annotation\Ignore;
use Symfony\Component\Serializer\Attribute\Groups;
use Thelia\Api\Resource\AbstractTranslatableResource;
use Selection\Model\Map\SelectionImageTableMap;
use Thelia\Api\Resource\I18nCollection;

class SelectionImage extends AbstractTranslatableResource
{
    #[Groups([Selection::GROUP_READ])]
    public ?int $id = null;

    #[Groups([Selection::GROUP_READ])]
    public ?string $file = null;

    #[Groups([Selection::GROUP_READ])]
    public ?int $position = null;

    #[Groups([Selection::GROUP_READ])]
    public ?bool $visible = null;

    #[Groups([Selection::GROUP_READ])]
    public I18nCollection $i18ns;

    public function getFile(): ?string
    {
        return $this->file;
    }

    public function setFile(?string $file): SelectionImage
    {
        $this->file = $file;
        return $this;
    }

    public function getPosition(): ?int
    {
        return $this->position;
    }

    public function setPosition(?int $position): SelectionImage
    {
        $this->position = $position;
        return $this;
    }

    public function getVisible(): ?bool
    {
        return $this->visible;
    }

    public function setVisible(?bool $visible): SelectionImage
    {
        $this->visible = $visible;
        return $this;
    }

    #[Ignore] public static function getPropelRelatedTableMap(): ?TableMap
    {
        return new SelectionImageTableMap();
    }

    public static function getI18nResourceClass(): string
    {
        return SelectionImageI18n::class;
    }
}
