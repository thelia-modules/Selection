<?php
/**
 * Created by PhpStorm.
 * User: mbruchet
 * Date: 16/03/2018
 * Time: 14:14
 */

namespace Selection\Controller;

use Propel\Runtime\ActiveQuery\Criteria;
use Selection\Event\SelectionEvent;
use Selection\Form\SelectionCreateForm;
use Selection\Form\SelectionFolderCreateForm;
use Selection\Form\SelectionUpdateForm;
use Selection\Model\Selection;
use Selection\Model\SelectionContentQuery;
use Selection\Model\SelectionFolder;
use Selection\Model\SelectionFolderQuery;
use Selection\Model\SelectionI18nQuery;
use Selection\Model\SelectionProductQuery;
use Selection\Model\SelectionQuery;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Thelia\Controller\Admin\AbstractSeoCrudController;
use Thelia\Core\Security\AccessManager;
use Thelia\Core\Security\Resource\AdminResources;
use Selection\Event\SelectionEvents;
use Thelia\Tools\URL;

class SelectionUpdateController extends AbstractSeoCrudController
{
    protected $currentRouter = "router.Selection";

    /**
     * Save content of the selection
     *
     * @return \Symfony\Component\HttpFoundation\Response|\Thelia\Core\HttpFoundation\Response
     * @throws \Propel\Runtime\Exception\PropelException
     */
    public function saveSelection()
    {

        $form = new SelectionUpdateForm($this->getRequest());

        $validForm  =   $this->validateForm($form);
        $data       =   $validForm->getData();

        $selectionID            = $data['selection_id'];
        $selectionTitle         = $data['selection_title'];
        $selectionChapo         = $data['selection_chapo'];
        $selectionDescription   = $data['selection_description'];
        $selectionPostscriptum  = $data['selection_postscriptum'];

        $lang = $this->getRequest()->getSession()->get('thelia.current.lang');

        $aSelection = SelectionI18nQuery::create()
            ->filterById($selectionID)
            ->filterByLocale($lang->getLocale())
            ->findOne();

        $aSelection
            ->setTitle($selectionTitle)
            ->setChapo($selectionChapo)
            ->setDescription($selectionDescription)
            ->setPostscriptum($selectionPostscriptum);

        $aSelection->save();

        if ($validForm->get('save_and_close')->isClicked()) {
            return $this->render("electionlist");
        }


        return $this->generateRedirectFromRoute('selection.update', [], ['selectionId' => $selectionID], null);
    }

    public function createSelection()
    {
        $form       = new SelectionCreateForm($this->getRequest());

        $validForm  = $this->validateForm($form);
        $data       = $validForm->getData();

        $selectionTitle         = $data['selection_title'];
        $selectionChapo         = $data['selection_chapo'];
        $selectionDescription   = $data['selection_description'];
        $selectionPostscriptum  = $data['selection_postscriptum'];

        $lang       = $this->getRequest()->getSession()->get('thelia.current.lang');

        $parent = $this->getRequest()->get('parentId');


        /*------------------------- Add in Selection table */
        $selection  = new Selection();
        $lastSelection   = SelectionQuery::create()
                        ->orderByPosition(Criteria::DESC)
//                        ->useSelectionSelectionFolderQuery()
//                            ->filterByDefaultFolder(true)
//                            ->filterBySelectionFolderId($parent, Criteria::IN)
//                        ->endUse()
                        ->findOne();

        $date       = new \DateTime();

        if (null !== $lastSelection) {
            $position =  $lastSelection->getPosition() + 1;
        } else {
            $position = 1;
        }

        try {
            $selection
                ->setCreatedAt($date->format('Y-m-d H:i:s'))
                ->setUpdatedAt($date->format('Y-m-d H:i:s'))
                ->setVisible(1)
                ->setPosition($position)
                ->setLocale($lang->getLocale())
                ->setTitle($selectionTitle)
                ->setChapo($selectionChapo)
                ->setDescription($selectionDescription)
                ->setPostscriptum($selectionPostscriptum);

            $selection->save();

            $m = [
                'message' => 'Selection : '.$selectionTitle.' has been created and it have #'
                    .$selection->getId().' as reference'
                ];
        } catch (\Exception $e) {
            $m = ['message' => $e->getMessage()];
        }


        return $this->render("selectionlist", $m);
    }



    public function deleteSelection()
    {
        $selectionID = $this->getRequest()->get('selection_ID');


        try {
            $selection = SelectionQuery::create()
                ->findOneById($selectionID);
            if (null !== $selection) {
                $selection->delete();
                $m = ['message' => "Selection #".$selectionID." has been deleted."];
            } else {
                $m = ['message' => "Selection #".$selectionID." doesn't exists so we can't delete it."];
            }
        } catch (\Exception $e) {
            $m = ['message' => $e->getMessage()];
        }

        return $this->render("selectionlist", $m);
    }

