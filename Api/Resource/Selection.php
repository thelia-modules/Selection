<?php

declare(strict_types=1);

namespace Selection\Api\Resource;

use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use Propel\Runtime\Map\TableMap;
use Selection\Model\Map\SelectionTableMap;
use Symfony\Component\Serializer\Annotation\Groups;
use Thelia\Api\Bridge\Propel\Attribute\Relation;
use Thelia\Api\Bridge\Propel\Filter\BooleanFilter;
use Thelia\Api\Bridge\Propel\Filter\OrderFilter;
use Thelia\Api\Bridge\Propel\Filter\SearchFilter;
use Thelia\Api\Resource\AbstractTranslatableResource;
use Thelia\Api\Resource\Content;
use Thelia\Api\Resource\I18nCollection;

#[ApiResource(
    operations: [
        new GetCollection(
            uriTemplate: '/front/selections',
        ),
        new Get(
            uriTemplate: '/front/selections/{id}',
            normalizationContext: ['groups' => [self::GROUP_FRONT_READ, self::GROUP_FRONT_READ_SINGLE, Content::GROUP_FRONT_READ]],
        ),
    ],
    normalizationContext: ['groups' => [self::GROUP_FRONT_READ, Content::GROUP_FRONT_READ]],
)]
#[ApiFilter(
    filterClass: SearchFilter::class,
    properties: [
        'id',
        'code',
    ],
)]
#[ApiFilter(
    filterClass: BooleanFilter::class,
    properties: [
        'visible',
    ],
)]
#[ApiFilter(
    filterClass: OrderFilter::class,
    properties: [
        'position',
    ],
)]
class Selection extends AbstractTranslatableResource
{
    public const GROUP_FRONT_READ = 'front:selection:read';
    public const GROUP_FRONT_READ_SINGLE = 'front:selection:read:single';

    #[Groups([self::GROUP_FRONT_READ])]
    public ?int $id = null;

    #[Groups([self::GROUP_FRONT_READ])]
    public ?string $code = null;

    #[Groups([self::GROUP_FRONT_READ])]
    public bool $visible;

    #[Groups([self::GROUP_FRONT_READ])]
    public ?int $position = null;

    #[Groups([self::GROUP_FRONT_READ])]
    public ?\DateTime $createdAt = null;

    #[Groups([self::GROUP_FRONT_READ])]
    public ?\DateTime $updatedAt = null;

    #[Groups([self::GROUP_FRONT_READ])]
    public I18nCollection $i18ns;

    /**
     * Related contents, through the selection_content join table.
     *
     * @var SelectionContent[]
     */
    #[Relation(targetResource: SelectionContent::class)]
    #[Groups([self::GROUP_FRONT_READ])]
    public array $selectionContents = [];

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): self
    {
        $this->id = $id;

        return $this;
    }

    public function getCode(): ?string
    {
        return $this->code;
    }

    public function setCode(?string $code): self
    {
        $this->code = $code;

        return $this;
    }

    public function isVisible(): bool
    {
        return $this->visible;
    }

    public function setVisible(bool $visible): self
    {
        $this->visible = $visible;

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

    /**
     * @return SelectionContent[]
     */
    public function getSelectionContents(): array
    {
        return $this->selectionContents;
    }

    /**
     * @param SelectionContent[] $selectionContents
     */
    public function setSelectionContents(array $selectionContents): self
    {
        $this->selectionContents = $selectionContents;

        return $this;
    }

    public static function getPropelRelatedTableMap(): ?TableMap
    {
        return new SelectionTableMap();
    }

    public static function getI18nResourceClass(): string
    {
        return SelectionI18n::class;
    }
}
