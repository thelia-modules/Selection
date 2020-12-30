<?php

namespace Selection\Controller;

use Selection\Model\SelectionContainer;
use Selection\Model\SelectionContainerImage;
use Selection\Model\SelectionContainerImageQuery;
use Selection\Model\SelectionImage;
use Selection\Model\SelectionImageQuery;
use Selection\Selection;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Thelia\Controller\Admin\FileController;
use Thelia\Core\Event\File\FileCreateOrUpdateEvent;
use Thelia\Core\Event\File\FileDeleteEvent;
use Thelia\Core\Event\File\FileToggleVisibilityEvent;
use Thelia\Core\Event\TheliaEvents;
use Thelia\Core\Event\UpdateFilePositionEvent;
use Thelia\Core\HttpFoundation\Response;
use Thelia\Core\Security\AccessManager;
use Thelia\Files\Exception\ProcessFileException;
use Thelia\Files\FileModelInterface;
use Thelia\Form\Exception\FormValidationException;
use Thelia\Log\Tlog;
use Thelia\Tools\Rest\ResponseRest;
use Thelia\Tools\URL;

class ImageUploadController extends FileController
{
    protected $currentRouter = Selection::ROUTER;

    /**
     * @inheritdoc
     * @throws \Exception
     */
    public function getImageListAjaxAction($parentId, $parentType)
    {
        $this->addModuleResource($parentType);
        $this->registerFileModel($parentType);
        $this->checkAccessForType(AccessManager::UPDATE, $parentType);
        $this->checkXmlHttpRequest();
        $args = array('imageType' => $parentType, 'parentId' => $parentId);
        return $this->render('image-upload-list-ajax', $args);
    }

    /**
     * @inheritdoc
     * @throws \Exception
     */
    public function getImageFormAjaxAction($parentId, $parentType)
    {
        $this->addModuleResource($parentType);
        $this->registerFileModel($parentType);
        $this->checkAccessForType(AccessManager::UPDATE, $parentType);
        $this->checkXmlHttpRequest();
        $args = array('imageType' => $parentType, 'parentId' => $parentId);
        return $this->render('selectionImageUpdate', $args);
    }

    /**
     * @param $imageId
     * @param $parentType
     * @return mixed|\Symfony\Component\HttpFoundation\Response
     * @throws \Exception
     */
    public function updateImageTitleAction($imageId, $parentType)
    {
        $this->addModuleResource($parentType);
        $parentId = $this->getRequest()->get('parentId');
        $this->registerFileModel($parentType);
        if (null !== $response = $this->checkAccessForType(AccessManager::UPDATE, $parentType)) {
            return $response;
        }

        $fileManager = $this->getFileManager();

        $fileModelInstance = $fileManager->getModelInstance('image', $parentType);
        /** @var FileModelInterface $file */
        $file = $fileModelInstance->getQueryInstance()->findPk($imageId);

        $new_title = $this->getRequest()->request->get('title');
        $locale = $this->getRequest()->request->get('locale');

        if (!empty($new_title)) {
            $file->setLocale($locale);
            $file->setTitle($new_title);
            $file->save();
        }
        return $this->getImagetTypeUpdateRedirectionUrl($parentType, $parentId);
    }

