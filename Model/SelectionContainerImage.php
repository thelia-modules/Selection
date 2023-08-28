<?php

namespace Selection\Model;

use Propel\Runtime\ActiveQuery\Criteria;
use Propel\Runtime\ActiveQuery\ModelCriteria;
use Propel\Runtime\Connection\ConnectionInterface;
use Propel\Runtime\Exception\PropelException;
use Selection\Model\Base\SelectionContainerImage as BaseSelectionContainerImage;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\Router;
use Thelia\Files\FileModelInterface;
use Thelia\Model\Breadcrumb\BreadcrumbInterface;
use Thelia\Model\Breadcrumb\CatalogBreadcrumbTrait;
use Thelia\Model\ConfigQuery;
use Thelia\Model\Tools\PositionManagementTrait;

class SelectionContainerImage extends BaseSelectionContainerImage implements FileModelInterface, BreadcrumbInterface
{
    use CatalogBreadcrumbTrait;
    use PositionManagementTrait;

    protected function addCriteriaToPositionQuery($query): void
    {
        $query->filterById($this->getId());
    }
    /**
     * @inheritDoc
     */
    public function preInsert(ConnectionInterface $con = null): bool
    {
        $lastImage = SelectionImageQuery::create()
            ->filterBySelectionId(
                $this->getSelectionContainerId()
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

    public function setParentId($parentId): SelectionContainerImage|static
    {
        $this->setSelectionContainerId($parentId);
        return $this;
    }

    public function getUpdateFormId(): string
    {
        return 'admin_selection_image_modification';
    }

    public function getUploadDir(): string
    {
        $uploadDir = ConfigQuery::read('images_library_path');
        if ($uploadDir === null) {
            $uploadDir = THELIA_LOCAL_DIR . 'media' . DS . 'images';
        } else {
            $uploadDir = THELIA_ROOT . $uploadDir;
        }

        return $uploadDir . DS . strtolower(SelectionContainer::IMAGE_TYPE_LABEL);
    }


    public function getRedirectionUrl(): string
    {
        return '/admin/selection/container/update/' . $this->getSelectionContainerId();
    }

    public function getParentId(): int
    {
        return $this->getId();
    }

    public function getParentFileModel(): SelectionContainer
    {
        return new SelectionContainer();
    }

    public function getQueryInstance(): SelectionContainerImageQuery|ModelCriteria
    {
        return SelectionContainerImageQuery::create();
    }

    /**
     * @param Router $router
     * @param string $tab
     * @param string $locale
     * @return array
     * @throws PropelException
     */
    public function getBreadcrumb(Router $router, $tab, $locale): array
    {
        /** @var SelectionContainerImage $selectionContainer */
        $selectionContainer = $this->getSelectionContainer();

        $selectionContainer->setLocale($locale);

        $breadcrumb[$selectionContainer->getTitle()] = sprintf(
            "%s?current_tab=%s",
            $router->generate(
                'admin.selection.container.view',
                ['selectionContainerId' => $selectionContainer->getId()],
                UrlGeneratorInterface::ABSOLUTE_URL
            ),
            $tab
        );

        return $breadcrumb;
    }
}
