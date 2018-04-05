<?php
/**
 * Created by PhpStorm.
 * User: mbruchet
 * Date: 09/03/2018
 * Time: 10:09
 */

namespace Selection\Loop;

use Propel\Runtime\ActiveQuery\Criteria;
use Propel\Runtime\ActiveQuery\Join;
use Selection\Model\Map\SelectionFolderTableMap;
use Selection\Model\Map\SelectionI18nTableMap;
use Selection\Model\Map\SelectionImageTableMap;
use Selection\Model\Map\SelectionSelectionFolderTableMap;
use Selection\Model\Map\SelectionTableMap;
use Selection\Model\Selection;
use Selection\Model\SelectionFolder;
use Selection\Model\SelectionFolderQuery;
use Selection\Model\SelectionQuery;
use Selection\Model\SelectionSelectionFolder;
use Selection\Model\SelectionSelectionFolderQuery;
use Thelia\Core\Template\Element\BaseI18nLoop;
use Thelia\Core\Template\Element\LoopResult;
use Thelia\Core\Template\Element\LoopResultRow;
use Thelia\Core\Template\Element\PropelSearchLoopInterface;
use Thelia\Core\Template\Loop\Argument\Argument;
use Thelia\Core\Template\Loop\Argument\ArgumentCollection;
use Thelia\Type\BooleanOrBothType;

class SelectionLoop extends BaseI18nLoop implements PropelSearchLoopInterface
{

    public $countable = true;
    public $timestampable = false;
    public $versionable = false;

    /***
     * @return ArgumentCollection
     */
    protected function getArgDefinitions()
    {
        return new ArgumentCollection(
            Argument::createIntListTypeArgument('id'),
            Argument::createBooleanOrBothTypeArgument('visible'),
            Argument::createAnyTypeArgument('title'),
            Argument::createIntListTypeArgument('position'),
            Argument::createIntListTypeArgument('parent')
        );
    }

    /**
     * @return \Propel\Runtime\ActiveQuery\ModelCriteria|SelectionQuery
     * @throws \Propel\Runtime\Exception\PropelException
     */
    public function buildModelCriteria()
    {
        $search = SelectionQuery::create();

        /* manage translations */
        $this->configureI18nProcessing($search, array('TITLE', 'CHAPO', 'DESCRIPTION', 'POSTSCRIPTUM',));

        if (null !== $id = $this->getId()) {
            $search->filterById($id, Criteria::IN);
        }

        if (null !== $title = $this->getTitle()) {
            $join = new Join(
                SelectionI18nTableMap::ID,
                SelectionTableMap::ID,
                Criteria::INNER_JOIN
            );
            $search->addJoinObject($join, 'search')
                   ->addJoinCondition('search', SelectionI18nTableMap::TITLE."=". $title);
        }

        if (null !== $position = $this->getPosition()) {
            $search->filterByPosition($position, Criteria::IN);
        }

        $visible = $this->getVisible();
        if (BooleanOrBothType::ANY !== $visible) {
            $search->filterByVisible($visible ? 1 : 0);
        }

        if (null !== $parent = $this->getParent()) {
            $search
                ->useSelectionSelectionFolderQuery()
                    ->filterByDefaultFolder(true)
                    ->filterBySelectionFolderId($parent, Criteria::IN)
                ->endUse();
            $a = $search->toString();
        }


        return $search->orderByPosition(Criteria::ASC);
    }

    /**
     * @param LoopResult $loopResult
     *
     * @return LoopResult
     */
    public function parseResults(LoopResult $loopResult)
    {
        foreach ($loopResult->getResultDataCollection() as $selection) {

            /** @var Selection $selection */
            $loopResultRow = new LoopResultRow($selection);
            $lang = $this->request->getSession()->get('thelia.current.lang');
            $loopResultRow
                ->set("SELECTION_ID", $selection->getId())
                ->set("SELECTION_TITLE", $selection->getTranslation($lang->getLocale())->getTitle())
                ->set("SELECTION_POSITION", $selection->getPosition())
                ->set("SELECTION_VISIBLE", $selection->getVisible());
            $loopResult->addRow($loopResultRow);
        }
        return $loopResult;
    }
}
