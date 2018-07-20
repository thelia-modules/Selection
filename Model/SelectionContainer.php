<?php

namespace Selection\Model;

use Propel\Runtime\Connection\ConnectionInterface;
use Selection\Event\SelectionContainerEvent;
use Selection\Event\SelectionEvents;
use Selection\Model\Base\SelectionContainer as BaseSelectionContainer;
use Thelia\Model\Tools\ModelEventDispatcherTrait;
use Thelia\Model\Tools\PositionManagementTrait;
use Thelia\Model\Tools\UrlRewritingTrait;

class SelectionContainer extends BaseSelectionContainer
{
    use UrlRewritingTrait;
    use ModelEventDispatcherTrait;
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

        $this->dispatchEvent(SelectionEvents::BEFORE_CREATE_SELECTION_CONTAINER, new SelectionContainerEvent($this));

        return true;
    }

    /**
     * {@inheritDoc}
     */
    public function postInsert(ConnectionInterface $con = null)
    {
        $this->dispatchEvent(SelectionEvents::AFTER_CREATE_SELECTION_CONTAINER, new SelectionContainerEvent($this));
    }

    /**
     * {@inheritDoc}
     */
    public function preUpdate(ConnectionInterface $con = null)
    {
        $this->dispatchEvent(SelectionEvents::BEFORE_UPDATE_SELECTION_CONTAINER, new SelectionContainerEvent($this));

        return true;
    }

    /**
     * {@inheritDoc}
     */
    public function postUpdate(ConnectionInterface $con = null)
    {
        $this->dispatchEvent(SelectionEvents::AFTER_UPDATE_SELECTION_CONTAINER, new SelectionContainerEvent($this));
    }

    /**
     * {@inheritDoc}
     */
    public function preDelete(ConnectionInterface $con = null)
    {
        $this->dispatchEvent(SelectionEvents::BEFORE_DELETE_SELECTION_CONTAINER, new SelectionContainerEvent($this));

        return true;
    }

    /**
     * {@inheritDoc}
     */
    public function postDelete(ConnectionInterface $con = null)
    {
        $this->dispatchEvent(SelectionEvents::AFTER_DELETE_SELECTION_CONTAINER, new SelectionContainerEvent($this));
    }
}
