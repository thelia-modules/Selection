<?php

namespace Selection\Api\Resource;

use Propel\Runtime\Map\TableMap;
use Symfony\Component\Serializer\Annotation\Ignore;
use Symfony\Component\Serializer\Attribute\Groups;
use Thelia\Api\Resource\AbstractTranslatableResource;
use Thelia\Api\Resource\I18nCollection;
use Thelia\Api\Resource\PropelResourceTrait;
use Selection\Model\Map\SelectionContainerImageTableMap;

class SelectionContainerImage extends AbstractTranslatableResource
{
    use PropelResourceTrait;

    #[Groups([SelectionContainer::GROUP_READ])]
    public ?int $id = null;

    #[Groups([SelectionContainer::GROUP_READ])]
    public ?string $file = null;

    #[Groups([SelectionContainer::GROUP_READ])]
    public ?int $position = null;

    #[Groups([SelectionContainer::GROUP_READ])]
    public ?bool $visible = null;

    #[Groups([SelectionContainer::GROUP_READ])]
    public I18nCollection $i18ns;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): SelectionContainerImage
    {
        $this->id = $id;
        return $this;
    }

    public function getFile(): ?string
    {
        return $this->file;
    }

    public function setFile(?string $file): SelectionContainerImage
    {
        $this->file = $file;
        return $this;
    }

    public function getPosition(): ?int
    {
        return $this->position;
    }

    public function setPosition(?int $position): SelectionContainerImage
    {
        $this->position = $position;
        return $this;
    }

    public function getVisible(): ?bool
    {
        return $this->visible;
    }

    public function setVisible(?bool $visible): SelectionContainerImage
    {
        $this->visible = $visible;
        return $this;
    }

    #[Ignore] public static function getPropelRelatedTableMap(): ?TableMap
    {
        return new SelectionContainerImageTableMap();
    }

    public static function getI18nResourceClass(): string
    {
        return SelectionContainerImageI18n::class;
    }
}
