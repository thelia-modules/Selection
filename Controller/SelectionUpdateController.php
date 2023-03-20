<?php

namespace Selection\Controller;

use Propel\Runtime\ActiveQuery\Criteria;
use Selection\Event\SelectionContainerEvent;
use Selection\Event\SelectionEvent;
use Selection\Event\SelectionEvents;
use Selection\Form\SelectionCreateForm;
use Selection\Form\SelectionUpdateForm;
use Selection\Model\Selection as SelectionModel;
use Selection\Model\SelectionContainerAssociatedSelection;
use Selection\Model\SelectionContentQuery;
use Selection\Model\SelectionProductQuery;
use Selection\Model\SelectionQuery;
use Selection\Selection;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Thelia\Controller\Admin\AbstractSeoCrudController;
use Thelia\Core\Event\UpdatePositionEvent;
use Thelia\Core\HttpFoundation\Request;
use Thelia\Core\Security\AccessManager;
use Thelia\Core\Security\Resource\AdminResources;
use Thelia\Core\Template\ParserContext;
use Thelia\Form\Exception\FormValidationException;
use Thelia\Log\Tlog;
use Thelia\Tools\URL;

class SelectionUpdateController extends AbstractSeoCrudController
{
    protected $currentRouter = Selection::ROUTER;

    /**
     * Save content of the selection
     *
     * @return \Symfony\Component\HttpFoundation\Response|\Thelia\Core\HttpFoundation\Response
     * @throws \Propel\Runtime\Exception\PropelException
     */
    public function saveSelection()
    {
        $form = $this->createForm(SelectionUpdateForm::class);

        $validForm = $this->validateForm($form);
        $data = $validForm->getData();

        $selectionID = $data['selection_id'];
        $selectionCode = $data['selection_code'];
        $selectionTitle = $data['selection_title'];
        $selectionChapo = $data['selection_chapo'];
        $selectionDescription = $data['selection_description'];
        $selectionPostscriptum = $data['selection_postscriptum'];

        $aSelection = SelectionQuery::create()->findPk($selectionID);

        $aSelection
            ->setCode($selectionCode)
            ->setLocale($this->getCurrentEditionLocale())
            ->setTitle($selectionTitle)
            ->setChapo($selectionChapo)
            ->setDescription($selectionDescription)
            ->setPostscriptum($selectionPostscriptum)
            ->save();

        if ($validForm->get('save_and_close')->isClicked()) {
            return $this->render("electionlist");
        }

        return $this->generateRedirect('/admin/selection/update/'.$selectionID);
    }

