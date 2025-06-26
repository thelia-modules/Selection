<?php

namespace Selection\Api\Resource;

use Symfony\Component\Serializer\Attribute\Groups;
use Thelia\Api\Resource\I18n;

class SelectionContainerImageI18n extends I18n
{
    #[Groups([SelectionContainer::GROUP_READ])]
    public ?string $title = null;

    #[Groups([SelectionContainer::GROUP_READ])]
    public ?string $description = null;

    #[Groups([SelectionContainer::GROUP_READ])]
    public ?string $chapo = null;

    #[Groups([SelectionContainer::GROUP_READ])]
    public ?string $postscriptum = null;

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(?string $title): SelectionContainerImageI18n
    {
        $this->title = $title;
        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): SelectionContainerImageI18n
    {
        $this->description = $description;
        return $this;
    }

    public function getChapo(): ?string
    {
        return $this->chapo;
    }

    public function setChapo(?string $chapo): SelectionContainerImageI18n
    {
        $this->chapo = $chapo;
        return $this;
    }

    public function getPostscriptum(): ?string
    {
        return $this->postscriptum;
    }

    public function setPostscriptum(?string $postscriptum): SelectionContainerImageI18n
    {
        $this->postscriptum = $postscriptum;
        return $this;
    }
}
