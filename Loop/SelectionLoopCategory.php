<?php
/**
 * Created by PhpStorm.
 * User: mbruchet
 * Date: 12/03/2018
 * Time: 12:46
 */

namespace Selection\Loop;

use Thelia\Core\Template\Element\BaseLoop;
use Thelia\Core\Template\Element\LoopResult;
use Thelia\Core\Template\Element\LoopResultRow;
use Thelia\Core\Template\Element\PropelSearchLoopInterface;
use Thelia\Core\Template\Loop\Argument\Argument;
use Thelia\Core\Template\Loop\Argument\ArgumentCollection;
use Thelia\Model\Category;
use Thelia\Model\CategoryDocumentI18nQuery;
use Thelia\Model\CategoryQuery;

class SelectionLoopCategory extends BaseLoop implements PropelSearchLoopInterface
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
            Argument::createIntListTypeArgument('category_id'),
            Argument::createAnyTypeArgument('category_title')
        );
    }

    /**
     * this method returns a Propel ModelCriteria
     *
     * @return \Propel\Runtime\ActiveQuery\ModelCriteria
     */
    public function buildModelCriteria()
    {
        $search = CategoryQuery::create();


        if (null !== $category_id = $this->getCategoryId()) {
            $search->filterById();
        }

        if (null !== $category_title = $this->getCategoryTitle()) {
            $title = CategoryDocumentI18nQuery::create();
            $title->filterByTitle();
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
        foreach ($loopResult->getResultDataCollection() as $category) {
            /** @var Category $category */
            $loopResultRow = new LoopResultRow($category);
            $lang = $this->request->getSession()->get('thelia.current.lang');
            $loopResultRow
                ->set("CATEGORY_ID", $category->getId())
                ->set("CATEGORY_TITLE", $category->getTranslation($lang->getLocale())->getTitle());

            $loopResult->addRow($loopResultRow);
        }
        return $loopResult;
    }
}
