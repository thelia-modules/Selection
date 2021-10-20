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
use Thelia\Controller\Admin\BaseAdminController;
use Thelia\Core\HttpFoundation\JsonResponse;
use Thelia\Model\Lang;
use Thelia\Model\Map\ProductI18nTableMap;
use Thelia\Model\Map\ProductTableMap;
use Thelia\Model\ProductQuery;

class ProductSearchController extends BaseAdminController
{
    public function search()
    {
        $locale = $this->getCurrentEditionLocale();

        $ref = $this->getRequest()->get('query', null);

        $result = [];

        if (! empty($ref)) {
            $data = ProductQuery::create()
                ->filterByRef("%$ref%", Criteria::LIKE)
                ->orderByRef()
                ->useI18nQuery($locale)
                ->withColumn(ProductI18nTableMap::COL_TITLE, 'title')
                ->endUse()
                ->limit(15)
                ->select([
                    ProductTableMap::COL_ID,
                    ProductTableMap::COL_REF,
                    'title'
                ])
                ->find();

            foreach ($data as $item) {
                $result[] = [
                    'id' => $item[ProductTableMap::COL_ID],
                    'ref' => $item[ProductTableMap::COL_REF],
                    'title' => $item['title'],
                ];
            }
        }

        return JsonResponse::create($result);
    }
}
