<?php

declare(strict_types=1);

namespace Selection\Api\Resource;

use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use Propel\Runtime\Map\TableMap;
use Selection\Model\Map\SelectionContentTableMap;
use Symfony\Component\Serializer\Annotation\Groups;
use Thelia\Api\Bridge\Propel\Attribute\CompositeIdentifiers;
use Thelia\Api\Bridge\Propel\Attribute\Relation;
use Thelia\Api\Bridge\Propel\Filter\OrderFilter;
use Thelia\Api\Bridge\Propel\Filter\SearchFilter;
use Thelia\Api\Resource\Content;
use Thelia\Api\Resource\PropelResourceInterface;
use Thelia\Api\Resource\PropelResourceTrait;

#[ApiResource(
    operations: [
        new Get(
            uriTemplate: '/front/selection_contents/{selection}/contents/{content}',
            normalizationContext: ['groups' => [self::GROUP_FRONT_READ, self::GROUP_FRONT_READ_SINGLE]],
        ),
    ],
    normalizationContext: ['groups' => [self::GROUP_FRONT_READ]],
)]
#[ApiFilter(
    filterClass: SearchFilter::class,
    properties: [
        'selection.id',
        'selection.code',
        'content.id',
    ],
)]
#[ApiFilter(
    filterClass: OrderFilter::class,
    properties: [
        'position',
    ],
)]
#[CompositeIdentifiers(['selection', 'content'])]
class SelectionContent implements PropelResourceInterface
{
    use PropelResourceTrait;

    public const GROUP_FRONT_READ = 'front:selection_content:read';
    public const GROUP_FRONT_READ_SINGLE = 'front:selection_content:read:single';

    #[Relation(targetResource: Selection::class, excludedGroups: [Selection::GROUP_FRONT_READ, Selection::GROUP_FRONT_READ_SINGLE])]
    #[Groups([self::GROUP_FRONT_READ])]
    public Selection $selection;

    #[Relation(targetResource: Content::class)]
    #[Groups([self::GROUP_FRONT_READ, Selection::GROUP_FRONT_READ_SINGLE])]
    public Content $content;

    #[Groups([self::GROUP_FRONT_READ, Selection::GROUP_FRONT_READ_SINGLE])]
    public ?int $position = null;

    #[Groups([self::GROUP_FRONT_READ])]
    public ?\DateTime $createdAt = null;

    #[Groups([self::GROUP_FRONT_READ])]
    public ?\DateTime $updatedAt = null;

    public function getSelection(): Selection
    {
        return $this->selection;
    }

    public function setSelection(Selection $selection): self
    {
        $this->selection = $selection;

        return $this;
    }

    public function getContent(): Content
    {
        return $this->content;
    }

    public function setContent(Content $content): self
    {
        $this->content = $content;

        return $this;
    }

    public function getPosition(): ?int
    {
        return $this->position;
    }

    public function setPosition(?int $position): self
    {
        $this->position = $position;

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
        return new SelectionContentTableMap();
    }
}
