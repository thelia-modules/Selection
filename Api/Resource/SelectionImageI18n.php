<?php

namespace Selection\Api\Resource;

use Symfony\Component\Serializer\Attribute\Groups;
use Thelia\Api\Resource\I18n;

class SelectionImageI18n extends I18n
{
    #[Groups([Selection::GROUP_READ,SelectionContainer::GROUP_READ])]
    public ?string $title = null;

    #[Groups([Selection::GROUP_READ,SelectionContainer::GROUP_READ])]
    public ?string $description = null;

    #[Groups([Selection::GROUP_READ,SelectionContainer::GROUP_READ])]
    public ?string $chapo = null;

    #[Groups([Selection::GROUP_READ,SelectionContainer::GROUP_READ])]
    public ?string $postscriptum = null;

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(?string $title): SelectionImageI18n
    {
        $this->title = $title;
        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): SelectionImageI18n
    {
        $this->description = $description;
        return $this;
    }

    public function getChapo(): ?string
    {
        return $this->chapo;
    }

    public function setChapo(?string $chapo): SelectionImageI18n
    {
        $this->chapo = $chapo;
        return $this;
    }

    public function getPostscriptum(): ?string
    {
        return $this->postscriptum;
    }

    public function setPostscriptum(?string $postscriptum): SelectionImageI18n
    {
        $this->postscriptum = $postscriptum;
        return $this;
    }
}
