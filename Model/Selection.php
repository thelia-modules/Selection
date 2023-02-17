<?php

namespace Selection\Model;

use Propel\Runtime\Connection\ConnectionInterface;
use Selection\Event\SelectionEvent;
use Selection\Event\SelectionEvents;
use Selection\Model\Base\Selection as BaseSelection;

use Thelia\Model\Tools\UrlRewritingTrait;
use Thelia\Model\Tools\PositionManagementTrait;

class Selection extends BaseSelection
{
    use UrlRewritingTrait;
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

        $con->getEventDispatcher()->dispatch(new SelectionEvent($this),SelectionEvents::BEFORE_CREATE_SELECTION);

        return true;
    }

    /**
     * {@inheritDoc}
     */
    public function postInsert(ConnectionInterface $con = null)
    {
        $con->getEventDispatcher()->dispatch(new SelectionEvent($this), SelectionEvents::AFTER_CREATE_SELECTION);
    }

    /**
     * {@inheritDoc}
     */
    public function preUpdate(ConnectionInterface $con = null)
    {
        $con->getEventDispatcher()->dispatch(new SelectionEvent($this), SelectionEvents::BEFORE_UPDATE_SELECTION);

        return true;
    }

    /**
     * {@inheritDoc}
     */
    public function postUpdate(ConnectionInterface $con = null)
    {
        $con->getEventDispatcher()->dispatch(new SelectionEvent($this), SelectionEvents::AFTER_UPDATE_SELECTION);
    }

    /**
     * {@inheritDoc}
     */
    public function preDelete(ConnectionInterface $con = null)
    {
        $con->getEventDispatcher()->dispatch(new SelectionEvent($this), SelectionEvents::BEFORE_DELETE_SELECTION);

        return true;
    }

    /**
     * {@inheritDoc}
     */
    public function postDelete(ConnectionInterface $con = null)
    {
        $con->getEventDispatcher()->dispatch(new SelectionEvent($this), SelectionEvents::AFTER_DELETE_SELECTION);
    }
}
