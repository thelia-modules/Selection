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
use Thelia\Files\FileModelInterface;
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
    public function preInsert(ConnectionInterface $con = null): bool
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

    public function setParentId($parentId): SelectionImage|static
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
        return '/admin/selection/update/' . $this->getId();
    }

    public function getParentId(): int
    {
        return $this->getId();
    }

    public function getParentFileModel(): Selection
    {
        return new Selection();
    }

    public function getQueryInstance(): SelectionImageQuery|ModelCriteria
    {
        return SelectionImageQuery::create();
    }

    /**
     * @throws PropelException
     */
    public function getBreadcrumb(Router $router, ContainerInterface $container, $tab, $locale)
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
