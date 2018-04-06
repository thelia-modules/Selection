<?php

namespace Selection\Controller;

use Selection\Model\SelectionImage;
use Selection\Model\SelectionImageQuery;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Thelia\Controller\Admin\FileController;
use Thelia\Core\Event\File\FileDeleteEvent;
use Thelia\Core\Event\File\FileToggleVisibilityEvent;
use Thelia\Core\Event\UpdateFilePositionEvent;
use Thelia\Core\HttpFoundation\Response;
use Thelia\Core\Security\AccessManager;
use Thelia\Files\Exception\ProcessFileException;
use Thelia\Files\FileModelInterface;
use Thelia\Tools\Rest\ResponseRest;

class SelectionImageUploadController extends FileController
{
    const MODULE_RIGHT = 'Selection';
    protected $currentRouter = "router.Selection";

    /**
     * @inheritdoc
     */
    public function getImageListAjaxAction($parentId, $parentType)
    {
        $data = ['SELECTION' => "admin.Selection"];
        $module = 'Selection';

        $this->getAdminResources()->addModuleResources($data, $module);
        $this->getFileManager()->addFileModel('image', $parentType, SelectionImage::class);

        $this->checkAuth(
            $this->getAdminResources()
                 ->getResource($parentType, static::MODULE_RIGHT),
            array(),
            AccessManager::UPDATE
        );
        $this->checkXmlHttpRequest();
        $args = array('imageType' => $parentType, 'parentId' => $parentId);

        return $this->render('image-upload-list-ajax', $args);
    }

    /**
     * @inheritdoc
     */
    public function getImageFormAjaxAction($parentId, $parentType)
    {
        $data = ['SELECTION' => "admin.Selection"];
        $module = 'Selection';

        $this->getAdminResources()->addModuleResources($data, $module);
        $this->getFileManager()->addFileModel('image', $parentType, SelectionImage::class);

        $this->checkAuth(
            $this->getAdminResources()
                 ->getResource($parentType, static::MODULE_RIGHT),
            array(),
            AccessManager::UPDATE
        );
        $this->checkXmlHttpRequest();
        $args = array('imageType' => $parentType, 'parentId' => $parentId);

        return $this->render('selectionImageUpdate', $args);
    }



