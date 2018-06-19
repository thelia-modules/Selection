<?php

namespace Selection\Loop;

use Propel\Runtime\ActiveQuery\Criteria;
use Propel\Runtime\ActiveQuery\Join;
use Selection\Model\Map\SelectionTableMap;
use Selection\Model\Selection;
use Selection\Model\SelectionI18nQuery;
use Selection\Model\SelectionQuery;
use Thelia\Core\Template\Element\BaseI18nLoop;
use Thelia\Core\Template\Element\LoopResult;
use Thelia\Core\Template\Element\LoopResultRow;
use Thelia\Core\Template\Element\PropelSearchLoopInterface;
use Thelia\Core\Template\Loop\Argument\Argument;
use Thelia\Core\Template\Loop\Argument\ArgumentCollection;
use Thelia\Model\Map\RewritingUrlTableMap;
use Thelia\Type\BooleanOrBothType;

/**
 * Class SelectionLoop
 *
 * @package Thelia\Core\Template\Loop
 *
 * {@inheritdoc}
 * @method int[] getExclude()
 * @method int[] getId()
 * @method string getTitle()
 * @method int[] getPosition()
 * @method bool|string getVisible()
 */
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
            Argument::createBooleanOrBothTypeArgument('visible', true),
            Argument::createAnyTypeArgument('title'),
            Argument::createIntListTypeArgument('position'),
            Argument::createIntListTypeArgument('exclude')
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
        $this->configureI18nProcessing($search, array('TITLE', 'CHAPO', 'DESCRIPTION', 'POSTSCRIPTUM', 'META_TITLE', 'META_DESCRIPTION'));

        if (null !== $exclude = $this->getExclude()) {
            $search->filterById($exclude, Criteria::NOT_IN);
        }

        if (null !== $id = $this->getId()) {
            $search->filterById($id, Criteria::IN);
        }

        if (null !== $position = $this->getPosition()) {
            $search->filterByPosition($position, Criteria::IN);
        }


        if (null !== $title = $this->getTitle()) {
            //find all selections that match exactly this title and find with all locales.
            $search2 = SelectionI18nQuery::create()
                ->filterByTitle($title, Criteria::LIKE)
                ->select('id')
                ->find();

            if ($search2) {
                $search->filterById(
                    $search2,
                    Criteria::IN
                );
            }
        }

        $visible = $this->getVisible();
        if (BooleanOrBothType::ANY !== $visible) {
            $search->filterByVisible($visible ? 1 : 0);
        }

        $search->orderByPosition(Criteria::ASC);

        return $search;
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
            $loopResultRow
                ->set("SELECTION_ID", $selection->getId())
                ->set("SELECTION_URL", $this->getReturnUrl() ? $selection->getUrl($this->locale) : null)
                ->set("SELECTION_TITLE", $selection->geti18n_TITLE())
                ->set("SELECTION_META_TITLE", $selection->geti18n_META_TITLE())
                ->set("SELECTION_POSITION", $selection->getPosition())
                ->set("SELECTION_VISIBLE", $selection->getVisible())
                ->set("SELECTION_DESCRIPTION", $selection->geti18n_DESCRIPTION())
                ->set("SELECTION_META_DESCRIPTION", $selection->geti18n_META_DESCRIPTION())
                ->set("SELECTION_POSTSCRIPTUM", $selection->geti18n_POSTSCRIPTUM())
                ->set("SELECTION_CHAPO", $selection->geti18n_CHAPO());
            $loopResult->addRow($loopResultRow);
        }
        return $loopResult;
    }
}
