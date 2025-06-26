<?php

namespace Selection\Api\Resource;

use Symfony\Component\Serializer\Attribute\Groups;
use Thelia\Api\Resource\I18n;

class SelectionI18n extends I18n
{
    #[Groups([Selection::GROUP_READ,SelectionContainer::GROUP_READ])]
    public ?string $title = null;

    #[Groups([Selection::GROUP_READ,SelectionContainer::GROUP_READ])]
    public ?string $description = null;

    #[Groups([Selection::GROUP_READ,SelectionContainer::GROUP_READ])]
    public ?string $chapo = null;

    #[Groups([Selection::GROUP_READ,SelectionContainer::GROUP_READ])]
    public ?string $postscriptum = null;

    #[Groups([Selection::GROUP_READ,SelectionContainer::GROUP_READ])]
    public ?string $metaTitle = null;

    #[Groups([Selection::GROUP_READ,SelectionContainer::GROUP_READ])]
    public ?string $metaDescription = null;

    #[Groups([Selection::GROUP_READ,SelectionContainer::GROUP_READ])]
    public ?string $metaKeywords = null;

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(?string $title): SelectionI18n
    {
        $this->title = $title;
        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): SelectionI18n
    {
        $this->description = $description;
        return $this;
    }

    public function getChapo(): ?string
    {
        return $this->chapo;
    }

    public function setChapo(?string $chapo): SelectionI18n
    {
        $this->chapo = $chapo;
        return $this;
    }

    public function getPostscriptum(): ?string
    {
        return $this->postscriptum;
    }

    public function setPostscriptum(?string $postscriptum): SelectionI18n
    {
        $this->postscriptum = $postscriptum;
        return $this;
    }

    public function getMetaTitle(): ?string
    {
        return $this->metaTitle;
    }

    public function setMetaTitle(?string $metaTitle): SelectionI18n
    {
        $this->metaTitle = $metaTitle;
        return $this;
    }

    public function getMetaDescription(): ?string
    {
        return $this->metaDescription;
    }

    public function setMetaDescription(?string $metaDescription): SelectionI18n
    {
        $this->metaDescription = $metaDescription;
        return $this;
    }

    public function getMetaKeywords(): ?string
    {
        return $this->metaKeywords;
    }

    public function setMetaKeywords(?string $metaKeywords): SelectionI18n
    {
        $this->metaKeywords = $metaKeywords;
        return $this;
    }
}
