<?php
/**
 * Created by PhpStorm.
 * User: tpradatos
 * Date: 06/04/2018
 * Time: 16:40
 */

namespace Selection\Controller;


use Propel\Runtime\ActiveQuery\Criteria;
use Propel\Runtime\ActiveQuery\Join;
use Selection\Model\Map\SelectionSelectionFolderTableMap;
use Selection\Model\Map\SelectionTableMap;
use Selection\Model\Selection;
use Selection\Model\SelectionQuery;
use Selection\Model\SelectionSelectionFolder;
use Selection\Model\SelectionSelectionFolderQuery;
use Thelia\Controller\Admin\BaseAdminController;

class SelectionFolderSelectionRelated extends BaseAdminController
{

    public function addSelectionRelated()
    {
        $folderId = $this->getRequest()->get('folderID');
        $selectionID = $this->getRequest()->get('selectionID');

        $lang = $this->getRequest()->getSession()->get('thelia.current.lang');

        $selectionRelated = new SelectionSelectionFolder();

        if (null !== $selectionID) {
            $selectionFolder = SelectionSelectionFolderQuery::create()
                ->filterBySelectionFolderId($folderId)
                ->filterBySelectionId($selectionID)
                ->findOne();

            if (is_null($selectionFolder)) {

                $selectionRelated->setSelectionId($selectionID)
                                 ->setSelectionFolderId($folderId)
                                 ->setDefaultFolder($folderId);

                $position = SelectionSelectionFolderQuery::create()
                    ->filterBySelectionId($selectionID)
                    ->orderByPosition(Criteria::DESC)
                    ->select('position')
                    ->findOne();

                if (null === $position) {
                    $selectionRelated->setPosition(1);
                } else {
                    $selectionRelated->setPosition($position + 1);
                }

                $selectionRelated->save();
            }
            $search = SelectionQuery::create();
            $selectionSelectionRelated = new Join(
                SelectionTableMap::ID,
                SelectionSelectionFolderTableMap::SELECTION_ID,
                Criteria::INNER_JOIN
            );
            $search->addJoinCondition($selectionSelectionRelated, 'selectionSelectionRelated');
            $search->addJoinCondition(
                    'selectionSelectionRelated',
                    SelectionSelectionFolderTableMap::SELECTION_FOLDER_ID. '=' .$folderId
                );
            $search->find();

            /** @var Selection $row */
            foreach ($search as $row) {
                $selectionContentPos = SelectionSelectionFolderQuery::create()
                    ->filterBySelectionFolderId($folderId)
                    ->filterBySelectionId($row->getId())
                    ->findOne();

                $result = [
                    'id' => $row->getId() ,
                    'title' => $row->getTranslation($lang->getLocale())->getTitle(),
                    'position' => $selectionContentPos->getPosition()
                ];
            }


            return $this->render('related/contentRelated', ['selection_id' => $selectionID]);
        }
    }

}