    public function updateImageTitleAction($imageId, $parentType)
    {
        $data = ['SELECTION' => "admin.Selection"];
        $module = 'Selection';

        $parentId = $this->getRequest()->get('parentId');

        $this->getAdminResources()->addModuleResources($data, $module);
        $this->getFileManager()->addFileModel('image', $parentType, SelectionImage::class);
        if (null !== $response = $this->checkAuth(
            $this->getAdminResources()
                ->getResource($parentType, static::MODULE_RIGHT),
            array(),
            AccessManager::UPDATE
        )) {
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

        return $this->generateRedirectFromRoute('selection.update', [], ['selectionId' => $parentId], null);
    }

    public function deleteFileAction($fileId, $parentType, $objectType, $eventName)
    {
        $message = null;
        $data = ['SELECTION' => "admin.Selection"];
        $module = 'Selection';

        $parentId = $this->getRequest()->get('parentId');

        $this->getAdminResources()->addModuleResources($data, $module);
        $this->getFileManager()->addFileModel('document', $parentType, SelectionImage::class);
        $this->checkAuth(
            $this->getAdminResources()
                ->getResource($parentType, static::MODULE_RIGHT),
            array(),
            AccessManager::UPDATE
        );
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

            $this->adminLogAppend(
                $this->getAdminResources()->getResource($parentType, static::MODULE_RIGHT),
                AccessManager::UPDATE,
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

            $this->adminLogAppend(
                $this->getAdminResources()->getResource($parentType, static::MODULE_RIGHT),
                AccessManager::UPDATE,
                $message,
                $fileDeleteEvent->getFileToDelete()->getId()
            );
        }

        if (null === $message) {
            $message = $this->getTranslator()->trans(
                '%obj%s deleted successfully',
                ['%obj%' => ucfirst($objectType)],
                'image'
            );
        }

        return $this->generateRedirectFromRoute('selection.update', [], ['selectionId' => $parentId], null);
    }

    /*----------------- My parts */

    public function saveFileAjaxAction(
        $parentId,
        $parentType,
        $objectType,
        $validMimeTypes = array(),
        $extBlackList = array()
    ) {
        $data = ['SELECTION' => "admin.Selection"];
        $module = 'Selection';

        $this->getAdminResources()->addModuleResources($data, $module);
        $this->getFileManager()->addFileModel($objectType, $parentType, SelectionImage::class);
        if (null !== $response = $this->checkAuth(
            $this->getAdminResources()
                 ->getResource(
                     $parentType,
                     static::MODULE_RIGHT
                 ),
            array(),
            AccessManager::UPDATE
        )) {
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

            return $this->generateRedirectFromRoute('selection.update', [], ['selectionId' => $parentId], null);
        }

        return new Response('', 404);
    }

    public function viewImageAction($imageId, $parentType)
    {
        $data = ['SELECTION' => "admin.Selection"];
        $module = 'Selection';

        $this->getAdminResources()->addModuleResources($data, $module);
        $this->getFileManager()->addFileModel('image', $parentType, SelectionImage::class);

        if (null !== $response = $this->checkAuth(
            $this->getAdminResources()
                ->getResource(
                    $parentType,
                    static::MODULE_RIGHT
                ),
            array(),
            AccessManager::UPDATE
        )) {
            return $response;
        }

        $fileManager = $this->getFileManager();
        $imageModel = $fileManager->getModelInstance('image', $parentType);

        $image = SelectionImageQuery::create()->findPk($imageId);

        $redirectUrl = 'admin/selection/update/'. $image->getSelectionId() ;

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

    public function toggleVisibilityFileAction($documentId, $parentType, $objectType, $eventName)
    {
        $message = null;


        $data = ['SELECTION' => "admin.Selection"];
        $module = 'Selection';
        $parentId = $this->getRequest()->get('parentId');

        $this->getAdminResources()->addModuleResources($data, $module);
        $this->getFileManager()->addFileModel('image', $parentType, SelectionImage::class);

        $this->checkAuth(
            $this->getAdminResources()
                ->getResource(
                    $parentType,
                    static::MODULE_RIGHT
                ),
            array(),
            AccessManager::UPDATE
        );

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
                [ '%type%' => $objectType, '%err%' => $e->getMessage() ]
            );
        }

        if (null === $message) {
            $message = $this->getTranslator()->trans(
                '%type% visibility updated',
                [ '%type%' => ucfirst($objectType) ]
            );
        }
        return $this->generateRedirectFromRoute('selection.update', [], ['selectionId' => $parentId], null);
    }

    public function updateFilePositionAction($parentType, $parentId, $objectType, $eventName)
    {
        $message = null;

        $data = ['SELECTION' => "admin.Selection"];
        $module = 'Selection';

        $this->getAdminResources()->addModuleResources($data, $module);
        $this->getFileManager()->addFileModel('image', $parentType, SelectionImage::class);

        $position = $this->getRequest()->request->get('position');

        $this->checkAuth(
            $this->getAdminResources()
                ->getResource(
                    $parentType,
                    static::MODULE_RIGHT
                ),
            array(),
            AccessManager::UPDATE
        );

        $this->checkXmlHttpRequest();

        $fileManager = $this->getFileManager();
        $modelInstance = $fileManager->getModelInstance($objectType, $parentType);
        $model = $modelInstance->getQueryInstance()->findPk($parentId);

        if ($model === null || $position === null) {
            return $this->pageNotFound();
        }

        // Feed event
        $event = new UpdateFilePositionEvent(
            $modelInstance->getQueryInstance($parentType),
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
                [ '%type%' => $objectType, '%err%' => $e->getMessage() ]
            );
        }

        if (null === $message) {
            $message = $this->getTranslator()->trans(
                '%type% position updated',
                [ '%type%' => ucfirst($objectType) ]
            );
        }

        return new Response($message);
    }
}