    /**
     * @param int $fileId
     * @param string $parentType
     * @param string $objectType
     * @param string $eventName
     * @return \Symfony\Component\HttpFoundation\Response|Response
     * @throws \Exception
     */
    public function deleteFileAction($fileId, $parentType, $objectType, $eventName)
    {
        $message = null;
        $this->addModuleResource($parentType);
        $parentId = $this->getRequest()->get('parentId');
        $this->registerFileModel($parentType);
        $this->checkAccessForType(AccessManager::UPDATE, $parentType);
        $this->checkXmlHttpRequest();
        $fileManager = $this->getFileManager();
        $modelInstance = $fileManager->getModelInstance($objectType, $parentType);
        $model = $modelInstance->getQueryInstance()->findPk($fileId);
        if ($model == null) {
            return $this->pageNotFound();
        }
        // Feed event
        $fileDeleteEvent = new FileDeleteEvent($model);
        // Dispatch Event to the Action
        try {
            $this->dispatch($eventName, $fileDeleteEvent);
            $this->adminUpadteLogAppend(
                $parentType,
                $this->getTranslator()->trans(
                    'Deleting %obj% for %id% with parent id %parentId%',
                    array(
                        '%obj%' => $objectType,
                        '%id%' => $fileDeleteEvent->getFileToDelete()->getId(),
                        '%parentId%' => $fileDeleteEvent->getFileToDelete()->getParentId(),
                    )
                ),
                $fileDeleteEvent->getFileToDelete()->getId()
            );
        } catch (\Exception $e) {
            $message = $this->getTranslator()->trans(
                'Fail to delete  %obj% for %id% with parent id %parentId% (Exception : %e%)',
                array(
                    '%obj%' => $objectType,
                    '%id%' => $fileDeleteEvent->getFileToDelete()->getId(),
                    '%parentId%' => $fileDeleteEvent->getFileToDelete()->getParentId(),
                    '%e%' => $e->getMessage()
                )
            );
        }
        if (null === $message) {
            $message = $this->getTranslator()->trans(
                '%obj%s deleted successfully',
                ['%obj%' => ucfirst($objectType)],
                Selection::DOMAIN_NAME
            );
        }
        $this->adminUpadteLogAppend($parentType, $message, $fileDeleteEvent->getFileToDelete()->getId());
        return $this->getImagetTypeUpdateRedirectionUrl($parentType, $parentId);
    }

    /*----------------- My parts */

    /**
     * @param int $parentId
     * @param string $parentType
     * @param string $objectType
     * @param array $validMimeTypes
     * @param array $extBlackList
     * @return mixed|\Symfony\Component\HttpFoundation\Response|Response|ResponseRest
     * @throws \Exception
     */
    public function saveFileAjaxAction(
        $parentId,
        $parentType,
        $objectType,
        $validMimeTypes = array(),
        $extBlackList = array()
    ) {
        $this->addModuleResource($parentType);
        $this->registerFileModel($parentType);
        if (null !== $response = $this->checkAccessForType(AccessManager::UPDATE, $parentType)) {
            return $response;
        }
        $this->checkXmlHttpRequest();
        if ($this->getRequest()->isMethod('POST')) {
            /** @var UploadedFile $fileBeingUploaded */
            $fileBeingUploaded = $this->getRequest()->files->get('file');
            try {
                if (null !== $fileBeingUploaded) {
                    $this->processFile(
                        $fileBeingUploaded,
                        $parentId,
                        $parentType,
                        $objectType,
                        $validMimeTypes,
                        $extBlackList
                    );
                }
            } catch (ProcessFileException $e) {
                return new ResponseRest($e->getMessage(), 'text', $e->getCode());
            }
            return $this->getImagetTypeUpdateRedirectionUrl($parentType, $parentId);
        }

        return new Response('', 404);
    }

    /**
     * @param int $imageId
     * @param string $parentType
     * @return mixed|Response
     * @throws \Exception
     */
    public function viewImageAction($imageId, $parentType)
    {
        $this->addModuleResource($parentType);
        $this->registerFileModel($parentType);
        if (null !== $response = $this->checkAccessForType(AccessManager::UPDATE, $parentType)) {
            return $response;
        }
        $fileManager = $this->getFileManager();
        $imageModel = $fileManager->getModelInstance('image', $parentType);
        $image = null;
        $parentId = null;
        if (SelectionContainer::IMAGE_TYPE_LABEL === $parentType) {
            $image = SelectionContainerImageQuery::create()->findPk($imageId);
            if ($image !== null) {
                $parentId = $image->getSelectionContainerId();
            }
        } else {
            $image = SelectionImageQuery::create()->findPk($imageId);
            if ($image !== null) {
                $parentId = $image->getSelectionId();
            }
        }
        if ($image === null) {
            return $this->pageNotFound();
        }
        $redirectUrl = $this->getImagetTypeUpdateRedirectionUrl($parentType, $parentId);
        return $this->render('selection-image-edit', array(
            'imageId' => $imageId,
            'imageType' => $parentType,
            'redirectUrl' => $redirectUrl,
            'formId' => $imageModel->getUpdateFormId(),
            'breadcrumb' => $image->getBreadcrumb(
                $this->getRouter($this->getCurrentRouter()),
                $this->container,
                'images',
                $this->getCurrentEditionLocale()
            )
        ));
    }

