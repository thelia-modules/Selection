<?php
/**
 * Created by PhpStorm.
 * User: tpradatos
 * Date: 05/04/2018
 * Time: 11:37
 */

namespace Selection\Controller;

use Propel\Runtime\ActiveQuery\Criteria;
use Selection\Event\SelectionFolderEvent;
use Selection\Event\SelectionFolderEvents;
use Selection\Model\SelectionFolder;
use Selection\Model\SelectionFolderQuery;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Thelia\Controller\Admin\AbstractSeoCrudController;
use Thelia\Core\Security\Resource\AdminResources;
use Thelia\Tools\URL;

class SelectionFolderUpdateController extends AbstractSeoCrudController
{
    protected $currentRouter = "router.Selection";

    public function __construct()
    {
        parent::__construct(
            'selectionFolder',
            'folder_id',
            'order',
            AdminResources::MODULE,
            SelectionFolderEvents::SELECTION_FOLDER_CREATE,
            SelectionFolderEvents::SELECTION_FOLDER_UPDATE,
            SelectionFolderEvents::SELECTION_FOLDER_DELETE,
            null,
            null,
            SelectionFolderEvents::SELECTION_FOLDER_UPDATE_SEO,
            'Selection'
        );
    }

    protected function getCreationForm()
    {
        return $this->createForm('admin.selection.folder.update');
    }

    protected function getUpdateForm($data = array())
    {
        if (!is_array($data)) {
            $data = array();
        }

        return $this->createForm('admin.selection.folder.update', 'form', $data);
    }

    protected function hydrateObjectForm($object)
    {
        $this->hydrateSeoForm($object);

        $data = array(
            'folder_id'          => $object->getId(),
            'id'                 => $object->getId(),
            'locale'             => $object->getLocale(),
            'folder_title'       => $object->getTitle(),
            'folder_chapo'       => $object->getChapo(),
            'folder_description' => $object->getDescription(),
            'folder_postscriptum'=> $object->getPostscriptum(),
            'current_id'         => $object->getId(),
        );

        return $this->getUpdateForm($data);
    }

    protected function getCreationEvent($formData)
    {
        $event = new SelectionFolderEvent();

        $event->setId($formData['folder_id']);
        $event->setTitle($formData['folder_title']);
        $event->setChapo($formData['folder_chapo']);
        $event->setDescription($formData['folder_description']);
        $event->setPostscriptum($formData['folder_postscriptum']);

        return $event;
    }

    protected function getUpdateEvent($formData)
    {
        $folder = SelectionFolderQuery::create()->findPk($formData['folder_id']);
        $event = new SelectionFolderEvent($folder);

        $event->setId($formData['folder_id']);
        $event->setTitle($formData['folder_title']);
        $event->setChapo($formData['folder_chapo']);
        $event->setDescription($formData['folder_description']);
        $event->setPostscriptum($formData['folder_postscriptum']);
        $event->setLocale($this->getRequest()->getSession()->get('thelia.current.lang')->getLocale());

        return $event;
    }
    protected function getDeleteEvent()
    {
    }

    protected function eventContainsObject($event)
    {
    }

    protected function getObjectFromEvent($event)
    {
        return $event->getSelectionFolder();
    }

    protected function getExistingObject()
    {
        $folder = SelectionFolderQuery::create()
            ->findPk($this->getRequest()->get('folderId', 0));

        if (null !== $folder) {
            $folder->setLocale($this->getCurrentEditionLocale());
        }
        return $folder;
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
                'folder_id',
                $this->getRequest()->get('folderId')
            );

        return $this->render('selection-folder-edit');
    }

    protected function redirectToEditionTemplate()
    {
        $id = $this->getRequest()->get('current_id');

        return new RedirectResponse(
            URL::getInstance()->absoluteUrl(
                "/admin/selection-folder/update/".$id
            )
        );
    }

    protected function redirectToListTemplate()
    {
        return new RedirectResponse(
            URL::getInstance()->absoluteUrl("/admin/Selection")
        );
    }




    public function createSelectionFolder()
    {
        $form = $this->createForm('admin.selection.folder.create');

        $validForm          = $this->validateForm($form, 'POST');
        $data               = $validForm->getData();

        $folderTitle     = $data['selection_folder_title'];

        $lang               = $this->getRequest()->getSession()->get('thelia.current.lang');

        $parent = $this->getRequest()->get('parentId');

        if (null === $parent) {
            $parent = 0;
        }
        /*------------------------- Add in SelectionFolder table */
        $folder  = new SelectionFolder();
        $position = SelectionFolderQuery::create()
            ->filterByParent($parent)
            ->orderByPosition(Criteria::DESC)
            ->select('position')
            ->findOne();

        $date       = new \DateTime();

        if ($position != 0) {
            $position =  $position + 1;
        } else {
            $position = 1;
        }

        try {
            $folder
                ->setCreatedAt($date->format('Y-m-d H:i:s'))
                ->setUpdatedAt($date->format('Y-m-d H:i:s'))
                ->setVisible(1)
                ->setPosition($position)
                ->setLocale($lang->getLocale())
                ->setTitle($folderTitle)
                ->setParent($parent);

            $folder->save();

            $m = [
                'message' => 'Selection : '.$folderTitle.' has been created and it have #'
                    .$folder->getId().' as reference'
            ];
        } catch (\Exception $e) {
            $m = ['message' => $e->getMessage()];
        }

        return $this->render('selectionlist', $m);
    }
}