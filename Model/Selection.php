<?php

namespace Selection\Model;

use Propel\Runtime\Connection\ConnectionInterface;
use Selection\Event\SelectionEvent;
use Selection\Event\SelectionEvents;
use Selection\Model\Base\Selection as BaseSelection;
use Thelia\Model\Tools\ModelEventDispatcherTrait;
use Thelia\Model\Tools\UrlRewritingTrait;
use Thelia\Model\Tools\PositionManagementTrait;

class Selection extends BaseSelection
{
    use UrlRewritingTrait;
    use ModelEventDispatcherTrait;
    use PositionManagementTrait;

    public function getRewrittenUrlViewName()
    {
        return 'selection';
    }

    /**
     * {@inheritDoc}
     */
    public function preInsert(ConnectionInterface $con = null)
    {
        // Set the current position for the new object
        $this->setPosition($this->getNextPosition());

        $this->dispatchEvent(SelectionEvents::BEFORE_CREATE_SELECTION, new SelectionEvent($this));

        return true;
    }

    /**
     * {@inheritDoc}
     */
    public function postInsert(ConnectionInterface $con = null)
    {
        $this->dispatchEvent(SelectionEvents::AFTER_CREATE_SELECTION, new SelectionEvent($this));
    }

    /**
     * {@inheritDoc}
     */
    public function preUpdate(ConnectionInterface $con = null)
    {
        $this->dispatchEvent(SelectionEvents::BEFORE_UPDATE_SELECTION, new SelectionEvent($this));

        return true;
    }

    /**
     * {@inheritDoc}
     */
    public function postUpdate(ConnectionInterface $con = null)
    {
        $this->dispatchEvent(SelectionEvents::AFTER_UPDATE_SELECTION, new SelectionEvent($this));
    }

    /**
     * {@inheritDoc}
     */
    public function preDelete(ConnectionInterface $con = null)
    {
        $this->dispatchEvent(SelectionEvents::BEFORE_DELETE_SELECTION, new SelectionEvent($this));

        return true;
    }

    /**
     * {@inheritDoc}
     */
    public function postDelete(ConnectionInterface $con = null)
    {
        $this->dispatchEvent(SelectionEvents::AFTER_DELETE_SELECTION, new SelectionEvent($this));
    }
}
