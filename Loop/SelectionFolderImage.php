<?php
/**
 * Created by PhpStorm.
 * User: tpradatos
 * Date: 06/04/2018
 * Time: 09:53
 */

namespace Selection\Loop;

use Propel\Runtime\ActiveQuery\Criteria;
use Thelia\Core\Event\Image\ImageEvent;
use Thelia\Core\Event\TheliaEvents;
use Thelia\Core\Template\Element\LoopResult;
use Thelia\Core\Template\Element\LoopResultRow;
use Thelia\Core\Template\Element\PropelSearchLoopInterface;
use Thelia\Core\Template\Loop\Image;
use Thelia\Log\Tlog;
use Thelia\Model\ConfigQuery;
use Thelia\Model\ProductImage;

class SelectionFolderImage extends Image implements PropelSearchLoopInterface
{

    protected function createSearchQuery($source, $object_id)
    {
        $object = ucfirst("SelectionFolder");

        $ns = 'Selection\Model';

        if ('\\' !== $ns[0]) {
            $ns = '\\'.$ns;
        }

        $queryClass   = sprintf("%s\\%sImageQuery", $ns, $object);
        $filterMethod = sprintf("filterBy%sId", $object);

        // xxxImageQuery::create()
        $method = new \ReflectionMethod($queryClass, 'create');
        $search = $method->invoke(null); // Static !

        // $query->filterByXXX(id)
        if (! is_null($object_id)) {
            $method = new \ReflectionMethod($queryClass, $filterMethod);
            $method->invoke($search, $object_id);
        }

        $orders  = $this->getOrder();

        // Results ordering
        foreach ($orders as $order) {
            switch ($order) {
                case "alpha":
                    $search->addAscendingOrderByColumn('i18n_TITLE');
                    break;
                case "alpha-reverse":
                    $search->addDescendingOrderByColumn('i18n_TITLE');
                    break;
                case "manual-reverse":
                    $search->orderByPosition(Criteria::DESC);
                    break;
                case "manual":
                    $search->orderByPosition(Criteria::ASC);
                    break;
                case "random":
                    $search->clearOrderByColumns();
                    $search->addAscendingOrderByColumn('RAND()');
                    break(2);
                    break;
            }
        }

        return $search;
    }

    public function parseResults(LoopResult $loopResult)
    {

        // Create image processing event
        $event = new ImageEvent();

        // Prepare tranformations
        $width = $this->getWidth();
        $height = $this->getHeight();
        $rotation = $this->getRotation();
        $background_color = $this->getBackgroundColor();
        $quality = $this->getQuality();
        $effects = $this->getEffects();

        $event->setAllowZoom($this->getAllowZoom());

        if (! is_null($effects)) {
            $effects = explode(',', $effects);
        }

        switch ($this->getResizeMode()) {
            case 'crop':
                $resizeMode = \Thelia\Action\Image::EXACT_RATIO_WITH_CROP;
                break;

            case 'borders':
                $resizeMode = \Thelia\Action\Image::EXACT_RATIO_WITH_BORDERS;
                break;

            case 'none':
            default:
                $resizeMode = \Thelia\Action\Image::KEEP_IMAGE_RATIO;

        }

        $baseSourceFilePath = ConfigQuery::read('images_library_path');
        if ($baseSourceFilePath === null) {
            $baseSourceFilePath = THELIA_LOCAL_DIR . 'media' . DS . 'images';
        } else {
            $baseSourceFilePath = THELIA_ROOT . $baseSourceFilePath;
        }

        /** @var ProductImage $result */
        foreach ($loopResult->getResultDataCollection() as $result) {
            // Setup required transformations
            if (! is_null($width)) {
                $event->setWidth($width);
            }
            if (! is_null($height)) {
                $event->setHeight($height);
            }
            $event->setResizeMode($resizeMode);
            if (! is_null($rotation)) {
                $event->setRotation($rotation);
            }
            if (! is_null($background_color)) {
                $event->setBackgroundColor($background_color);
            }
            if (! is_null($quality)) {
                $event->setQuality($quality);
            }
            if (! is_null($effects)) {
                $event->setEffects($effects);
            }

            // Put source image file path
            $sourceFilePath = sprintf(
                '%s/%s/%s',
                $baseSourceFilePath,
                'selection',
                $result->getFile()
            );

            $event->setSourceFilepath($sourceFilePath);
            $event->setCacheSubdirectory('selection');

            $loopResultRow = new LoopResultRow($result);

            $loopResultRow
                ->set("ID", $result->getId())
                ->set("LOCALE", $this->locale)
                ->set("ORIGINAL_IMAGE_PATH", $sourceFilePath)
                ->set("TITLE", $result->getVirtualColumn('i18n_TITLE'))
                ->set("CHAPO", $result->getVirtualColumn('i18n_CHAPO'))
                ->set("DESCRIPTION", $result->getVirtualColumn('i18n_DESCRIPTION'))
                ->set("POSTSCRIPTUM", $result->getVirtualColumn('i18n_POSTSCRIPTUM'))
                ->set("VISIBLE", $result->getVisible())
                ->set("POSITION", $result->getPosition())
                ->set("OBJECT_TYPE", $this->objectType)
                ->set("OBJECT_ID", $this->objectId)
            ;

            $addRow = true;

            $returnErroredImages = $this->getBackendContext() || ! $this->getIgnoreProcessingErrors();

            try {
                // Dispatch image processing event
                $this->dispatcher->dispatch(TheliaEvents::IMAGE_PROCESS, $event);
                $loopResultRow
                    ->set("IMAGE_URL", $event->getFileUrl())
                    ->set("ORIGINAL_IMAGE_URL", $event->getOriginalFileUrl())
                    ->set("IMAGE_PATH", $event->getCacheFilepath())
                    ->set("PROCESSING_ERROR", false)
                ;
            } catch (\Exception $ex) {
                // Ignore the result and log an error
                Tlog::getInstance()->addError(sprintf("Failed to process image in image loop: %s", $ex->getMessage()));

                if ($returnErroredImages) {
                    $loopResultRow
                        ->set("IMAGE_URL", '')
                        ->set("ORIGINAL_IMAGE_URL", '')
                        ->set("IMAGE_PATH", '')
                        ->set("PROCESSING_ERROR", true)
                    ;
                } else {
                    $addRow = false;
                }
            }

            if ($addRow) {
                $this->addOutputFields($loopResultRow, $result);

                $loopResult->addRow($loopResultRow);
            }
        }

        return $loopResult;
    }
}