    public function deleteRelatedProduct()
    {
        $selectionID = $this->getRequest()->get('selectionID');
        $productID   = $this->getRequest()->get('productID');

        try {
            $selection = SelectionProductQuery::create()
                ->filterByProductId($productID)
                ->findOneBySelectionId($selectionID);
            if (null !== $selection) {
                $selection->delete();
                $m = ['message' => "Product #".$productID." related to #".$selectionID." has been deleted."];
            } else {
                $m = ['message' => "Product #".$productID." related to #"
                    .$selectionID." doesn't exists so we can't delete it."];
            }
        } catch (\Exception $e) {
            $m = ['message' => $e->getMessage()];
        }

        return $this->generateRedirectFromRoute('selection.update', [], ['selectionId' => $selectionID], null);
    }
    public function deleteRelatedContent()
    {
        $selectionID = $this->getRequest()->get('selectionID');
        $contentID   = $this->getRequest()->get('contentID');

        try {
            $selection = SelectionContentQuery::create()
                ->filterByContentId($contentID)
                ->findOneBySelectionId($selectionID);
            if (null !== $selection) {
                $selection->delete();
                $m = ['message' => "Product #".$contentID." related to #".$selectionID." has been deleted."];
            } else {
                $m = ['message' => "Product #".$contentID." related to #"
                    .$selectionID." doesn't exists so we can't delete it."];
            }
        } catch (\Exception $e) {
            $m = ['message' => $e->getMessage()];
        }

        return $this->generateRedirectFromRoute('selection.update', [], ['selectionId' => $selectionID], null);
    }
    /*--------------------------    Part Controller SEO */
    public function __construct()
    {
        parent::__construct(
            'selection',
            'selection_id',
            'order',
            AdminResources::MODULE,
            SelectionEvents::SELECTION_CREATE,
            SelectionEvents::SELECTION_UPDATE,
            SelectionEvents::SELECTION_DELETE,
            null,
            null,
            SelectionEvents::SELECTION_UPDATE_SEO,
            'Selection'
        );
    }

    protected function getCreationForm()
    {
        return $this->createForm('admin.selection.update');
    }

    protected function getUpdateForm($data = array())
    {
        if (!is_array($data)) {
            $data = array();
        }

        return $this->createForm('admin.selection.update', 'form', $data);
    }

    protected function hydrateObjectForm($object)
    {
        $this->hydrateSeoForm($object);

        $data = array(
            'selection_id'          => $object->getId(),
            'id'                    => $object->getId(),
            'locale'                => $object->getLocale(),
            'selection_title'       => $object->getTitle(),
            'selection_chapo'       => $object->getChapo(),
            'selection_description' => $object->getDescription(),
            'selection_postscriptum'=> $object->getPostscriptum(),
            'current_id'            => $object->getId(),
        );

        return $this->getUpdateForm($data);
    }

    protected function getCreationEvent($formData)
    {
        $event = new SelectionEvent();

        $event->setId($formData['selection_id']);
        $event->setTitle($formData['selection_title']);
        $event->setChapo($formData['selection_chapo']);
        $event->setDescription($formData['selection_description']);
        $event->setPostscriptum($formData['selection_postscriptum']);

        return $event;
    }

    protected function getUpdateEvent($formData)
    {
        $selection = SelectionQuery::create()->findPk($formData['selection_id']);
        $event = new SelectionEvent($selection);

        $event->setId($formData['selection_id']);
        $event->setTitle($formData['selection_title']);
        $event->setChapo($formData['selection_chapo']);
        $event->setDescription($formData['selection_description']);
        $event->setPostscriptum($formData['selection_postscriptum']);
        $event->setLocale($this->getRequest()->getSession()->get('thelia.current.lang')->getLocale());
        return $event;
    }

    protected function getDeleteEvent()
    {
        $event = new SelectionEvent();

        $event->setId($this->getRequest()->request->get('selection_id'));

        return $event;
    }

    protected function eventContainsObject($event)
    {
        return $event->hasSelection();
    }

    protected function getObjectFromEvent($event)
    {
        return $event->getSelection();
    }

    protected function getExistingObject()
    {
        $selection = SelectionQuery::create()
            ->findPk($this->getRequest()->get('selectionId', 0));

        if (null !== $selection) {
            $selection->setLocale($this->getCurrentEditionLocale());
        }

        return $selection;
    }

    protected function getObjectLabel($object)
    {
        return '';
    }

    protected function getObjectId($object)
    {
        return $object->getId();
    }

    protected function renderListTemplate($currentOrder)
    {
        $this->getParser()
            ->assign("order", $currentOrder);

        return $this->render('selectionlist');
    }

    protected function renderEditionTemplate()
    {
        $this->getParserContext()
            ->set(
                'selection_id',
                $this->getRequest()->get('selectionId')
            );

        return $this->render('selection-edit');
    }

    protected function redirectToEditionTemplate()
    {
        $id = $this->getRequest()->get('selection_id');

        return new RedirectResponse(
            URL::getInstance()->absoluteUrl(
                "/admin/selection/update/".$id
            )
        );
    }

    protected function redirectToListTemplate()
    {
        return new RedirectResponse(
            URL::getInstance()->absoluteUrl("/admin/Selection")
        );
    }

    /**
     * Online status toggle product
     */
    public function setToggleVisibilityAction()
    {
        // Check current user authorization
        if (null !== $response = $this->checkAuth($this->resourceCode, array(), AccessManager::UPDATE)) {
            return $response;
        }

        $event = new SelectionEvent($this->getExistingObject());

        try {
            $this->dispatch(SelectionEvents::SELECTION_TOGGLE_VISIBILITY, $event);
        } catch (\Exception $ex) {
            // Any error
            return $this->errorPage($ex);
        }

        // Ajax response -> no action
        return $this->nullResponse();
    }
}
