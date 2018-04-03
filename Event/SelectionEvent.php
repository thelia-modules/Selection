<?php
/**
 * Created by PhpStorm.
 * User: mbruchet
 * Date: 21/03/2018
 * Time: 14:26
 */

namespace Selection\Event;

use Selection\Model\Selection;
use Thelia\Core\Event\ActionEvent;

class SelectionEvent extends ActionEvent
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
    /** @var Selection $selection  */
    protected $selection;

    /*----------------------------- General parts */
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

    /*----------------------------- Selection object Parts*/
    public function __construct(Selection $selection = null)
    {
        $this->selection = $selection;
    }

    public function getSelection()
    {
        return $this->selection;
    }

    public function setSelection($selection)
    {
        $this->selection = $selection;

        return $this;
    }

    public function hasSelection()
    {

        return ! is_null($this->selection);
    }
}
