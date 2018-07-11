<?php
/**
 * Created by PhpStorm.
 * User: audreymartel
 * Date: 10/07/2018
 * Time: 10:14
 */

namespace Selection\Action;


use Propel\Runtime\ActiveQuery\ModelCriteria;
use Propel\Runtime\Propel;
use Selection\Event\SelectionContainerEvent;
use Selection\Event\SelectionEvents;
use Selection\Model\Map\SelectionContainerTableMap;
use Selection\Model\SelectionContainer;
use Selection\Model\SelectionContainerQuery;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Thelia\Action\BaseAction;
use Thelia\Core\Event\UpdatePositionEvent;
use Thelia\Core\Event\UpdateSeoEvent;
use Thelia\Log\Tlog;

class SelectionContainerAction extends BaseAction implements EventSubscriberInterface
{
    /** @var EventDispatcherInterface */
    protected $eventDispatcher;

    /**
     * @param SelectionContainerEvent $event
     * @throws \Exception
     */
    public function create(SelectionContainerEvent $event)
    {
        $this->createOrUpdate($event, new SelectionContainer());
    }

    /**
     * @param SelectionContainerEvent $event
     * @throws \Exception
     */
    public function update(SelectionContainerEvent $event)
    {
        $model = $this->getSelectionContainer($event);

        $this->createOrUpdate($event, $model);
    }

    /**
     * @param UpdateSeoEvent $event
     * @param $eventName
     * @param EventDispatcherInterface $dispatcher
     * @return mixed
     */
    public function updateSeo(
        UpdateSeoEvent $event,
        /** @noinspection PhpUnusedParameterInspection  */
        $eventName,
        EventDispatcherInterface $dispatcher)
    {
        return $this->genericUpdateSeo(SelectionContainerQuery::create(), $event, $dispatcher);
    }

    /**
     * @param SelectionContainerEvent $event
     * @throws \Propel\Runtime\Exception\PropelException
     */
    public function delete(SelectionContainerEvent $event)
    {
        $this->getSelectionContainer($event)->delete();
    }

    protected function getSelectionContainer(SelectionContainerEvent $event)
    {
        $model = SelectionContainerQuery::create()->findPk($event->getId());

        if (null === $model) {
            throw new \RuntimeException(sprintf(
                "SelectionContainer id '%d' doesn't exist",
                $event->getId()
            ));
        }
        return $model;
    }

    /**
     * @param SelectionContainerEvent $event
     * @param SelectionContainer $model
     * @throws \Exception
     */
    protected function createOrUpdate(SelectionContainerEvent $event, SelectionContainer $model)
    {
        $con = Propel::getConnection(SelectionContainerTableMap::DATABASE_NAME);
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

            $event->setSelectionContainer($model);

            $con->commit();
        } catch (\Exception $e) {
            $con->rollBack();
            Tlog::getInstance()->error($e->getMessage());
            throw $e;
        }
    }

    /**
     * @param SelectionContainerEvent $event
     * @throws \Propel\Runtime\Exception\PropelException
     */
    public function toggleVisibility(SelectionContainerEvent $event)
    {
        $selectionContainer = $event->getSelectionContainer();

        $selectionContainer
            ->setVisible($selectionContainer->getVisible() ? false : true)
            ->save()
        ;

        $event->setSelectionContainer($selectionContainer);
    }


    /**
     * @param UpdatePositionEvent $event
     * @param $eventName
     * @param EventDispatcherInterface $dispatcher
     */
    public function updatePosition(
        UpdatePositionEvent $event,
        /** @noinspection PhpUnusedParameterInspection */
        $eventName,
        EventDispatcherInterface $dispatcher
    )
    {
        $modelCriteria = SelectionContainerQuery::create()->filterById($event->getObjectId());
        $this->genericUpdateDelegatePosition(
            $modelCriteria,
            $event,
            $dispatcher
        );
    }

    protected function genericUpdateDelegatePosition(
        ModelCriteria $query,
        UpdatePositionEvent $event,
        EventDispatcherInterface $dispatcher = null)
    {

        if (null !== $object = $query->findOne()) {
            if (!isset(class_uses($object)['Thelia\Model\Tools\PositionManagementTrait'])) {
                throw new \InvalidArgumentException("Your model does not implement the PositionManagementTrait trait");
            }

            if (!is_null($dispatcher)) {
                $object->setDispatcher($dispatcher);
            }

            $mode = $event->getMode();

            if ($mode == UpdatePositionEvent::POSITION_ABSOLUTE) {
                $object->changeAbsolutePosition($event->getPosition());
            } elseif ($mode == UpdatePositionEvent::POSITION_UP) {
                $object->movePositionUp();
            } elseif ($mode == UpdatePositionEvent::POSITION_DOWN) {
                $object->movePositionDown();
            }
        }
    }

    /**
     * Returns an array of event names this subscriber wants to listen to.
     * @return array The event names to listen to
     */
    public static function getSubscribedEvents()
    {
        return array(
            SelectionEvents::SELECTION_CONTAINER_CREATE                   => array("create", 128),
            SelectionEvents::SELECTION_CONTAINER_UPDATE                   => array("update", 128),
            SelectionEvents::SELECTION_CONTAINER_DELETE                   => array("delete", 128),
            SelectionEvents::SELECTION_CONTAINER_UPDATE_SEO               => array("updateSeo", 128),
            SelectionEvents::SELECTION_CONTAINER_UPDATE_POSITION          => array("updatePosition", 128),
            SelectionEvents::SELECTION_CONTAINER_TOGGLE_VISIBILITY        => array("toggleVisibility", 128),
        );
    }
}