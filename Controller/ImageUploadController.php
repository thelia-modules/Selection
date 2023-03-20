<?php

namespace Selection\Controller;

use Selection\Model\SelectionContainer;
use Selection\Model\SelectionContainerImage;
use Selection\Model\SelectionContainerImageQuery;
use Selection\Model\SelectionImage;
use Selection\Model\SelectionImageQuery;
use Selection\Selection;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;
use Thelia\Controller\Admin\BaseAdminController;
use Thelia\Core\Event\File\FileCreateOrUpdateEvent;
use Thelia\Core\Event\File\FileDeleteEvent;
use Thelia\Core\Event\File\FileToggleVisibilityEvent;
use Thelia\Core\Event\TheliaEvents;
use Thelia\Core\Event\UpdateFilePositionEvent;
use Thelia\Core\HttpFoundation\Request;
use Thelia\Core\HttpFoundation\Response;
use Thelia\Core\Security\AccessManager;
use Thelia\Core\Translation\Translator;
use Thelia\Files\Exception\ProcessFileException;
use Thelia\Files\FileConfiguration;
use Thelia\Files\FileManager;
use Thelia\Files\FileModelInterface;
use Thelia\Form\Exception\FormValidationException;
use Thelia\Log\Tlog;
use Thelia\Model\Lang;
use Thelia\Tools\Rest\ResponseRest;
use Thelia\Tools\URL;

class ImageUploadController extends BaseAdminController
{
    public const MODULE_RIGHT = 'thelia';

    public function saveImageAjaxAction(FileManager $fileManager, Request $request, EventDispatcherInterface $eventDispatcher, $parentId, $parentType)
    {
        $config = FileConfiguration::getImageConfig();

        return $this->saveFileAjaxAction(
            $fileManager,
            $request,
            $eventDispatcher,
            $parentId,
            $parentType,
            $config['objectType'],
            $config['validMimeTypes'],
            $config['extBlackList']
        );
    }

    public function deleteImageAction(FileManager $fileManager, Request $request, EventDispatcherInterface $eventDispatcher, $imageId, $parentType)
    {
        return $this->deleteFileAction($fileManager, $request, $eventDispatcher, $imageId, $parentType, 'image', TheliaEvents::IMAGE_DELETE);
    }

    /**
     * @inheritdoc
     * @throws \Exception
     */
    public function getImageListAjaxAction(FileManager $fileManager, $parentId, $parentType)
    {
        $this->addModuleResource($parentType);
        $this->registerFileModel($fileManager, $parentType);
        $this->checkAccessForType(AccessManager::UPDATE, $parentType);
        $this->checkXmlHttpRequest();
        $args = array('imageType' => $parentType, 'parentId' => $parentId);
        return $this->render('image-upload-list-ajax', $args);
    }

