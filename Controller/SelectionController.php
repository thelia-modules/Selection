<?php
/**
 * Created by PhpStorm.
 * User: mbruchet
 * Date: 09/03/2018
 * Time: 09:57
 */

namespace Selection\Controller;

use Selection\Model\SelectionI18nQuery;
use Thelia\Controller\Admin\BaseAdminController;

class SelectionController extends BaseAdminController
{
    /**
     * Show the default template : selectionList
     *
     * @return \Thelia\Core\HttpFoundation\Response
     */
    public function viewAction()
    {
        return $this->render("selectionlist");
    }

    /**
     * @return \Thelia\Core\HttpFoundation\Response
     * @throws \Exception
     */
    public function updateAction()
    {
        $selectionID = $this->getRequest()->get('selectionId');
        $response = array();

        try {
            $selection = SelectionI18nQuery::create()
                ->filterById($selectionID)
                ->findOne();

            if ($selection !== null) {
                $id          = $selection->getId();
                $title       = $selection->getTitle();
                $summary     = $selection->getChapo();
                $description = $selection->getDescription();
                $conclusion  = $selection->getPostscriptum();
                $locale = $this->getRequest()->getSession()->get('thelia.current.lang')->getLocale();

                $response = [
                    'id'          => $id,
                    'title'       => $title,
                    'summary'     => $summary,
                    'description' => $description,
                    'conclusion'  => $conclusion,
                ];
                $selectionSeo = new SelectionUpdateController();
                $selectionSeo->updateAction(
                    $id,
                    $locale = $this->getRequest()
                                    ->getSession()
                                    ->get('thelia.current.lang')
                                    ->getLocale()
                );
            }
        } catch (\Exception $ex) {
            throw $ex;
        }

        return $this->render("selection-edit", $response);
    }
}
