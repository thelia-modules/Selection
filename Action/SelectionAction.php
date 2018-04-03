<?php

namespace Selection\Action;

use Propel\Runtime\Propel;
use Selection\Event\SelectionEvent;
use Selection\Event\SelectionEvents;
use Selection\Model\Map\SelectionTableMap;
use Selection\Model\Selection;
use Selection\Model\SelectionQuery;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Thelia\Action\BaseAction;
use Thelia\Core\Event\UpdateSeoEvent;

class SelectionAction extends BaseAction implements EventSubscriberInterface
{
    public function create(SelectionEvent $event)
    {
        $this->createOrUpdate($event, new Selection());
    }

    public function update(SelectionEvent $event)
    {
        $model = $this->getSelection($event);

        $this->createOrUpdate($event, $model);
    }

    public function updateSeo(UpdateSeoEvent $event, $eventName, EventDispatcherInterface $dispatcher)
    {
        return $this->genericUpdateSeo(SelectionQuery::create(), $event, $dispatcher);
    }

    public function delete(SelectionEvent $event)
    {
        $this->getSelection($event)->delete();
    }

    protected function getSelection(SelectionEvent $event)
    {
        $model = SelectionQuery::create()
            ->findPk($event->getId());

        if (null === $model) {
            throw new \RuntimeException(sprintf(
                "The 'selection' id '%d' doesn't exist",
                $event->getId()
            ));
        }
        return $model;
    }


    protected function createOrUpdate($event, Selection $model)
    {
        $con = Propel::getConnection(SelectionTableMap::DATABASE_NAME);
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

            $event->setSelection($model);

            $con->commit();
        } catch (\Exception $e) {
            $con->rollBack();

            throw $e;
        }
    }

    /**
     * @param SelectionEvent $event
     * @throws \Propel\Runtime\Exception\PropelException
     */
    public function toggleVisibility(SelectionEvent $event)
    {
        $selection = $event->getSelection();

        $selection
            ->setVisible($selection->getVisible() ? false : true)
            ->save()
        ;

        $event->setSelection($selection);
    }


    public static function getSubscribedEvents()
    {
        return array(
            SelectionEvents::SELECTION_CREATE              => array("create", 128),
            SelectionEvents::SELECTION_UPDATE              => array("update", 128),
            SelectionEvents::SELECTION_DELETE              => array("delete", 128),
            SelectionEvents::SELECTION_UPDATE_SEO          => array("updateSeo", 128),
            SelectionEvents::SELECTION_TOGGLE_VISIBILITY   => array("toggleVisibility", 128),
        );
    }
}