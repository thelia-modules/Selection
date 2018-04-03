<?php
/**
 * Created by PhpStorm.
 * User: mbruchet
 * Date: 29/03/2018
 * Time: 11:45
 */

namespace Selection\Loop;

use Propel\Runtime\ActiveQuery\Criteria;
use Propel\Runtime\ActiveQuery\Join;
use Selection\Model\Map\SelectionContentTableMap;
use Selection\Model\SelectionProduct;
use Selection\Model\SelectionProductQuery;
use Thelia\Core\Template\Element\BaseLoop;
use Thelia\Core\Template\Element\LoopResult;
use Thelia\Core\Template\Element\LoopResultRow;
use Thelia\Core\Template\Element\PropelSearchLoopInterface;
use Thelia\Core\Template\Loop\Argument\Argument;
use Thelia\Core\Template\Loop\Argument\ArgumentCollection;
use Thelia\Model\Map\ProductI18nTableMap;

class SelectionProductRelated extends BaseLoop implements PropelSearchLoopInterface
{
    public $countable = true;
    public $timestampable = false;
    public $versionable = false;

    protected function getArgDefinitions()
    {
        return new ArgumentCollection(
            Argument::createIntListTypeArgument('product_id'),
            Argument::createAnyTypeArgument('product_title'),
            Argument::createIntListTypeArgument('selection_id'),
            Argument::createIntListTypeArgument('position')
        );
    }

    /**
     * @return \Propel\Runtime\ActiveQuery\ModelCriteria|SelectionProductQuery
     * @throws \Propel\Runtime\Exception\PropelException
     */
    public function buildModelCriteria()
    {
        $search = SelectionProductQuery::create();


        if (null !== $product_id = $this->getProductID()) {
            $search->filterByProductId($product_id, Criteria::IN);
        }


        if (null !== $selection_id = $this->getSelectionId()) {
            $search->filterBySelectionId($selection_id, Criteria::IN);
        }

        if (null !== $position = $this->getPosition()) {
            $search->filterByPosition($position, Criteria::IN);
        }

        if (null !== $product_title = $this->getProductTitle()) {
            $join = new Join(
                ProductI18nTableMap::ID,
                SelectionContentTableMap::CONTENT_ID,
                Criteria::INNER_JOIN
            );
            $search->addJoinObject($join, 'search')
                ->addJoinCondition('search', ProductI18nTableMap::TITLE."=". $product_title);
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

        foreach ($loopResult->getResultDataCollection() as $product) {

            /** @var SelectionProduct $product */
            $loopResultRow = new LoopResultRow($product);
            $lang = $this->request->getSession()->get('thelia.current.lang');
            $loopResultRow
                ->set("PRODUCT_ID", $product->getProductId())
                ->set("PRODUCT_TITLE", $product->getProduct()->getTitle())
                ->set("POSITION", $product->getPosition())
                ->set("selection_id", $product->getSelectionId());

            $loopResult->addRow($loopResultRow);
        }
        return $loopResult;
    }
}
