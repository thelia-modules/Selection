<?php

namespace Selection\Model;

use Propel\Runtime\ActiveQuery\Criteria;
use Propel\Runtime\Connection\ConnectionInterface;
use Selection\Model\Base\SelectionContainerImage as BaseSelectionContainerImage;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Routing\Router;
use Thelia\Files\FileModelInterface;
use Thelia\Model\Breadcrumb\BreadcrumbInterface;
use Thelia\Model\Breadcrumb\CatalogBreadcrumbTrait;
use Thelia\Model\ConfigQuery;
use Thelia\Model\Tools\ModelEventDispatcherTrait;
use Thelia\Model\Tools\PositionManagementTrait;

class SelectionContainerImage extends BaseSelectionContainerImage implements FileModelInterface, BreadcrumbInterface
{
    use CatalogBreadcrumbTrait;
    use PositionManagementTrait;
    use ModelEventDispatcherTrait;

    protected function addCriteriaToPositionQuery($query)
    {
        $query->filterById($this->getId());
    }
    /**
     * @inheritDoc
     */
    public function preInsert(ConnectionInterface $con = null)
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

    public function setParentId($parentId)
    {
        $this->setSelectionContainerId($parentId);
        return $this;
    }

    public function getUpdateFormId()
    {
        return 'admin_selection_image_modification';
    }

    public function getUploadDir()
    {
        $uploadDir = ConfigQuery::read('images_library_path');
        if ($uploadDir === null) {
            $uploadDir = THELIA_LOCAL_DIR . 'media' . DS . 'images';
        } else {
            $uploadDir = THELIA_ROOT . $uploadDir;
        }

        return $uploadDir . DS . strtolower(SelectionContainer::IMAGE_TYPE_LABEL);
    }


    public function getRedirectionUrl()
    {
        return '/admin/selection/container/update/' . $this->getSelectionContainerId();
    }

    public function getParentId()
    {
        return $this->getId();
    }

    public function getParentFileModel()
    {
        return new SelectionContainer();
    }

    public function getQueryInstance()
    {
        return SelectionContainerImageQuery::create();
    }

    /**
     * @param Router $router
     * @param ContainerInterface $container
     * @param string $tab
     * @param string $locale
     * @return array|mixed
     * @throws \Propel\Runtime\Exception\PropelException
     */
    public function getBreadcrumb(Router $router, ContainerInterface $container, $tab, $locale)
    {
        /** @var SelectionContainerImage $selectionContainer */
        $selectionContainer = $this->getSelectionContainer();

        $selectionContainer->setLocale($locale);

        $breadcrumb[$selectionContainer->getTitle()] = sprintf(
            "%s?current_tab=%s",
            $router->generate(
                'admin.selection.container.view',
                ['selectionContainerId' => $selectionContainer->getId()],
                Router::ABSOLUTE_URL
            ),
            $tab
        );

        return $breadcrumb;
    }
}
