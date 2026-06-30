<?php

declare(strict_types=1);

namespace Selection\Api\Resource;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use Propel\Runtime\Map\TableMap;
use Selection\Model\Map\SelectionContentTableMap;
use Symfony\Component\Serializer\Annotation\Groups;
use Thelia\Api\Bridge\Propel\Attribute\CompositeIdentifiers;
use Thelia\Api\Bridge\Propel\Attribute\Relation;
use Thelia\Api\Resource\Content;
use Thelia\Api\Resource\PropelResourceInterface;
use Thelia\Api\Resource\PropelResourceTrait;

#[ApiResource(
    operations: [
        new Get(
            uriTemplate: '/front/selection-contents/{selection}/contents/{content}',
        ),
    ],
    normalizationContext: ['groups' => [self::GROUP_FRONT_READ, Content::GROUP_FRONT_READ]],
)]
#[CompositeIdentifiers(['selection', 'content'])]
class SelectionContent implements PropelResourceInterface
{
    use PropelResourceTrait;

    public const GROUP_FRONT_READ = 'front:selection_content:read';

    #[Relation(targetResource: Selection::class)]
    #[Groups([self::GROUP_FRONT_READ])]
    public Selection $selection;

    #[Relation(targetResource: Content::class)]
    #[Groups([self::GROUP_FRONT_READ, Selection::GROUP_FRONT_READ])]
    public Content $content;

    #[Groups([self::GROUP_FRONT_READ, Selection::GROUP_FRONT_READ])]
    public ?int $position = null;

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

    public static function getPropelRelatedTableMap(): ?TableMap
    {
        return new SelectionContentTableMap();
    }
}
