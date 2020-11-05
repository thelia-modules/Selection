<?php
/**
 * Created by PhpStorm.
 * User: audreymartel
 * Date: 10/07/2018
 * Time: 09:38
 */

namespace Selection\Controller;


use Propel\Runtime\ActiveQuery\Criteria;
use Selection\Event\SelectionContainerEvent;
use Selection\Event\SelectionEvents;
use Selection\Form\SelectionCreateForm;
use Selection\Model\SelectionContainer;
use Selection\Model\SelectionContainerQuery;
use Selection\Selection;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Thelia\Controller\Admin\AbstractSeoCrudController;
use Thelia\Core\Event\UpdatePositionEvent;
use Thelia\Core\Security\AccessManager;
use Thelia\Core\Security\Resource\AdminResources;
use Thelia\Form\BaseForm;
use Thelia\Form\Exception\FormValidationException;
use Thelia\Tools\URL;

class SelectionContainerUpdateController extends AbstractSeoCrudController
{
    public function __construct()
    {
        parent::__construct(
            'selection_container',
            'selection_container_id',
            'order',
            AdminResources::MODULE,
            SelectionEvents::SELECTION_CONTAINER_CREATE,
            SelectionEvents::SELECTION_CONTAINER_UPDATE,
            SelectionEvents::SELECTION_CONTAINER_DELETE,
            null,
            SelectionEvents::SELECTION_CONTAINER_UPDATE_POSITION,
            SelectionEvents::SELECTION_CONTAINER_UPDATE_SEO,
            'Selection'
        );
    }

    /**
     * Return the creation form for this object
     * @return BaseForm
     */
    protected function getCreationForm()
    {
        return $this->createForm('admin.selection.container.create');
    }

    /**
     * Return the update form for this object
     * @param array $data
     * @return BaseForm
     */
    protected function getUpdateForm($data = [])
    {
        if (!is_array($data)) {
            $data = array();
        }

        return $this->createForm('admin.selection.container.update', 'form', $data);
    }

    /**
     * Hydrate the update form for this object, before passing it to the update template
     * @param SelectionContainer $object
     * @return BaseForm
     */
    protected function hydrateObjectForm($object)
    {
        $this->hydrateSeoForm($object);
        $data = array(
            'selection_container_id'=> $object->getId(),
            'id'                    => $object->getId(),
            'locale'                => $object->getLocale(),
            'selection_container_chapo'                 => $object->getChapo(),
            'selection_container_title'                 => $object->getTitle(),
            'selection_container_description'           => $object->getDescription(),
            'selection_container_postscriptum'          => $object->getPostscriptum(),
            'current_id'            => $object->getId(),
        );

        return $this->getUpdateForm($data);
    }

    /**
     * Creates the creation event with the provided form data
     * @param mixed $formData
     * @return \Thelia\Core\Event\ActionEvent
     */
    protected function getCreationEvent($formData)
    {
        $event = new SelectionContainerEvent();

        $event->setTitle($formData['title']);
        $event->setChapo($formData['chapo']);
        $event->setDescription($formData['description']);
        $event->setPostscriptum($formData['postscriptum']);
        $event->setLocale($this->getCurrentEditionLocale());

        return $event;
    }

    /**
     * Creates the update event with the provided form data
     * @param mixed $formData
     * @return \Thelia\Core\Event\ActionEvent
     */
    protected function getUpdateEvent($formData)
    {
        $selectionContainer = SelectionContainerQuery::create()->findPk($formData['selection_container_id']);
        $event = new SelectionContainerEvent($selectionContainer);

        $event->setId($formData['selection_container_id']);
        $event->setTitle($formData['selection_container_title']);
        $event->setChapo($formData['selection_container_chapo']);
        $event->setDescription($formData['selection_container_description']);
        $event->setPostscriptum($formData['selection_container_postscriptum']);
        $event->setLocale($this->getCurrentEditionLocale());
        return $event;
    }

    /**
     * Creates the delete event with the provided form data
     * @return \Thelia\Core\Event\ActionEvent
     */
    protected function getDeleteEvent()
    {
        $event = new SelectionContainerEvent();
        $selectionId = $this->getRequest()->request->get('selection_container_id');
        $event->setId($selectionId);
        return $event;
    }

    /**
     * Return true if the event contains the object, e.g. the action has updated the object in the event.
     * @param SelectionContainerEvent $event
     * @return bool
     */
    protected function eventContainsObject($event)
    {
        return $event->hasSelection();
    }

    /**
     * Get the created object from an event.
     * @param SelectionContainerEvent $event
     * @return SelectionContainer
     */
    protected function getObjectFromEvent($event)
    {
        return $event->getSelectionContainer();
    }

    /**
     * Load an existing object from the database
     */
    protected function getExistingObject()
    {
        $selectionContainer = SelectionContainerQuery::create()
            ->findPk($this->getRequest()->get('selection_container_id', 0));
        if (null !== $selectionContainer) {
            $selectionContainer->setLocale($this->getCurrentEditionLocale());
        }

        return $selectionContainer;
    }

