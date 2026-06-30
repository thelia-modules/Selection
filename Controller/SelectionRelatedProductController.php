<?php

namespace Selection\Controller;

use Propel\Runtime\ActiveQuery\Criteria;
use Propel\Runtime\ActiveQuery\Join;
use Selection\Model\Map\SelectionProductTableMap;
use Selection\Model\SelectionProduct;
use Selection\Model\SelectionProductQuery;
use Symfony\Component\HttpFoundation\Response;
use Thelia\Controller\Admin\BaseAdminController;
use Thelia\Core\Event\Loop\LoopExtendsBuildModelCriteriaEvent;
use Thelia\Core\HttpFoundation\Request;
use Thelia\Model\Lang;
use Thelia\Model\Map\ProductTableMap;
use Thelia\Model\Product;
use Thelia\Model\ProductCategory;
use Thelia\Model\ProductCategoryQuery;
use Thelia\Model\ProductQuery;
use Twig\Environment;

class SelectionRelatedProductController extends BaseAdminController
{
    public function __construct(private readonly Environment $twig)
    {
    }

    /**
     * Reproduce the selection_product_related loop in PHP, ordered by position.
     */
    private function getRelatedProductRows($selectionID, string $locale): array
    {
        $rows = [];
        $related = SelectionProductQuery::create()
            ->filterBySelectionId($selectionID)
            ->orderByPosition(Criteria::ASC)
            ->find();

        foreach ($related as $item) {
            $product = ProductQuery::create()->findPk($item->getProductId());
            if (null === $product) {
                continue;
            }
            $rows[] = [
                'id' => $item->getProductId(),
                'title' => $product->setLocale($locale)->getTitle(),
                'position' => $item->getPosition(),
            ];
        }

        return $rows;
    }

    private function renderRelatedProducts($selectionID, string $locale): Response
    {
        return new Response($this->twig->render(
            '@SelectionModule/backOffice/default-twig/related/productRelated.html.twig',
            [
                'selection_id' => $selectionID,
                'locale' => $locale,
                'rows' => $this->getRelatedProductRows($selectionID, $locale),
            ]
        ));
    }

    /**
     * Return product which they are related to a category id in a select.
     *
     * @return \Thelia\Core\HttpFoundation\Response
     */
    public function getProductRelated(Request $request)
    {
        $categoryID = $request->attributes->get('categoryID', $request->query->get('categoryID', $request->request->get('categoryID')));

        $lang = $request->hasSession() ? $request->getSession()->get('thelia.current.lang') : Lang::getDefaultLanguage();
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
        $productID = $request->attributes->get('productID', $request->query->get('productID', $request->request->get('productID')));
        $selectionID = $request->attributes->get('selectionID', $request->query->get('selectionID', $request->request->get('selectionID')));

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
                ProductTableMap::COL_ID,
                SelectionProductTableMap::COL_PRODUCT_ID,
                Criteria::INNER_JOIN
            );
            $search->addJoinObject($selectionProductRelated, 'selectionProductRelated');
            $search->addJoinCondition(
                'selectionProductRelated',
                SelectionProductTableMap::COL_SELECTION_ID . ' = ' . $selectionID
            );
            $search->find();
        }
        return $this->renderRelatedProducts($selectionID, $this->getCurrentEditionLocale());
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

        $selectionID = $request->attributes->get('selectionID', $request->query->get('selectionID', $request->request->get('selectionID')));
        $lang = $request->hasSession() ? $request->getSession()->get('thelia.current.lang') : Lang::getDefaultLanguage();

        /** @var  \Thelia\Model\Product $search */
        /** @var  LoopExtendsBuildModelCriteriaEvent $event */
        $search = ProductQuery::create();
        $selectionProductRelated = new Join(
            ProductTableMap::COL_ID,
            SelectionProductTableMap::COL_PRODUCT_ID,
            Criteria::INNER_JOIN
        );
        $search->addJoinObject($selectionProductRelated, 'selectionProductRelated');
        $search->addJoinCondition(
            'selectionProductRelated',
            SelectionProductTableMap::COL_SELECTION_ID . ' = ' . $selectionID
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
            return $this->renderRelatedProducts($selectionID, $lang->getLocale());
        } else {
            return $result;
        }
    }
}
