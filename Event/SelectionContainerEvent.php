<?php

namespace Selection\Event;

use Selection\Model\SelectionContainer;
use Thelia\Core\Event\ActionEvent;

class SelectionContainerEvent extends ActionEvent
{
    protected $id;
    protected $code;
    protected $title;
    protected $chapo;
    protected $postscriptum;
    protected $description;
    private $selectionContainer;
    private $locale;

    /*----------------------------- Selection object Parts*/
    public function __construct(SelectionContainer $selectionContainer = null)
    {
        $this->selectionContainer = $selectionContainer;
    }

    /**
     * @param mixed $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @param mixed $title
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }

    /**
     * @param mixed $chapo
     */
    public function setChapo($chapo)
    {
        $this->chapo = $chapo;
    }

    /**
     * @param mixed $postscriptum
     */
    public function setPostscriptum($postscriptum)
    {
        $this->postscriptum = $postscriptum;
    }

    /**
     * @param mixed $desciption
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }

    public function setLocale($locale)
    {
        $this->locale = $locale;
        return $this;
    }

    public function hasSelection()
    {
        return ! is_null($this->selectionContainer);
    }

    public function getSelectionContainer()
    {
        return $this->selectionContainer;
    }

    public function setSelectionContainer($selectionContainer)
    {
        $this->selectionContainer = $selectionContainer;
    }

    /**
     * @return mixed
     */
    public function getLocale()
    {
        return $this->locale;
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return mixed
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @return mixed
     */
    public function getChapo()
    {
        return $this->chapo;
    }

    /**
     * @return mixed
     */
    public function getPostscriptum()
    {
        return $this->postscriptum;
    }

    /**
     * @return mixed
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @return mixed
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * @param mixed $code
     * @return $this
     */
    public function setCode($code)
    {
        $this->code = $code;
        return $this;
    }
}