    /**
     * Returns the object label form the object event (name, title, etc.)
     * @param SelectionContainer|null $object
     * @return string
     */
    protected function getObjectLabel($object)
    {
        return empty($object) ? '' : $object->getTitle();
    }

    /**
     * Returns the object ID from the object
     * @param SelectionContainer|null $object
     * @return int
     */
    protected function getObjectId($object)
    {
        return $object->getId();
    }

    /**
     * Render the main list template
     * @param mixed $currentOrder , if any, null otherwise.
     * @return \Thelia\Core\HttpFoundation\Response
     */
    protected function renderListTemplate($currentOrder)
    {
        return $this->render(
            'selection-list',
            ['order' => $currentOrder]
        );
    }

    /**
     * Render the edition template
     * @return \Thelia\Core\HttpFoundation\Response
     */
    protected function renderEditionTemplate()
    {
        $selectionContainerId = $this->getRequest()->get('selection_container_id');
        $currentTab = $this->getRequest()->get('current_tab');
        return $this->render("container-edit",
            [
                'selection_container_id' => $selectionContainerId,
                'current_tab' => $currentTab
            ]);
    }

    /**
     * Must return a RedirectResponse instance
     * @return RedirectResponse
     */
    protected function redirectToEditionTemplate()
    {
        $id = $this->getRequest()->get('selection_container_id');

        return new RedirectResponse(
            URL::getInstance()->absoluteUrl(
                "/admin/selection/container/update/".$id
            )
        );
    }

    /**
     * Must return a RedirectResponse instance
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    protected function redirectToListTemplate()
    {
        return new RedirectResponse(
            URL::getInstance()->absoluteUrl("/admin/selection")
        );
    }

    /**
     * Online status toggle
     */
    public function setToggleVisibilityAction()
    {
        // Check current user authorization
        if (null !== $response = $this->checkAuth($this->resourceCode, array(), AccessManager::UPDATE)) {
            return $response;
        }

        $event = new SelectionContainerEvent($this->getExistingObject());

        try {
            $this->dispatch(SelectionEvents::SELECTION_CONTAINER_TOGGLE_VISIBILITY, $event);
        } catch (\Exception $ex) {
            // Any error
            return $this->errorPage($ex);
        }

        // Ajax response -> no action
        return $this->nullResponse();
    }

    public function createSelectionContainerAction()
    {
        $form       = new SelectionCreateForm($this->getRequest());
        try {
            $validForm  = $this->validateForm($form);
            $data       = $validForm->getData();
            $title         = $data['title'];
            $chapo         = $data['chapo'];
            $description   = $data['description'];
            $postscriptum  = $data['postscriptum'];
            $date = new \DateTime();

            $selectionContainer  = new SelectionContainer();
            $lastSelection   = SelectionContainerQuery::create()->orderByPosition(Criteria::DESC)->findOne();
            if (null !== $lastSelection) {
                $position =  $lastSelection->getPosition() + 1;
            } else {
                $position = 1;
            }
            $selectionContainer
                ->setCreatedAt($date->format('Y-m-d H:i:s'))
                ->setUpdatedAt($date->format('Y-m-d H:i:s'))
                ->setVisible(1)
                ->setPosition($position)
                ->setLocale($this->getCurrentEditionLocale())
                ->setTitle($title)
                ->setChapo($chapo)
                ->setDescription($description)
                ->setPostscriptum($postscriptum);
            $selectionContainer->save();

            return $this->generateRedirect("/admin/selection");
        } catch (FormValidationException $ex) {
            // Form cannot be validated
            $error_msg = $this->createStandardFormValidationErrorMessage($ex);
        } catch (\Exception $ex) {
            // Any other error
            $error_msg = $ex->getMessage();
        }

        if (false !== $error_msg) {
            $this->setupFormErrorContext(
                $this->getTranslator()->trans("%obj creation", ['%obj' => $this->objectName]),
                $error_msg,
                $form,
                $ex
            );
            // At this point, the form has error, and should be redisplayed.
            return $this->renderList();
        }
    }

    /**
     * Show the default template : selectionList
     * display selections inide the container
     * @param $selectionContainerId
     * @return \Thelia\Core\HttpFoundation\Response
     */
    public function viewAction($selectionContainerId)
    {
        $this->getRequest()->request->set("selectionContainerId", $selectionContainerId);
        $selectionContainer = $this->getExistingObject();
        if (!is_null($selectionContainer)) {
            $changeForm = $this->hydrateObjectForm($selectionContainer);
            $this->getParserContext()->addForm($changeForm);
        }
        return $this->render("container-view",
            array(
                'selected_container_id' => $selectionContainerId
            ));
    }

    /**
     * @param $selectionContainerId
     * @return \Thelia\Core\HttpFoundation\Response
     */
    public function updateContainerAction($selectionContainerId)
   {
       $this->getRequest()->request->set("selection_container_id", $selectionContainerId);
       return parent::updateAction();
   }

    public function processUpdateSeoAction()
    {
        $selectionContainerId = $this->getRequest()->get('current_id');
        $this->getRequest()->request->set("selection_container_id", $selectionContainerId);
        return parent::processUpdateSeoAction();
    }
}
