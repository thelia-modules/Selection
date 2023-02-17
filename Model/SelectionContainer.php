<?php

namespace Selection\Model;

use Propel\Runtime\Connection\ConnectionInterface;
use Selection\Event\SelectionContainerEvent;
use Selection\Event\SelectionEvents;
use Selection\Model\Base\SelectionContainer as BaseSelectionContainer;
use Thelia\Model\Tools\PositionManagementTrait;
use Thelia\Model\Tools\UrlRewritingTrait;

class SelectionContainer extends BaseSelectionContainer
{
    use UrlRewritingTrait;
    use PositionManagementTrait;
    const IMAGE_TYPE_LABEL = 'SelectionContainer';

    public function getRewrittenUrlViewName()
    {
        return 'selection_container';
    }

    /**
     * {@inheritDoc}
     */
    public function preInsert(ConnectionInterface $con = null)
    {
        // Set the current position for the new object
        $this->setPosition($this->getNextPosition());

        $con->getEventDispatcher()->dispatch(new SelectionContainerEvent($this), SelectionEvents::BEFORE_CREATE_SELECTION_CONTAINER);

        return true;
    }

    /**
     * {@inheritDoc}
     */
    public function postInsert(ConnectionInterface $con = null)
    {
        $con->getEventDispatcher()->dispatch(new SelectionContainerEvent($this), SelectionEvents::AFTER_CREATE_SELECTION_CONTAINER);
    }

    /**
     * {@inheritDoc}
     */
    public function preUpdate(ConnectionInterface $con = null)
    {
        $con->getEventDispatcher()->dispatch(new SelectionContainerEvent($this), SelectionEvents::BEFORE_UPDATE_SELECTION_CONTAINER);

        return true;
    }

    /**
     * {@inheritDoc}
     */
    public function postUpdate(ConnectionInterface $con = null)
    {
        $con->getEventDispatcher()->dispatch(new SelectionContainerEvent($this), SelectionEvents::AFTER_UPDATE_SELECTION_CONTAINER);
    }

    /**
     * {@inheritDoc}
     */
    public function preDelete(ConnectionInterface $con = null)
    {
        $con->getEventDispatcher()->dispatch(new SelectionContainerEvent($this), SelectionEvents::BEFORE_DELETE_SELECTION_CONTAINER);

        return true;
    }

    /**
     * {@inheritDoc}
     */
    public function postDelete(ConnectionInterface $con = null)
    {
        $con->getEventDispatcher()->dispatch(new SelectionContainerEvent($this), SelectionEvents::AFTER_DELETE_SELECTION_CONTAINER);
    }
}
