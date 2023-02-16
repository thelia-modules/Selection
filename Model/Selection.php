<?php

namespace Selection\Model;

use Propel\Runtime\Connection\ConnectionInterface;
use Selection\Event\SelectionEvent;
use Selection\Event\SelectionEvents;
use Selection\Model\Base\Selection as BaseSelection;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Thelia\Model\Tools\ModelEventDispatcherTrait;
use Thelia\Model\Tools\UrlRewritingTrait;
use Thelia\Model\Tools\PositionManagementTrait;

class Selection extends BaseSelection
{
    use UrlRewritingTrait;
    use ModelEventDispatcherTrait;
    use PositionManagementTrait;

    protected $dispatcher;

    public function __construct(EventDispatcherInterface $dispatcher)
    {
        $this->dispatcher = $dispatcher;
    }

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

        $this->dispatcher->dispatch(new SelectionEvent($this),SelectionEvents::BEFORE_CREATE_SELECTION);

        return true;
    }

    /**
     * {@inheritDoc}
     */
    public function postInsert(ConnectionInterface $con = null)
    {
        $this->dispatcher->dispatch(new SelectionEvent($this), SelectionEvents::AFTER_CREATE_SELECTION);
    }

    /**
     * {@inheritDoc}
     */
    public function preUpdate(ConnectionInterface $con = null)
    {
        $this->dispatcher->dispatch(new SelectionEvent($this), SelectionEvents::BEFORE_UPDATE_SELECTION);

        return true;
    }

    /**
     * {@inheritDoc}
     */
    public function postUpdate(ConnectionInterface $con = null)
    {
        $this->dispatcher->dispatch(new SelectionEvent($this), SelectionEvents::AFTER_UPDATE_SELECTION);
    }

    /**
     * {@inheritDoc}
     */
    public function preDelete(ConnectionInterface $con = null)
    {
        $this->dispatcher->dispatch(new SelectionEvent($this), SelectionEvents::BEFORE_DELETE_SELECTION);

        return true;
    }

    /**
     * {@inheritDoc}
     */
    public function postDelete(ConnectionInterface $con = null)
    {
        $this->dispatcher->dispatch(new SelectionEvent($this), SelectionEvents::AFTER_DELETE_SELECTION);
    }
}
