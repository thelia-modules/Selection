<?php

namespace Selection\Model;

use Propel\Runtime\ActiveQuery\Criteria;
use Propel\Runtime\ActiveQuery\ModelCriteria;
use Propel\Runtime\Connection\ConnectionInterface;
use Propel\Runtime\Exception\PropelException;
use Selection\Model\Base\SelectionImage as BaseSelectionImage;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\Router;
use Thelia\Core\File\FileModelInterface;
use Thelia\Core\File\FileModelParentInterface;
use Thelia\Model\Breadcrumb\BreadcrumbInterface;
use Thelia\Model\Breadcrumb\CatalogBreadcrumbTrait;
use Thelia\Model\ConfigQuery;
use Thelia\Model\Tools\PositionManagementTrait;

class SelectionImage extends BaseSelectionImage implements FileModelInterface, BreadcrumbInterface
{
    use CatalogBreadcrumbTrait;
    use PositionManagementTrait;

    protected function addCriteriaToPositionQuery($query): void
    {
        $query->filterById($this->getId());
    }
    /**
     * @inheritDoc
     * @throws PropelException
     */
    public function preInsert(?ConnectionInterface $con = null): bool
    {
        $lastImage = SelectionImageQuery::create()
            ->filterBySelectionId(
                $this->getSelection()
                    ->getId()
            )
            ->orderByPosition(Criteria::DESC)
            ->findOne();

        if (null !== $lastImage) {
            $position =  $lastImage->getPosition() + 1;
        } else {
            $position = 1;
        }

        $this->setPosition($position);

        return true;
    }

    public function setParentId(int $parentId): static
    {
        $this->setSelectionId($parentId);

        return $this;
    }

    public function getUpdateFormId(): string
    {
        return 'admin.selection.image.modification';
    }

    public function getUploadDir(): string
    {
        $uploadDir = ConfigQuery::read('images_library_path');
        if ($uploadDir === null) {
            $uploadDir = THELIA_LOCAL_DIR . 'media' . DS . 'images';
        } else {
            $uploadDir = THELIA_ROOT . $uploadDir;
        }

        return $uploadDir . DS . 'selection';
    }


    public function getRedirectionUrl(): string
    {
        return '/admin/selection/update/' . $this->getSelectionId();
    }

    public function getParentId(): int
    {
        return (int) $this->getSelectionId();
    }

    public function getParentFileModel(): FileModelParentInterface
    {
        return new Selection();
    }

    public function getQueryInstance(): SelectionImageQuery|ModelCriteria
    {
        return SelectionImageQuery::create();
    }

    /**
     * The interface of Thelia 3 asks for a string, the generated getter may answer null.
     */
    public function getFile(): string
    {
        return parent::getFile() ?? '';
    }

    /**
     * @throws PropelException
     */
    public function getBreadcrumb(Router $router, $tab, $locale): array
    {
        /** @var SelectionImage $selection */
        $selection = $this->getSelection();

        $selection->setLocale($locale);

        $breadcrumb[$selection->getTitle()] = sprintf(
            "%s?current_tab=%s",
            $router->generate(
                'selection.update',
                ['selectionId' => $selection->getId()],
                UrlGeneratorInterface::ABSOLUTE_URL
            ),
            $tab
        );

        return $breadcrumb;
    }
}
