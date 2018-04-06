<?php
/**
 * Created by PhpStorm.
 * User: tpradatos
 * Date: 05/04/2018
 * Time: 16:26
 */

namespace Selection\Action;

use Propel\Runtime\Propel;
use Selection\Event\SelectionFolderEvent;
use Selection\Event\SelectionFolderEvents;
use Selection\Model\Map\SelectionFolderTableMap;
use Selection\Model\SelectionFolder;
use Selection\Model\SelectionFolderQuery;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Thelia\Action\BaseAction;
use Thelia\Core\Event\UpdateSeoEvent;

class SelectionFolderAction extends BaseAction implements EventSubscriberInterface
{
    public function create(SelectionFolderEvent $event)
    {
        $this->createOrUpdate($event, new SelectionFolder());
    }

    public function update(SelectionFolderEvent $event)
    {
        $model = $this->getSelectionFolder($event);

        $this->createOrUpdate($event, $model);
    }

    public function updateSeo(UpdateSeoEvent $event, $eventName, EventDispatcherInterface $dispatcher)
    {
        return $this->genericUpdateSeo(SelectionFolderQuery::create(), $event, $dispatcher);
    }

    public function delete(SelectionFolderEvent $event)
    {
        $this->getSelectionFolder()->delete();
    }

    protected function getSelectionFolder(SelectionFolderEvent $event)
    {
        $model = SelectionFolderQuery::create()
            ->findPk($event->getId());

        if (null === $model) {
            throw new \RuntimeException(sprintf(
                "The 'selection' id '%d' doesn't exist",
                $event->getId()
            ));
        }
        return $model;
    }


    protected function createOrUpdate($event, SelectionFolder $model)
    {
        $con = Propel::getConnection(SelectionFolderTableMap::DATABASE_NAME);
        $con->beginTransaction();
        try {
            if (null !== $locale = $event->getLocale()) {
                $model->setLocale($locale);
            }
            if (null !== $id = $event->getId()) {
                $model->setId($id);
            }

            if (null !== $title = $event->getTitle()) {
                $model->setTitle($title);
            }

            if (null !== $chapo = $event->getChapo()) {
                $model->setChapo($chapo);
            }

            if (null !== $description = $event->getDescription()) {
                $model->setDescription($description);
            }

            if (null !== $postscriptum = $event->getPostscriptum()) {
                $model->setPostscriptum($postscriptum);
            }

            $model->save();

            $event->setSelectionFolder($model);

            $con->commit();
        } catch (\Exception $e) {
            $con->rollBack();

            throw $e;
        }
    }

    /**
     * @param SelectionFolderEvent $event
     * @throws \Propel\Runtime\Exception\PropelException
     */
    public function toggleVisibility(SelectionFolderEvent $event)
    {
        $selection = $event->getSelectionFolder();

        $selection
            ->setVisible($selection->getVisible() ? false : true)
            ->save();

        $event->setSelectionFolder($selection);
    }


    public static function getSubscribedEvents()
    {
        return array(
            SelectionFolderEvents::SELECTION_FOLDER_CREATE => array("create", 128),
            SelectionFolderEvents::SELECTION_FOLDER_UPDATE => array("update", 128),
            SelectionFolderEvents::SELECTION_FOLDER_DELETE => array("delete", 128),
            SelectionFolderEvents::SELECTION_FOLDER_UPDATE_SEO => array("updateSeo", 128),
            SelectionFolderEvents::SELECTION_FOLDER_TOGGLE_VISIBILITY => array("toggleVisibility", 128),
        );
    }
}