    /**
     * @param int $fileId
     * @param string $parentType
     * @param string $objectType
     * @param string $eventName
     * @return \Symfony\Component\HttpFoundation\Response
     */
    protected function updateFileAction($fileId, $parentType, $objectType, $eventName)
    {
        $message = false;
        $fileManager = $this->getFileManager();
        $fileModelInstance = $fileManager->getModelInstance($objectType, $parentType);
        $fileUpdateForm = $this->createForm($fileModelInstance->getUpdateFormId());

        /** @var FileModelInterface $file */
        $file = $fileModelInstance->getQueryInstance()->findPk($fileId);

        try {
            $oldFile = clone $file;

            if (null === $file) {
                throw new \InvalidArgumentException(sprintf('%d %s id does not exist', $fileId, $objectType));
            }

            $data = $this->validateForm($fileUpdateForm)->getData();

            $event = new FileCreateOrUpdateEvent(null);

            if (array_key_exists('visible', $data)) {
                $file->setVisible($data['visible'] ? 1 : 0);
            }

            $file->setLocale($data['locale']);

            if (array_key_exists('title', $data)) {
                $file->setTitle($data['title']);
            }
            if (array_key_exists('chapo', $data)) {
                $file->setChapo($data['chapo']);
            }
            if (array_key_exists('description', $data)) {
                $file->setDescription($data['description']);
            }
            if (array_key_exists('postscriptum', $data)) {
                $file->setPostscriptum($data['postscriptum']);
            }

            if (isset($data['file'])) {
                $file->setFile($data['file']);
            }

            $event->setModel($file);
            $event->setOldModel($oldFile);

            $files = $this->getRequest()->files;

            $fileForm = $files->get($fileUpdateForm->getName());

            if (isset($fileForm['file'])) {
                $event->setUploadedFile($fileForm['file']);
            }

            $this->dispatch($eventName, $event);

            $fileUpdated = $event->getModel();

            $this->adminUpadteLogAppend(
                $parentType,
                sprintf(
                    '%s with Ref %s (ID %d) modified',
                    ucfirst($objectType),
                    $fileUpdated->getTitle(),
                    $fileUpdated->getId()
                ),
                $fileUpdated->getId()
            );
        } catch (FormValidationException $e) {
            $message = sprintf('Please check your input: %s', $e->getMessage());
        } catch (\Exception $e) {
            $message = sprintf('Sorry, an error occurred: %s', $e->getMessage() . ' ' . $e->getFile());
        }

        if ($message !== false) {
            Tlog::getInstance()->error(sprintf('Error during %s editing : %s.', $objectType, $message));

            $fileUpdateForm->setErrorMessage($message);

            $this->getParserContext()
                ->addForm($fileUpdateForm)
                ->setGeneralError($message);
        }
        if ($this->getRequest()->get('save_mode') === 'close') {
            return $this->generateRedirect(
                URL::getInstance()->absoluteUrl($file->getRedirectionUrl(), ['current_tab' => 'images'])
            );
        }
        return $this->generateSuccessRedirect($fileUpdateForm);
    }

    /**
     * @param int $imageId
     * @param string $parentType
     * @return mixed|Response|FileModelInterface
     * @throws \Exception
     */
    public function updateImageAction($imageId, $parentType)
    {
        $this->addModuleResource($parentType);
        if (null !== $response = $this->checkAccessForType(AccessManager::UPDATE, $parentType)) {
            return $response;
        }
        $this->registerFileModel($parentType);
        return $this->updateFileAction($imageId, $parentType, 'image', TheliaEvents::IMAGE_UPDATE);
    }

