<?php

declare(strict_types=1);

namespace Selection\Api\Resource;

use Symfony\Component\Serializer\Annotation\Groups;
use Thelia\Api\Resource\I18n;

class SelectionContainerI18n extends I18n
{
    #[Groups([SelectionContainer::GROUP_FRONT_READ])]
    protected ?string $title = null;

    #[Groups([SelectionContainer::GROUP_FRONT_READ])]
    protected ?string $description = null;

    #[Groups([SelectionContainer::GROUP_FRONT_READ])]
    protected ?string $chapo = null;

    #[Groups([SelectionContainer::GROUP_FRONT_READ])]
    protected ?string $postscriptum = null;

    #[Groups([SelectionContainer::GROUP_FRONT_READ])]
    protected ?string $metaTitle = null;

    #[Groups([SelectionContainer::GROUP_FRONT_READ])]
    protected ?string $metaDescription = null;

    #[Groups([SelectionContainer::GROUP_FRONT_READ])]
    protected ?string $metaKeywords = null;

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(?string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getChapo(): ?string
    {
        return $this->chapo;
    }

    public function setChapo(?string $chapo): self
    {
        $this->chapo = $chapo;

        return $this;
    }

    public function getPostscriptum(): ?string
    {
        return $this->postscriptum;
    }

    public function setPostscriptum(?string $postscriptum): self
    {
        $this->postscriptum = $postscriptum;

        return $this;
    }

    public function getMetaTitle(): ?string
    {
        return $this->metaTitle;
    }

    public function setMetaTitle(?string $metaTitle): self
    {
        $this->metaTitle = $metaTitle;

        return $this;
    }

    public function getMetaDescription(): ?string
    {
        return $this->metaDescription;
    }

    public function setMetaDescription(?string $metaDescription): self
    {
        $this->metaDescription = $metaDescription;

        return $this;
    }

    public function getMetaKeywords(): ?string
    {
        return $this->metaKeywords;
    }

    public function setMetaKeywords(?string $metaKeywords): self
    {
        $this->metaKeywords = $metaKeywords;

        return $this;
    }
}
