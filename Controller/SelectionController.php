<?php

namespace Selection\Controller;

use Thelia\Controller\Admin\BaseAdminController;
use Thelia\Core\Event\UpdatePositionEvent;
use Thelia\Core\HttpFoundation\Request;

class SelectionController extends BaseAdminController
{
    /**
     * Show the default template : selectionList
     *
     * @return \Thelia\Core\HttpFoundation\Response
     */
    public function viewAction()
    {
        return $this->render(
            "selection-list",
            [
                'selection_order' => $this->getAttributeSelectionOrder(),
                'selection_container_order' => $this->getAttributeContainerOrder()
            ]
        );
    }

    private function getAttributeSelectionOrder()
    {
        return $this->getListOrderFromSession(
            'selection',
            'selection_order',
            'manual'
        );
    }

    private function getAttributeContainerOrder()
    {
        return $this->getListOrderFromSession(
            'selectioncontainer',
            'selection_container_order',
            'manual'
        );
    }

    protected function createUpdatePositionEvent(Request $request, $positionChangeMode, $positionValue)
    {
        return new UpdatePositionEvent(
            $request->get('selection_id', null),
            $positionChangeMode,
            $positionValue
        );
    }
}