<?php

namespace Selection\Controller;

use Propel\Runtime\ActiveQuery\Criteria;
use Propel\Runtime\ActiveQuery\Join;
use Selection\Model\Map\SelectionProductTableMap;
use Selection\Model\SelectionProduct;
use Selection\Model\SelectionProductQuery;
use Thelia\Controller\Admin\BaseAdminController;
use Thelia\Core\Event\Loop\LoopExtendsBuildModelCriteriaEvent;
use Thelia\Core\HttpFoundation\Request;
use Thelia\Model\Map\ProductTableMap;
use Thelia\Model\Product;
use Thelia\Model\ProductCategory;
use Thelia\Model\ProductCategoryQuery;
use Thelia\Model\ProductQuery;

class SelectionRelatedProductController extends BaseAdminController
{

    /**
     * Return product which they are related to a category id in a select.
     *
     * @return \Thelia\Core\HttpFoundation\Response
     */
    public function getProductRelated(Request $request)
    {
        $categoryID = $request->get('categoryID');

        $lang = $request->getSession()->get('thelia.current.lang');
        $productCategory = ProductCategoryQuery::create();

        $result = array();

        if ($categoryID !== null) {
            $productCategory->filterByCategoryId($categoryID)
                ->find();
            if ($productCategory !== null) {
                /** @var ProductCategory $item */
                foreach ($productCategory as $item) {
                    $product = ProductQuery::create()
                        ->filterById($item->getProductId())
                        ->filterByVisible(1)
                        ->findOne();

                    if (null !== $product) {
                        $result[] = [
                            'id' => $product->getId(),
                            'title' => $product->getTranslation($lang->getLocale())->getTitle()
                        ];
                    }
                }
            }
        }
        return $this->jsonResponse(json_encode($result));
    }

    /**
     * Add product to the current selection
     *
     * @return \Thelia\Core\HttpFoundation\Response
     * @throws \Propel\Runtime\Exception\PropelException
     */
    public function addProductRelated(Request $request)
    {
        $productID = $request->get('productID');
        $selectionID = $request->get('selectionID');

        $productRelated = new SelectionProduct();

        if ($productID !== null) {
            $SelectionProduit = SelectionProductQuery::create()
                ->filterByProductId($productID)
                ->filterBySelectionId($selectionID)
                ->findOne();

            if (is_null($SelectionProduit)) {
                //Insert in the table Selection_product
                $productRelated->setSelectionId($selectionID);
                $productRelated->setProductId($productID);

                $position = SelectionProductQuery::create()
                    ->filterBySelectionId($selectionID)
                    ->orderByPosition(Criteria::DESC)
                    ->select('position')
                    ->findOne();

                if (null === $position) {
                    $productRelated->setPosition(1);
                } else {
                    $productRelated->setPosition($position + 1);
                }
                $productRelated->save();
            }
            /** @var  \Thelia\Model\Product $search */
            /** @var  LoopExtendsBuildModelCriteriaEvent $event */
            $search = ProductQuery::create();
            $selectionProductRelated = new Join(
                ProductTableMap::ID,
                SelectionProductTableMap::PRODUCT_ID,
                Criteria::INNER_JOIN
            );
            $search->addJoinObject($selectionProductRelated, 'selectionProductRelated');
            $search->addJoinCondition(
                'selectionProductRelated',
                SelectionProductTableMap::SELECTION_ID . ' = ' . $selectionID
            );
            $search->find();
        }
        return $this->render('related/productRelated', [
            'selection_id' => $selectionID,
            'locale' => $this->getCurrentEditionLocale()
        ]);
    }

    /**
     * Show product related to a selection
     *
     * @param null $p
     * @return array|\Thelia\Core\HttpFoundation\Response
     * @throws \Propel\Runtime\Exception\PropelException
     */
    public function showProduct(Request $request, $p = null)
    {

        $selectionID = $request->get('selectionID');
        $lang = $request->getSession()->get('thelia.current.lang');

        /** @var  \Thelia\Model\Product $search */
        /** @var  LoopExtendsBuildModelCriteriaEvent $event */
        $search = ProductQuery::create();
        $selectionProductRelated = new Join(
            ProductTableMap::ID,
            SelectionProductTableMap::PRODUCT_ID,
            Criteria::INNER_JOIN
        );
        $search->addJoinObject($selectionProductRelated, 'selectionProductRelated');
        $search->addJoinCondition(
            'selectionProductRelated',
            SelectionProductTableMap::SELECTION_ID . ' = ' . $selectionID
        );
        $search->find();

        $result = array();
        /** @var Product $row */
        foreach ($search as $row) {
            $selectionProductPos = SelectionProductQuery::create()
                ->filterBySelectionId($selectionID)
                ->filterByProductId($row->getId())
                ->findOne();

            $result = [
                'id' => $row->getId(),
                'title' => $row->getTranslation($lang->getLocale())->getTitle(),
                'position' => $selectionProductPos->getPosition(),
            ];
        }

        if ($p === null) {
            return $this->render('related/productRelated', ['selection_id' => $selectionID]);
        } else {
            return $result;
        }
    }
}
