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
use Symfony\Component\HttpFoundation\Response;
use Thelia\Controller\Admin\AbstractSeoCrudController;
use Thelia\Core\Event\UpdatePositionEvent;
use Thelia\Core\HttpFoundation\Request;
use Thelia\Core\Security\AccessManager;
use Thelia\Core\Security\Resource\AdminResources;
use Thelia\Core\Template\ParserContext;
use Thelia\Form\BaseForm;
use Thelia\Form\Exception\FormValidationException;
use Thelia\Log\Tlog;
use Thelia\Tools\URL;
use Twig\Environment;

class SelectionUpdateController extends AbstractSeoCrudController
{
    protected string $currentRouter = Selection::ROUTER;

    private Environment $twig;

    /**
     * Render a module back-office template through Twig, using the module namespace.
     */
    private function renderTwig(string $template, array $context = []): Response
    {
        return new Response($this->twig->render(
            '@SelectionModule/backOffice/default-twig/' . $template,
            $context
        ));
    }

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
            return $this->generateRedirect('/admin/selection');
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
    public function __construct(Environment $twig)
    {
        $this->twig = $twig;
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

    protected function getCreationForm(): ?BaseForm
    {
        return $this->createForm(SelectionUpdateForm::getName());
    }

    protected function getUpdateForm($data = array()): ?BaseForm
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
    protected function hydrateObjectForm(ParserContext $parserContext, $selection): BaseForm
    {
        $this->hydrateSeoForm($parserContext, $selection);
        $containers = $selection->getSelectionContainerAssociatedSelections();

        $containerId = $containers?->getFirst()?->getSelectionContainerId();

        $data = array(
            'selection_id' => $selection->getId(),
            'selection_container' => $containerId,
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

    protected function getCreationEvent($formData): \Thelia\Core\Event\ActionEvent|\Propel\Runtime\Event\ActiveRecordEvent|null
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

    protected function getUpdateEvent($formData): \Thelia\Core\Event\ActionEvent|\Propel\Runtime\Event\ActiveRecordEvent|null
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

    protected function getDeleteEvent(): \Propel\Runtime\Event\ActiveRecordEvent|\Thelia\Core\Event\ActionEvent|null
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

    protected function eventContainsObject($event): bool
    {
        return $event->hasSelection();
    }

    protected function getObjectFromEvent($event): mixed
    {
        return $event->getSelection();
    }

    protected function getExistingObject(): ?\Propel\Runtime\ActiveRecord\ActiveRecordInterface
    {
        $selection = SelectionQuery::create()
            ->findPk($this->getRequest()->request->get('selectionId', $this->getRequest()->query->get('selectionId', 0)));

        if (null !== $selection) {
            $selection->setLocale($this->getCurrentEditionLocale());
        }

        return $selection;
    }

    protected function getObjectLabel($object): ?string
    {
        return '';
    }

    /**
     * Returns the object ID from the object
     * @param \Selection\Model\Selection $object
     * @return int selection id
     */
    protected function getObjectId($object): int
    {
        return $object->getId();
    }

    protected function renderListTemplate($currentOrder): Response
    {
        $locale = $this->getCurrentEditionLocale();
        $listController = new SelectionController($this->twig);

        return $this->renderTwig('selection-list.html.twig', [
            'selection_order' => $currentOrder,
            'selection_container_order' => $currentOrder,
            'containers' => $listController->getContainerRows($locale),
            'selections' => $listController->getSelectionRows($locale, null),
            'selected_container_id' => null,
        ]);
    }

    protected function renderEditionTemplate(): Response
    {
        $request = $this->getRequest();
        $selectionId = $request->query->get('selectionId', $request->request->get('selectionId'));
        $currentTab = $request->query->get('current_tab', $request->request->get('current_tab'));

        $selection = SelectionQuery::create()->findPk($selectionId);
        $form = $this->hydrateObjectForm($this->getParserContext(), $selection);
        $locale = $this->getCurrentEditionLocale();

        return $this->renderTwig('selection-edit.html.twig', [
            'selection_id' => $selectionId,
            'current_tab' => $currentTab,
            'selection' => $selection,
            'form' => $form->createView()->getView(),
            'categories' => $this->buildCategoryTree($locale),
            'folders' => $this->buildFolderTree($locale),
        ]);
    }

    /**
     * Flat category tree (id, title, level), reproducing the category-tree loop.
     */
    private function buildCategoryTree(string $locale, int $parent = 0, int $level = 0): array
    {
        $result = [];
        $categories = \Thelia\Model\CategoryQuery::create()
            ->filterByParent($parent)
            ->orderByPosition(\Propel\Runtime\ActiveQuery\Criteria::ASC)
            ->find();

        foreach ($categories as $category) {
            $category->setLocale($locale);
            $result[] = [
                'id' => $category->getId(),
                'title' => $category->getTitle(),
                'level' => $level,
            ];
            $result = array_merge($result, $this->buildCategoryTree($locale, $category->getId(), $level + 1));
        }

        return $result;
    }

    /**
     * Flat folder tree (id, title, level), reproducing the folder-tree loop.
     */
    private function buildFolderTree(string $locale, int $parent = 0, int $level = 0): array
    {
        $result = [];
        $folders = \Thelia\Model\FolderQuery::create()
            ->filterByParent($parent)
            ->orderByPosition(\Propel\Runtime\ActiveQuery\Criteria::ASC)
            ->find();

        foreach ($folders as $folder) {
            $folder->setLocale($locale);
            $result[] = [
                'id' => $folder->getId(),
                'title' => $folder->getTitle(),
                'level' => $level,
            ];
            $result = array_merge($result, $this->buildFolderTree($locale, $folder->getId(), $level + 1));
        }

        return $result;
    }

    protected function redirectToEditionTemplate(): Response|RedirectResponse
    {
        if (!$id = $this->getRequest()->request->get('selection_id', $this->getRequest()->query->get('selection_id'))) {
            $id = $this->getRequest()->request->get('admin_selection_update')['selection_id'];
        }

        return new RedirectResponse(
            URL::getInstance()->absoluteUrl(
                "/admin/selection/update/" . $id
            )
        );
    }

    protected function redirectToListTemplate(): Response|RedirectResponse
    {
        return new RedirectResponse(
            URL::getInstance()->absoluteUrl("/admin/selection")
        );
    }

    /**
     * Online status toggle product
     */
    public function setToggleVisibilityAction(EventDispatcherInterface $eventDispatcher): ?Response
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

    protected function createUpdatePositionEvent($positionChangeMode, $positionValue): \Thelia\Core\Event\ActionEvent
    {
        return new UpdatePositionEvent(
            $this->getRequest()->query->get('product_id', null),
            $positionChangeMode,
            $positionValue,
            $this->getRequest()->query->get('selection_id', null)
        );
    }

    protected function createUpdateSelectionPositionEvent(Request $request, $positionChangeMode, $positionValue): \Thelia\Core\Event\ActionEvent
    {
        return new UpdatePositionEvent(
            $request->get('selection_id', null),
            $positionChangeMode,
            $positionValue,
            Selection::getModuleId()
        );
    }

    protected function performAdditionalUpdatePositionAction($positionEvent): ?Response
    {
        $selectionID = $this->getRequest()->request->get('selection_id', $this->getRequest()->query->get('selection_id'));

        return $this->generateRedirect(URL::getInstance()->absoluteUrl('/admin/selection/update/'.$selectionID));
    }

    protected function performAdditionalDeleteAction($deleteEvent): ?Response
    {
        $containerId = (int)$this->getRequest()->request->get('container_id', $this->getRequest()->query->get('container_id'));

        if ($containerId > 0) {
            return $this->generateRedirect(URL::getInstance()->absoluteUrl("/admin/selection/container/view/" . $containerId));
        }

        return null;
    }

    public function processUpdateSeoAction(
        Request                  $request,
        ParserContext            $parserContext,
        EventDispatcherInterface $eventDispatcher
    ): Response
    {
        $selectionId = $request->query->get('current_id');
        $request->request->set("selectionId", $selectionId);
        return parent::processUpdateSeoAction($request, $parserContext, $eventDispatcher);
    }
}