    public function createSelection()
    {
        $form = $this->createForm(SelectionCreateForm::class);
        try {
            $validForm = $this->validateForm($form);
            $data = $validForm->getData();
            $code = $data['code'];
            $title = $data['title'];
            $chapo = $data['chapo'];
            $description = $data['description'];
            $postscriptum = $data['postscriptum'];
            $containerId = (int)$data['container_id'];
            $date = new \DateTime();
            $selection = new SelectionModel();

            $lastSelectionQuery = SelectionQuery::create()->orderByPosition(Criteria::DESC);

            if ($containerId > 0) {
                $lastSelectionQuery
                    ->useSelectionContainerAssociatedSelectionQuery('toto', Criteria::LEFT_JOIN)
                    ->filterBySelectionContainerId($containerId)
                    ->endUse();
            }

            $position = 1;

            if (null !== $lastSelection = $lastSelectionQuery->findOne()) {
                $position = $lastSelection->getPosition() + 1;
            }

            $selection
                ->setCreatedAt($date->format('Y-m-d H:i:s'))
                ->setUpdatedAt($date->format('Y-m-d H:i:s'))
                ->setVisible(1)
                ->setCode($code)
                ->setPosition($position)
                ->setLocale($this->getCurrentEditionLocale())
                ->setTitle($title)
                ->setChapo($chapo)
                ->setDescription($description)
                ->setPostscriptum($postscriptum)
                ->save();

            if ($containerId > 0) {
                // Required, see Selection::preInsert();
                $selection->setPosition($position)->save();

                (new SelectionContainerAssociatedSelection())
                    ->setSelectionContainerId($containerId)
                    ->setSelectionId($selection->getId())
                    ->save();

                return $this->generateRedirect(URL::getInstance()->absoluteUrl("/admin/selection/container/view/" . $containerId));
            }

            return $this->generateRedirect(URL::getInstance()->absoluteUrl("/admin/selection"));
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


    public function updateSelectionPositionAction(Request $request, EventDispatcherInterface $eventDispatcher)
    {
        if (null !== $response = $this->checkAuth(array(AdminResources::MODULE), array(Selection::DOMAIN_NAME), AccessManager::UPDATE)) {
            return $response;
        }
        try {
            $mode = $request->get('mode', null);

            if ($mode === 'up') {
                $mode = UpdatePositionEvent::POSITION_UP;
            } elseif ($mode === 'down') {
                $mode = UpdatePositionEvent::POSITION_DOWN;
            } else {
                $mode = UpdatePositionEvent::POSITION_ABSOLUTE;
            }

            $position = $this->getRequest()->get('position', null);

            $event = $this->createUpdateSelectionPositionEvent($request, $mode, $position);

            $eventDispatcher->dispatch($event, SelectionEvents::SELECTION_UPDATE_POSITION);
        } catch (\Exception $ex) {
            Tlog::getInstance()->error($ex->getMessage());
        }

        return $this->forward('Selection\Controller\SelectionController::viewAction');
    }

    public function deleteRelatedProduct(Request $request)
    {
        $selectionID = $request->get('selectionID');
        $productID = $request->get('productID');

        try {
            $selection = SelectionProductQuery::create()
                ->filterByProductId($productID)
                ->findOneBySelectionId($selectionID);
            if (null !== $selection) {
                $selection->delete();
            }
        } catch (\Exception $e) {
            Tlog::getInstance()->error($e->getMessage());
        }

        return $this->generateRedirect('/admin/selection/update/'.$selectionID);
    }

    public function deleteRelatedContent(Request $request)
    {
        $selectionID = $request->get('selectionID');
        $contentID = $request->get('contentID');

        try {
            $selection = SelectionContentQuery::create()
                ->filterByContentId($contentID)
                ->findOneBySelectionId($selectionID);
            if (null !== $selection) {
                $selection->delete();
            }
        } catch (\Exception $e) {
            Tlog::getInstance()->error($e->getMessage());
        }

        return $this->generateRedirect('/admin/selection/update/'.$selectionID);
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
            SelectionEvents::RELATED_PRODUCT_UPDATE_POSITION,
            SelectionEvents::SELECTION_UPDATE_SEO,
            Selection::DOMAIN_NAME
        );
    }

    protected function getCreationForm()
    {
        return $this->createForm(SelectionUpdateForm::getName());
    }

    protected function getUpdateForm($data = array())
    {
        if (!is_array($data)) {
            $data = array();
        }

        return $this->createForm(SelectionUpdateForm::getName(), FormType::class, $data);
    }

    /**
     * $object Selection
     * @param \Selection\Model\Selection $selection
     * @return \Thelia\Form\BaseForm
     * @throws \Propel\Runtime\Exception\PropelException
     */
    protected function hydrateObjectForm(ParserContext $parserContext, $selection)
    {
        $this->hydrateSeoForm($parserContext, $selection);
        $associatedContainer = $selection->getSelectionContainerAssociatedSelections();
        $container = null;
        if (!empty($associatedContainer) && count($associatedContainer) > 0) {
            /** @var SelectionContainerAssociatedSelection[] $associatedContainer */
            $container = $associatedContainer[0]->getSelectionContainerId();
        }
        $data = array(
            'selection_id' => $selection->getId(),
            'selection_container' => $container,
            'id' => $selection->getId(),
            'locale' => $selection->getLocale(),
            'selection_code' => $selection->getCode(),
            'selection_title' => $selection->getTitle(),
            'selection_chapo' => $selection->getChapo(),
            'selection_description' => $selection->getDescription(),
            'selection_postscriptum' => $selection->getPostscriptum(),
            'current_id' => $selection->getId(),
        );

        return $this->getUpdateForm($data);
    }

    protected function getCreationEvent($formData)
    {
        $event = new SelectionEvent();

        $event->setCode($formData['code']);
        $event->setTitle($formData['title']);
        $event->setChapo($formData['chapo']);
        $event->setDescription($formData['description']);
        $event->setPostscriptum($formData['postscriptum']);
        $event->setContainerId($formData['container_id']);

        return $event;
    }

    protected function getUpdateEvent($formData)
    {
        $selection = SelectionQuery::create()->findPk($formData['selection_id']);
        $event = new SelectionEvent($selection);

        $event->setId($formData['selection_id']);
        $event->setContainerId($formData['selection_container_id']);
        $event->setCode($formData['selection_code']);
        $event->setTitle($formData['selection_title']);
        $event->setChapo($formData['selection_chapo']);
        $event->setDescription($formData['selection_description']);
        $event->setPostscriptum($formData['selection_postscriptum']);
        $event->setLocale($this->getCurrentEditionLocale());
        return $event;
    }

    protected function getDeleteEvent()
    {
        $event = new SelectionEvent();
        $selectionId = $this->getRequest()->request->get('selection_id');
        $event->setId($selectionId);
        return $event;
    }

    protected function getDeleteGroupEvent(Request $request)
    {
        $event = new SelectionContainerEvent();
        $selectionGroupId = $request->request->get('selection_group_id');
        $event->setId($selectionGroupId);
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

    /**
     * Returns the object ID from the object
     * @param \Selection\Model\Selection $object
     * @return int selection id
     */
    protected function getObjectId($object)
    {
        return $object->getId();
    }

    protected function renderListTemplate($currentOrder)
    {
        $this->getParser()->assign("order", $currentOrder);
        return $this->render('selection-list');
    }

    protected function renderEditionTemplate()
    {
        $selectionId = $this->getRequest()->get('selectionId');
        $currentTab = $this->getRequest()->get('current_tab');
        return $this->render(
            'selection-edit',
            [
                'selection_id' => $selectionId,
                'current_tab' => $currentTab
            ]
        );
    }

    protected function redirectToEditionTemplate()
    {
        if (!$id = $this->getRequest()->get('selection_id')) {
            $id = $this->getRequest()->get('admin_selection_update')['selection_id'];
        }

        return new RedirectResponse(
            URL::getInstance()->absoluteUrl(
                "/admin/selection/update/" . $id
            )
        );
    }

    protected function redirectToListTemplate()
    {
        return new RedirectResponse(
            URL::getInstance()->absoluteUrl("/admin/selection")
        );
    }

    /**
     * Online status toggle product
     */
    public function setToggleVisibilityAction(EventDispatcherInterface $eventDispatcher)
    {
        // Check current user authorization
        if (null !== $response = $this->checkAuth($this->resourceCode, array(), AccessManager::UPDATE)) {
            return $response;
        }

        $event = new SelectionEvent($this->getExistingObject());

        try {
            $eventDispatcher->dispatch($event, SelectionEvents::SELECTION_TOGGLE_VISIBILITY);
        } catch (\Exception $ex) {
            // Any error
            return $this->errorPage($ex);
        }

        // Ajax response -> no action
        return $this->nullResponse();
    }

    protected function createUpdatePositionEvent($positionChangeMode, $positionValue)
    {
        return new UpdatePositionEvent(
            $this->getRequest()->get('product_id', null),
            $positionChangeMode,
            $positionValue,
            $this->getRequest()->get('selection_id', null)
        );
    }

    protected function createUpdateSelectionPositionEvent(Request $request, $positionChangeMode, $positionValue)
    {
        return new UpdatePositionEvent(
            $request->get('selection_id', null),
            $positionChangeMode,
            $positionValue,
            Selection::getModuleId()
        );
    }

    protected function performAdditionalUpdatePositionAction($positionEvent)
    {
        $selectionID = $this->getRequest()->get('selection_id');

        return $this->generateRedirect(URL::getInstance()->absoluteUrl('/admin/selection/update/'.$selectionID));
    }

    protected function performAdditionalDeleteAction($deleteEvent)
    {
        $containerId = (int)$this->getRequest()->get('container_id');

        if ($containerId > 0) {
            return $this->generateRedirect(URL::getInstance()->absoluteUrl("/admin/selection/container/view/" . $containerId));
        }

        return null;
    }

    public function processUpdateSeoAction(
        Request                  $request,
        ParserContext            $parserContext,
        EventDispatcherInterface $eventDispatcher
    )
    {
        $selectionId = $request->get('current_id');
        $request->request->set("selectionId", $selectionId);
        return parent::processUpdateSeoAction($request, $parserContext, $eventDispatcher);
    }
}
