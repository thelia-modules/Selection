<?php

namespace Selection\Loop;

use Propel\Runtime\ActiveQuery\Criteria;
use Propel\Runtime\ActiveQuery\Join;
use Selection\Model\Map\SelectionContentTableMap;
use Selection\Model\SelectionContent;
use Selection\Model\SelectionContentQuery;
use Thelia\Core\Template\Element\BaseLoop;
use Thelia\Core\Template\Element\LoopResult;
use Thelia\Core\Template\Element\LoopResultRow;
use Thelia\Core\Template\Element\PropelSearchLoopInterface;
use Thelia\Core\Template\Loop\Argument\Argument;
use Thelia\Core\Template\Loop\Argument\ArgumentCollection;
use Thelia\Model\Map\ContentI18nTableMap;

class SelectionContentRelated extends BaseLoop implements PropelSearchLoopInterface
{
    public $countable = true;
    public $timestampable = false;
    public $versionable = false;

    protected function getArgDefinitions()
    {
        return new ArgumentCollection(
            Argument::createIntListTypeArgument('content_id'),
            Argument::createIntListTypeArgument('selection_id'),
            Argument::createAnyTypeArgument('content_title'),
            Argument::createIntListTypeArgument('position'),
            Argument::createAnyTypeArgument('locale')
        );
    }

    /**
     * @return \Propel\Runtime\ActiveQuery\ModelCriteria|SelectionContentQuery
     * @throws \Propel\Runtime\Exception\PropelException
     */
    public function buildModelCriteria()
    {
        $search = SelectionContentQuery::create();


        if (null !== $content_id = $this->getContentId()) {
            $search->filterByContentId($content_id, Criteria::IN);
        }

        if (null !== $position = $this->getPosition()) {
            $search->filterByPosition($position, Criteria::IN);
        }
        if (null !== $selection_id = $this->getSelectionId()) {
            $search->filterBySelectionId($selection_id, Criteria::IN);
        }

        if (null !== $content_title = $this->getContentTitle()) {
            $join = new Join(
                ContentI18nTableMap::ID,
                SelectionContentTableMap::CONTENT_ID,
                Criteria::INNER_JOIN
            );
            $search->addJoinObject($join, 'search')
                ->addJoinCondition('search', ContentI18nTableMap::TITLE."=". $content_title);
        }
        return $search->orderByPosition(Criteria::ASC);
    }

    /**
     * @param LoopResult $loopResult
     * @return LoopResult
     * @throws \Propel\Runtime\Exception\PropelException
     */
    public function parseResults(LoopResult $loopResult)
    {

        foreach ($loopResult->getResultDataCollection() as $content) {

            /** @var SelectionContent $content */
            $loopResultRow = new LoopResultRow($content);

            $loopResultRow
                ->set("CONTENT_ID", $content->getContentId())
                ->set("CONTENT_TITLE", $content->getContent()->setLocale($this->getLocale())->getTitle())
                ->set("POSITION", $content->getPosition())
                ->set("selection_id", $content->getSelectionId());

            $loopResult->addRow($loopResultRow);
        }

        return $loopResult;
    }
}
