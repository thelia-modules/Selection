<?php
/**
 * Created by PhpStorm.
 * User: mbruchet
 * Date: 15/03/2018
 * Time: 10:15
 */

namespace Selection\Loop;

use Propel\Runtime\ActiveQuery\Criteria;
use Propel\Runtime\ActiveQuery\Join;
use Selection\Model\Map\SelectionFolderI18nTableMap;
use Selection\Model\Map\SelectionFolderTableMap;
use Selection\Model\Map\SelectionSelectionFolderTableMap;
use Selection\Model\SelectionFolder;
use Selection\Model\SelectionFolderI18n;
use Selection\Model\SelectionFolderI18nQuery;
use Selection\Model\SelectionFolderQuery;
use Selection\Model\SelectionSelectionFolder;
use Thelia\Core\Template\Element\BaseLoop;
use Thelia\Core\Template\Element\LoopResult;
use Thelia\Core\Template\Element\LoopResultRow;
use Thelia\Core\Template\Element\PropelSearchLoopInterface;
use Thelia\Core\Template\Loop\Argument\Argument;
use Thelia\Core\Template\Loop\Argument\ArgumentCollection;
use Thelia\Model\Folder;
use Thelia\Model\FolderI18nQuery;
use Thelia\Model\FolderQuery;
use Thelia\Model\Map\FolderTableMap;

class SelectionFolderLoop extends BaseLoop implements PropelSearchLoopInterface
{
    /**
     * @return ArgumentCollection
     */
    protected function getArgDefinitions()
    {
        return new ArgumentCollection(
            Argument::createIntListTypeArgument('folder_id'),
            Argument::createIntListTypeArgument('selection_id'),
            Argument::createAnyTypeArgument('folder_title'),
            Argument::createAnyTypeArgument('folder_chapo'),
            Argument::createAnyTypeArgument('folder_description'),
            Argument::createAnyTypeArgument('folder_postscriptum'),
            Argument::createIntListTypeArgument('folder_position'),
            Argument::createIntListTypeArgument('folder_visible'),
            Argument::createIntListTypeArgument('parent')

        );
    }

    /**
     * this method returns a Propel ModelCriteria
     *
     * @return \Propel\Runtime\ActiveQuery\ModelCriteria
     */
    public function buildModelCriteria()
    {
        $search = SelectionFolderQuery::create();

        if (null !== $folder_id = $this->getFolderId()) {
            $search->filterById($folder_id);
        }

        if (null !== $position = $this->getFolderPosition()) {
            $search->filterByPosition($position, Criteria::IN);
        }

        if (null !== $visible = $this->getFolderVisible()) {
            $search->filterByVisible($visible);
        }

        if (null !== $selection_id = $this->getSelectionId()) {
            $join = new Join(
                SelectionFolderTableMap::ID,
                SelectionSelectionFolderTableMap::SELECTION_FOLDER_ID,
                Criteria::INNER_JOIN
            );
            $search->addJoinObject($join, 'search');

            $search->addJoinCondition(
                'search',
                SelectionSelectionFolderTableMap::SELECTION_ID."=". $selection_id
            );
        }
        $join2 = new Join(
            SelectionFolderTableMap::ID,
            SelectionFolderI18nTableMap::ID,
            Criteria::INNER_JOIN
        );
        $search->addJoinObject($join2, 'search');

        if (null !== $folder_title = $this->getFolderTitle()) {
            $search->addJoinCondition(
                'search',
                SelectionFolderI18nTableMap::TITLE."=". $folder_title
            );
        }
        if (null !== $folder_chapo = $this->getFolderChapo()) {
            $search->addJoinCondition(
                'search',
                SelectionFolderI18nTableMap::CHAPO."=". $folder_chapo
            );
        }
        if (null !== $folder_description= $this->getFolderDescription()) {
            $search->addJoinCondition(
                'search',
                SelectionFolderI18nTableMap::DESCRIPTION."=". $folder_description
            );
        }
        if (null !== $folder_postscriptum = $this->getFolderPostscriptum()) {
            $search->addJoinCondition(
                'search',
                SelectionFolderI18nTableMap::POSTSCRIPTUM."=". $folder_postscriptum
            );
        }

        if (null !== $parent = $this->getParent()) {
            $search->filterByParent($parent);
        }

        return $search;
    }

    /**
     * @param LoopResult $loopResult
     *
     * @return LoopResult
     */
    public function parseResults(LoopResult $loopResult)
    {
        foreach ($loopResult->getResultDataCollection() as $folder) {

            /** @var SelectionFolder $folder */
            $loopResultRow = new LoopResultRow($folder);
            $lang = $this->request->getSession()->get('thelia.current.lang');
            $loopResultRow
                ->set('folder_id', $folder->getId())
                ->set('folder_title', $folder->getTranslation($lang->getLocale())->getTitle())
                ->set('folder_description', $folder->getTranslation($lang->getLocale())->getDescription())
                ->set('folder_chapo', $folder->getTranslation($lang->getLocale())->getChapo())
                ->set('folder_postscriptum', $folder->getTranslation($lang->getLocale())->getPostscriptum())
                ->set('folder_position', $folder->getPosition())
                ->set('folder_visible', $folder->getVisible())
                ->set('parentId', $folder->getParent());
            $loopResult->addRow($loopResultRow);
        }

        return $loopResult;
    }
}
