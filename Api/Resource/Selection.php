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
use Thelia\Api\Bridge\Propel\Filter\NotInFilter;
use Thelia\Api\Bridge\Propel\Filter\OrderFilter;
use Thelia\Api\Bridge\Propel\Filter\SearchFilter;
use Thelia\Api\Resource\AbstractTranslatableResource;
use Thelia\Api\Resource\I18nCollection;
use Thelia\Model\Tools\UrlRewritingTrait;

#[ApiResource(
    operations: [
        new GetCollection(
            uriTemplate: '/front/selections',
        ),
        new Get(
            uriTemplate: '/front/selections/{id}',
            normalizationContext: ['groups' => [self::GROUP_FRONT_READ, self::GROUP_FRONT_READ_SINGLE]],
        ),
    ],
    normalizationContext: ['groups' => [self::GROUP_FRONT_READ]],
)]
#[ApiFilter(
    filterClass: SearchFilter::class,
    properties: [
        'id',
        'code',
        'selectionProducts.product.id',
        'selectionContents.content.id',
        'selectionContainerAssociatedSelections.selectionContainer.id',
    ],
)]
#[ApiFilter(
    filterClass: NotInFilter::class,
    properties: [
        'id',
        'code',
    ],
)]
#[ApiFilter(
    filterClass: OrderFilter::class,
    properties: [
        'id',
        'code',
        'position',
        'createdAt',
        'updatedAt',
    ],
)]
#[ApiFilter(
    filterClass: BooleanFilter::class,
    properties: [
        'visible',
    ],
)]
class Selection extends AbstractTranslatableResource
{
    use UrlRewritingTrait;

    public const GROUP_FRONT_READ = 'front:selection:read';
    public const GROUP_FRONT_READ_SINGLE = 'front:selection:read:single';

    #[Groups([
        self::GROUP_FRONT_READ,
        SelectionProduct::GROUP_FRONT_READ,
        SelectionContent::GROUP_FRONT_READ,
        SelectionContainerAssociatedSelection::GROUP_FRONT_READ,
        SelectionContainer::GROUP_FRONT_READ_SINGLE,
    ])]
    public ?int $id = null;

    #[Groups([self::GROUP_FRONT_READ, SelectionContainer::GROUP_FRONT_READ_SINGLE])]
    public ?string $code = null;

    #[Groups([self::GROUP_FRONT_READ, SelectionContainer::GROUP_FRONT_READ_SINGLE])]
    public bool $visible;

    #[Groups([self::GROUP_FRONT_READ, SelectionContainer::GROUP_FRONT_READ_SINGLE])]
    public ?int $position = null;

    #[Groups([self::GROUP_FRONT_READ])]
    public ?\DateTime $createdAt = null;

    #[Groups([self::GROUP_FRONT_READ])]
    public ?\DateTime $updatedAt = null;

    #[Relation(targetResource: SelectionProduct::class)]
    #[Groups([self::GROUP_FRONT_READ_SINGLE])]
    public array $selectionProducts = [];

    #[Relation(targetResource: SelectionContent::class)]
    #[Groups([self::GROUP_FRONT_READ_SINGLE])]
    public array $selectionContents = [];

    #[Relation(
        targetResource: SelectionContainerAssociatedSelection::class,
        excludedGroups: [SelectionContainer::GROUP_FRONT_READ, SelectionContainer::GROUP_FRONT_READ_SINGLE],
    )]
    #[Groups([self::GROUP_FRONT_READ_SINGLE])]
    public array $selectionContainerAssociatedSelections = [];

    #[Groups([self::GROUP_FRONT_READ, SelectionContainer::GROUP_FRONT_READ_SINGLE])]
    public I18nCollection $i18ns;

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

    public function getSelectionProducts(): array
    {
        return $this->selectionProducts;
    }

    public function setSelectionProducts(array $selectionProducts): self
    {
        $this->selectionProducts = $selectionProducts;

        return $this;
    }

    public function getSelectionContents(): array
    {
        return $this->selectionContents;
    }

    public function setSelectionContents(array $selectionContents): self
    {
        $this->selectionContents = $selectionContents;

        return $this;
    }

    public function getSelectionContainerAssociatedSelections(): array
    {
        return $this->selectionContainerAssociatedSelections;
    }

    public function setSelectionContainerAssociatedSelections(array $selectionContainerAssociatedSelections): self
    {
        $this->selectionContainerAssociatedSelections = $selectionContainerAssociatedSelections;

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

    #[Groups([self::GROUP_FRONT_READ])]
    public function getPublicUrl(): string
    {
        /** @var \Selection\Model\Selection $propelModel */
        $propelModel = $this->getPropelModel();

        if (!$locale = $propelModel?->getLocale()) {
            $locale = $this->getDefaultLocale();
        }

        return $this->getUrl($locale);
    }

    public function getRewrittenUrlViewName(): string
    {
        /** @var \Selection\Model\Selection $propelModel */
        $propelModel = $this->getPropelModel();

        return $propelModel?->getRewrittenUrlViewName() ?: '';
    }
}
