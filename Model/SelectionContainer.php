<?php

namespace Selection\Model;

use Propel\Runtime\Connection\ConnectionInterface;
use Selection\Event\SelectionContainerEvent;
use Selection\Event\SelectionEvents;
use Selection\Model\Base\SelectionContainer as BaseSelectionContainer;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Thelia\Model\Tools\ModelEventDispatcherTrait;
use Thelia\Model\Tools\PositionManagementTrait;
use Thelia\Model\Tools\UrlRewritingTrait;

class SelectionContainer extends BaseSelectionContainer
{
    use UrlRewritingTrait;
    use ModelEventDispatcherTrait;
    use PositionManagementTrait;
    const IMAGE_TYPE_LABEL = 'SelectionContainer';

    protected $dispatcher;

    public function __construct(EventDispatcherInterface $dispatcher)
    {
        $this->dispatcher = $dispatcher;
    }

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

        $this->dispatcher->dispatch(new SelectionContainerEvent($this), SelectionEvents::BEFORE_CREATE_SELECTION_CONTAINER);

        return true;
    }

    /**
     * {@inheritDoc}
     */
    public function postInsert(ConnectionInterface $con = null)
    {
        $this->dispatcher->dispatch(new SelectionContainerEvent($this), SelectionEvents::AFTER_CREATE_SELECTION_CONTAINER);
    }

    /**
     * {@inheritDoc}
     */
    public function preUpdate(ConnectionInterface $con = null)
    {
        $this->dispatcher->dispatch(new SelectionContainerEvent($this), SelectionEvents::BEFORE_UPDATE_SELECTION_CONTAINER);

        return true;
    }

    /**
     * {@inheritDoc}
     */
    public function postUpdate(ConnectionInterface $con = null)
    {
        $this->dispatcher->dispatch(new SelectionContainerEvent($this), SelectionEvents::AFTER_UPDATE_SELECTION_CONTAINER);
    }

    /**
     * {@inheritDoc}
     */
    public function preDelete(ConnectionInterface $con = null)
    {
        $this->dispatcher->dispatch(new SelectionContainerEvent($this), SelectionEvents::BEFORE_DELETE_SELECTION_CONTAINER);

        return true;
    }

    /**
     * {@inheritDoc}
     */
    public function postDelete(ConnectionInterface $con = null)
    {
        $this->dispatcher->dispatch(new SelectionContainerEvent($this), SelectionEvents::AFTER_DELETE_SELECTION_CONTAINER);
    }
}