    /**
     * @param $documentId
     * @param string $parentType
     * @param string $objectType
     * @param string $eventName
     * @return \Symfony\Component\HttpFoundation\Response|Response
     * @throws \Exception
     */
    public function toggleVisibilityFileAction($documentId, $parentType, $objectType, $eventName)
    {
        $message = null;
        $this->addModuleResource($parentType);
        $parentId = $this->getRequest()->get('parentId');
        $this->registerFileModel($parentType);
        $this->checkAccessForType(AccessManager::UPDATE, $parentType);
        $this->checkXmlHttpRequest();
        $fileManager = $this->getFileManager();
        $modelInstance = $fileManager->getModelInstance($objectType, $parentType);
        $model = $modelInstance->getQueryInstance()->findPk($documentId);
        if ($model === null) {
            return $this->pageNotFound();
        }

        // Feed event
        $event = new FileToggleVisibilityEvent(
            $modelInstance->getQueryInstance(),
            $documentId
        );

        // Dispatch Event to the Action
        try {
            $this->dispatch($eventName, $event);
        } catch (\Exception $e) {
            $message = $this->getTranslator()->trans(
                'Fail to update %type% visibility: %err%',
                ['%type%' => $objectType, '%err%' => $e->getMessage()]
            );
        }

        if (null === $message) {
            $message = $this->getTranslator()->trans(
                '%type% visibility updated',
                ['%type%' => ucfirst($objectType)]
            );
        }
        $this->adminUpadteLogAppend($parentType, $message, $documentId);
        return $this->generateRedirectFromRoute('selection.update', [], ['selectionId' => $parentId], null);
    }

    /**
     * @param $parentType
     * @param $parentId
     * @param $objectType
     * @param $eventName
     * @return Response
     * @throws \Exception
     */
    public function updateFilePositionAction($parentType, $parentId, $objectType, $eventName)
    {
        $message = null;
        $this->addModuleResource($parentType);
        $this->registerFileModel($parentType);
        $position = $this->getRequest()->request->get('position');

        $this->checkAccessForType(AccessManager::UPDATE, $parentType);
        $this->checkXmlHttpRequest();

        $fileManager = $this->getFileManager();
        $modelInstance = $fileManager->getModelInstance($objectType, $parentType);
        $model = $modelInstance->getQueryInstance()->findPk($parentId);

        if ($model === null || $position === null) {
            return $this->pageNotFound();
        }

        // Feed event
        $event = new UpdateFilePositionEvent(
            $modelInstance->getQueryInstance(),
            $parentId,
            UpdateFilePositionEvent::POSITION_ABSOLUTE,
            $position
        );

        // Dispatch Event to the Action
        try {
            $this->dispatch($eventName, $event);
        } catch (\Exception $e) {
            $message = $this->getTranslator()->trans(
                'Fail to update %type% position: %err%',
                ['%type%' => $objectType, '%err%' => $e->getMessage()]
            );
        }

        if (null === $message) {
            $message = $this->getTranslator()->trans(
                '%type% position updated',
                ['%type%' => ucfirst($objectType)]
            );
        }

        return new Response($message);
    }

    /**
     * @param string $type
     * @param $message string
     * @param string|null $resourceId
     */
    protected function adminUpadteLogAppend($type, $message, $resourceId = null)
    {
        $this->adminLogAppend(
            $this->getAdminResources()->getResource($type, ucfirst(Selection::DOMAIN_NAME)),
            AccessManager::UPDATE,
            $message,
            $resourceId
        );
    }

    /**
     * @param string $type
     * @throws \Exception
     */
    protected function addModuleResource($type)
    {
        $data = [strtoupper($type) => "admin.selection"];
        $module = ucfirst(Selection::DOMAIN_NAME);
        /** @noinspection PhpParamsInspection */
        $this->getAdminResources()->addModuleResources($data, $module);
    }

    /**
     * @param string $access
     * @param string $type
     * @return mixed null if authorization is granted, or a Response object which contains the error page otherwise
     */
    protected function checkAccessForType($access, $type)
    {
        return $this->checkAuth(
            $this->getAdminResources()->getResource($type, ucfirst(Selection::DOMAIN_NAME)),
            array(),
            $access
        );
    }

    /**
     * @param string $type
     */
    private function registerFileModel($type)
    {
        $this->getFileManager()->addFileModel(
            'image',
            $type,
            $type === 'SelectionContainer' ? SelectionContainerImage::class : SelectionImage::class
        );
    }

    private function getImagetTypeUpdateRedirectionUrl($parentType, $parentId)
    {
        if ($parentType === SelectionContainer::IMAGE_TYPE_LABEL) {
            return $this->generateRedirectFromRoute('admin.selection.container.update', [], ['selectionContainerId' => $parentId, 'current_tab' => 'images'], null);
        }
        return $this->generateRedirectFromRoute('selection.update', [], ['selectionId' => $parentId, 'current_tab' => 'images'], null);
    }
}
