<?php
/**
 * Created by PhpStorm.
 * User: tpradatos
 * Date: 05/04/2018
 * Time: 13:00
 */

namespace Selection\Event;

use Selection\Model\SelectionFolder;
use Thelia\Core\Event\ActionEvent;

class SelectionFolderEvent extends ActionEvent
{
    /*---- GENERAL parts */
    protected $id;
    protected $title;
    protected $chapo;
    protected $description;
    protected $postscriptum;

    /*---- SEO parts */
    protected $url;
    protected $meta_title;
    protected $meta_description;
    protected $meta_keywords;

    /*---- LOCAL parts */
    protected $locale;
    protected $currentLocale;

    /*---- SELECTIONS OBJECT parts */
    /** @var SelectionFolder $folder  */
    protected $folder;

    /*----------------------------- General parts */

    public function __construct(SelectionFolder $folder = null)
    {
        $this->folder = $folder;
    }

    public function getSelectionFolder()
    {
        return $this->folder;
    }

    public function setSelectionFolder($folder)
    {
        $this->folder = $folder;

        return $this;
    }

    public function hasSelectionFolder()
    {

        return ! is_null($this->folder);
    }
    public function getId()
    {
        return $this->id;
    }

    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    public function getTitle()
    {
        return $this->title;
    }

    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    public function getChapo()
    {
        return $this->chapo;
    }

    public function setChapo($chapo)
    {
        $this->chapo = $chapo;

        return $this;
    }

    public function getDescription()
    {
        return $this->description;
    }

    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    public function getPostscriptum()
    {
        return $this->postscriptum;
    }

    public function setPostscriptum($postscriptum)
    {
        $this->postscriptum = $postscriptum;

        return $this;
    }

    public function getLocale()
    {
        return $this->locale;
    }

    public function setLocale($locale)
    {
        $this->locale = $locale;
        return $this;
    }

    /*----------------------------- SEO EVENT PARTS */
    public function getUrl()
    {
        return $this->url;
    }

    public function setUrl($url)
    {
        $this->url = $url;

        return $this;
    }

    public function getMetaTitle()
    {
        return $this->meta_title;
    }

    public function setMetaTitle($meta_title)
    {
        $this->meta_title = $meta_title;

        return $this;
    }

    public function getMetaDescription()
    {
        return $this->meta_description;
    }

    public function setMetaDescription($meta_description)
    {
        $this->meta_description = $meta_description;

        return $this;
    }

    public function getMetaKeywords()
    {
        return $this->meta_keywords;
    }

    public function setMetaKeywords($meta_keywords)
    {
        $this->meta_keywords = $meta_keywords;

        return $this;
    }
}