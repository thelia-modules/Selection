<?php
/*************************************************************************************/
/*      Copyright (c) Open Studio                                                    */
/*      web : https://open.studio                                                    */
/*                                                                                   */
/*      For the full copyright and license information, please view the LICENSE      */
/*      file that was distributed with this source code.                             */
/*************************************************************************************/

/**
 * Created by Franck Allimant, OpenStudio <fallimant@openstudio.fr>
 * Date: 20/10/2021 23:48
 */

namespace Selection\Controller;

use Propel\Runtime\ActiveQuery\Criteria;
use Propel\Runtime\Exception\PropelException;
use Selection\Model\SelectionProductQuery;
use Selection\Selection;
use Symfony\Component\HttpFoundation\Response;
use Thelia\Controller\Admin\BaseAdminController;
use Thelia\Core\HttpFoundation\JsonResponse;
use Thelia\Core\HttpFoundation\Request;
use Thelia\Core\Security\AccessManager;
use Thelia\Core\Security\Resource\AdminResources;
use Thelia\Model\Map\ProductI18nTableMap;
use Thelia\Model\Map\ProductTableMap;
use Thelia\Model\ProductQuery;

class ProductSearchController extends BaseAdminController
{
    private const MAX_RESULTS = 15;

    /**
     * Products whose title, in the edition language, or reference contains the query, leaving out
     * the products already in the selection given by the "selection" parameter.
     *
     * @throws PropelException
     */
    public function search(Request $request): Response
    {
        if (null !== $response = $this->checkAuth(AdminResources::MODULE, [Selection::DOMAIN_NAME], AccessManager::VIEW)) {
            return $response;
        }

        $query = trim((string) $request->query->get('query', $request->request->get('query', '')));

        if ('' === $query) {
            return new JsonResponse([]);
        }

        $pattern = '%'.addcslashes($query, '%_\\').'%';

        $search = ProductQuery::create()
            ->useI18nQuery($this->getCurrentEditionLocale())
                ->withColumn(ProductI18nTableMap::COL_TITLE, 'title')
                ->withColumn('ISNULL('.ProductI18nTableMap::COL_TITLE.')', 'untitled')
            ->endUse()
            ->condition('title', ProductI18nTableMap::COL_TITLE.' LIKE ?', $pattern)
            ->condition('reference', ProductTableMap::COL_REF.' LIKE ?', $pattern)
            ->where(['title', 'reference'], Criteria::LOGICAL_OR)
            // A product with no title in the edition language comes after the titled ones.
            ->orderBy('untitled')
            ->orderBy('title')
            ->orderByRef()
            ->limit(self::MAX_RESULTS)
            ->select([ProductTableMap::COL_ID, ProductTableMap::COL_REF, 'title']);

        $productsInTheSelection = SelectionProductQuery::create()
            ->filterBySelectionId((int) $request->query->get('selection', $request->request->get('selection', 0)))
            ->select(['ProductId'])
            ->find()
            ->getData();

        if ([] !== $productsInTheSelection) {
            $search->filterById($productsInTheSelection, Criteria::NOT_IN);
        }

        $result = [];
        foreach ($search->find() as $item) {
            $result[] = [
                'id' => (int) $item[ProductTableMap::COL_ID],
                'ref' => $item[ProductTableMap::COL_REF],
                'title' => $item['title'],
            ];
        }

        return new JsonResponse($result);
    }
}
