<?php
namespace Selection\Controller;

use Thelia\Controller\Admin\BaseAdminController;
use Thelia\Core\Event\UpdatePositionEvent;

class SelectionController extends BaseAdminController
{
    /**
     * Show the default template : selectionList
     *
     * @return \Thelia\Core\HttpFoundation\Response
     */
    public function viewAction()
    {
        return $this->render("selectionlist",
            array(
                'selection_order' => $this->getAttributeOrder()
            ));
    }

    protected function createUpdatePositionEvent($positionChangeMode, $positionValue)
    {
        return new UpdatePositionEvent(
            $this->getRequest()->get('selection_id', null),
            $positionChangeMode,
            $positionValue
        );
    }

    private function getAttributeOrder()
    {
        return $this->getListOrderFromSession(
            'selection',
            'selection_order',
            'manual'
        );
    }
}