    /**
     * @inheritdoc
     * @throws \Exception
     */
    public function getImageFormAjaxAction(FileManager $fileManager, $parentId, $parentType)
    {
        $this->addModuleResource($parentType);
        $this->registerFileModel($fileManager, $parentType);
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
    public function updateImageTitleAction(FileManager $fileManager, Request $request, $imageId, $parentType)
    {
        $this->addModuleResource($parentType);
        $parentId = $this->getRequest()->get('parentId');
        $this->registerFileModel($fileManager, $parentType);
        if (null !== $response = $this->checkAccessForType(AccessManager::UPDATE, $parentType)) {
            return $response;
        }

        $fileModelInstance = $fileManager->getModelInstance('image', $parentType);
        /** @var FileModelInterface $file */
        $file = $fileModelInstance->getQueryInstance()->findPk($imageId);

        $new_title = $request->get('title');
        $locale = $request->get('locale');

        if (!empty($new_title)) {
            $file->setLocale($locale);
            $file->setTitle($new_title);
            $file->save();
        }
        return $this->getImagetTypeUpdateRedirectionUrl($parentType, $parentId);
    }

    /**
     * @param EventDispatcherInterface $eventDispatcher
     * @param $fileId
     * @param $parentType
     * @param $objectType
     * @param $eventName
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response|Response
     * @throws \Exception
     */
    public function deleteFileAction(FileManager $fileManager, Request $request, EventDispatcherInterface $eventDispatcher, $fileId, $parentType, $objectType, $eventName)
    {
        $message = null;
        $this->addModuleResource($parentType);
        $parentId = $request->get('parentId');
        $this->registerFileModel($fileManager, $parentType);
        $this->checkAccessForType(AccessManager::UPDATE, $parentType);
        $this->checkXmlHttpRequest();

        $modelInstance = $fileManager->getModelInstance($objectType, $parentType);
        $model = $modelInstance->getQueryInstance()->findPk($fileId);
        if ($model == null) {
            return $this->pageNotFound();
        }
        // Feed event
        $fileDeleteEvent = new FileDeleteEvent($model);
        // Dispatch Event to the Action
        try {
            $eventDispatcher->dispatch($fileDeleteEvent, $eventName);
            $this->adminUpadteLogAppend(
                $parentType,
                Translator::getInstance()->trans(
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
            $message = Translator::getInstance()->trans(
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
            $message = Translator::getInstance()->trans(
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
        FileManager              $fileManager,
        Request                  $request,
        EventDispatcherInterface $eventDispatcher,
                                 $parentId,
                                 $parentType,
                                 $objectType,
                                 $validMimeTypes = [],
                                 $extBlackList = []
    )
    {
        $this->addModuleResource($parentType);
        $this->registerFileModel($fileManager, $parentType);
        if (null !== $response = $this->checkAccessForType(AccessManager::UPDATE, $parentType)) {
            return $response;
        }
        $this->checkXmlHttpRequest();
        if ($request->isMethod('POST')) {
            /** @var UploadedFile $fileBeingUploaded */
            $fileBeingUploaded = $request->files->get('file');
            try {
                if (null !== $fileBeingUploaded) {
                    $this->processFile(
                        $fileManager,
                        $eventDispatcher,
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
    public function viewImageAction(FileManager $fileManager, $imageId, $parentType)
    {
        $this->addModuleResource($parentType);
        $this->registerFileModel($fileManager, $parentType);
        if (null !== $response = $this->checkAccessForType(AccessManager::UPDATE, $parentType)) {
            return $response;
        }

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
     * @param EventDispatcherInterface $eventDispatcher
     * @param $fileId
     * @param $parentType
     * @param $objectType
     * @param $eventName
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response|FileModelInterface|null
     */
    protected function updateFileAction(
        FileManager              $fileManager,
        EventDispatcherInterface $eventDispatcher,
                                 $fileId,
                                 $parentType,
                                 $objectType,
                                 $eventName
    )
    {
        $message = false;

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

            $eventDispatcher->dispatch($event, $eventName);

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
     * @param EventDispatcherInterface $eventDispatcher
     * @param $imageId
     * @param $parentType
     * @return mixed|\Symfony\Component\HttpFoundation\Response|Response
     * @throws \Exception
     */
    public function updateImageAction(FileManager $fileManager, EventDispatcherInterface $eventDispatcher, $imageId, $parentType)
    {
        $this->addModuleResource($parentType);
        if (null !== $response = $this->checkAccessForType(AccessManager::UPDATE, $parentType)) {
            return $response;
        }
        $this->registerFileModel($fileManager, $parentType);
        return $this->updateFileAction($fileManager, $eventDispatcher, $imageId, $parentType, 'image', TheliaEvents::IMAGE_UPDATE);
    }

    /**
     * @param EventDispatcherInterface $eventDispatcher
     * @param $documentId
     * @param $parentType
     * @param $objectType
     * @param $eventName
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response|Response
     * @throws \Exception
     */
    public function toggleVisibilityFileAction(FileManager $fileManager, Request $request, EventDispatcherInterface $eventDispatcher, $documentId, $parentType, $objectType, $eventName)
    {
        $message = null;
        $this->addModuleResource($parentType);
        $parentId = $request->get('parentId');
        $this->registerFileModel($fileManager, $parentType);
        $this->checkAccessForType(AccessManager::UPDATE, $parentType);
        $this->checkXmlHttpRequest();

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
            $eventDispatcher->dispatch($event, $eventName);
        } catch (\Exception $e) {
            $message = Translator::getInstance()->trans(
                'Fail to update %type% visibility: %err%',
                ['%type%' => $objectType, '%err%' => $e->getMessage()]
            );
        }

        if (null === $message) {
            $message = Translator::getInstance()->trans(
                '%type% visibility updated',
                ['%type%' => ucfirst($objectType)]
            );
        }
        $this->adminUpadteLogAppend($parentType, $message, $documentId);
        return $this->generateRedirect(URL::getInstance()->absoluteUrl('/admin/selection/update/'.$parentId));
    }

    /**
     * @param EventDispatcherInterface $eventDispatcher
     * @param $parentType
     * @param $parentId
     * @param $objectType
     * @param $eventName
     * @return Response
     * @throws \Exception
     */
    public function updateFilePositionAction(FileManager $fileManager, Request $request, EventDispatcherInterface $eventDispatcher, $parentType, $parentId, $objectType, $eventName)
    {
        $message = null;
        $this->addModuleResource($parentType);
        $this->registerFileModel($fileManager, $parentType);
        $position = $request->get('position');

        $this->checkAccessForType(AccessManager::UPDATE, $parentType);
        $this->checkXmlHttpRequest();

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
            $eventDispatcher->dispatch($event, $eventName);
        } catch (\Exception $e) {
            $message = Translator::getInstance()->trans(
                'Fail to update %type% position: %err%',
                ['%type%' => $objectType, '%err%' => $e->getMessage()]
            );
        }

        if (null === $message) {
            $message = Translator::getInstance()->trans(
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

        $this->getAdminResources()->addResource(strtoupper($type), "admin.selection", 'thelia');
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
    private function registerFileModel(FileManager $fileManager, $type)
    {
        $fileManager->addFileModel(
            'image',
            $type,
            $type === 'SelectionContainer' ? SelectionContainerImage::class : SelectionImage::class
        );
    }

    private function getImagetTypeUpdateRedirectionUrl($parentType, $parentId)
    {
        if ($parentType === SelectionContainer::IMAGE_TYPE_LABEL) {
            return $this->generateRedirect('/admin/selection/container/update/'.$parentId.'?current_tab=images');
        }
        return $this->generateRedirect('/admin/selection/update/'.$parentId.'?current_tab=images');
    }

    public function processFile(
        FileManager              $fileManager,
        EventDispatcherInterface $eventDispatcher,
                                 $fileBeingUploaded,
                                 $parentId,
                                 $parentType,
                                 $objectType,
                                 $validMimeTypes = [],
                                 $extBlackList = []
    )
    {
        // Validate if file is too big
        if ($fileBeingUploaded->getError() == 1) {
            $message = Translator::getInstance()
                ->trans(
                    'File is too large, please retry with a file having a size less than %size%.',
                    ['%size%' => \ini_get('upload_max_filesize')],
                    'core'
                );

            throw new ProcessFileException($message, 403);
        }

        $message = null;
        $realFileName = $fileBeingUploaded->getClientOriginalName();

        if (!empty($validMimeTypes)) {
            $mimeType = $fileBeingUploaded->getMimeType();

            if (!isset($validMimeTypes[$mimeType])) {
                $message = Translator::getInstance()
                    ->trans(
                        'Only files having the following mime type are allowed: %types%',
                        ['%types%' => implode(', ', array_keys($validMimeTypes))]
                    );
            } else {
                $regex = "#^(.+)\.(" . implode('|', $validMimeTypes[$mimeType]) . ')$#i';

                if (!preg_match($regex, $realFileName)) {
                    $message = Translator::getInstance()
                        ->trans(
                            "There's a conflict between your file extension \"%ext\" and the mime type \"%mime\"",
                            [
                                '%mime' => $mimeType,
                                '%ext' => $fileBeingUploaded->getClientOriginalExtension(),
                            ]
                        );
                }
            }
        }

        if (!empty($extBlackList)) {
            $regex = "#^(.+)\.(" . implode('|', $extBlackList) . ')$#i';

            if (preg_match($regex, $realFileName)) {
                $message = Translator::getInstance()
                    ->trans(
                        'Files with the following extension are not allowed: %extension, please do an archive of the file if you want to upload it',
                        [
                            '%extension' => $fileBeingUploaded->getClientOriginalExtension(),
                        ]
                    );
            }
        }

        if ($message !== null) {
            throw new ProcessFileException($message, 415);
        }

        $fileModel = $fileManager->getModelInstance($objectType, $parentType);

        $parentModel = $fileModel->getParentFileModel();

        if ($parentModel === null || $fileModel === null || $fileBeingUploaded === null) {
            throw new ProcessFileException('', 404);
        }

        $defaultTitle = $parentModel->getTitle();

        if (empty($defaultTitle) && $objectType !== 'image') {
            $defaultTitle = $fileBeingUploaded->getClientOriginalName();
        }

        $fileModel
            ->setParentId($parentId)
            ->setLocale(Lang::getDefaultLanguage()->getLocale())
            ->setTitle($defaultTitle);

        $fileCreateOrUpdateEvent = new FileCreateOrUpdateEvent($parentId);
        $fileCreateOrUpdateEvent->setModel($fileModel);
        $fileCreateOrUpdateEvent->setUploadedFile($fileBeingUploaded);
        $fileCreateOrUpdateEvent->setParentName($parentModel->getTitle());

        // Dispatch Event to the Action
        $eventDispatcher->dispatch(
            $fileCreateOrUpdateEvent,
            TheliaEvents::IMAGE_SAVE
        );

        $this->adminLogAppend(
            $this->getAdminResources()->getResource($parentType, static::MODULE_RIGHT),
            AccessManager::UPDATE,
            Translator::getInstance()->trans(
                'Saving %obj% for %parentName% parent id %parentId%',
                [
                    '%parentName%' => $fileCreateOrUpdateEvent->getParentName(),
                    '%parentId%' => $fileCreateOrUpdateEvent->getParentId(),
                    '%obj%' => $objectType,
                ]
            )
        );

        // return new ResponseRest(array('status' => true, 'message' => ''));
        return $fileCreateOrUpdateEvent;
    }
}
