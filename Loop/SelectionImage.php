<?php
/**
 * Created by PhpStorm.
 * User: mbruchet
 * Date: 23/03/2018
 * Time: 12:24
 */

namespace Selection\Loop;

use Propel\Runtime\ActiveQuery\Criteria;
use Thelia\Core\Template\Element\PropelSearchLoopInterface;
use Thelia\Core\Template\Loop\Image;

class SelectionImage extends Image implements PropelSearchLoopInterface
{

    protected function createSearchQuery($source, $object_id)
    {
        $object = ucfirst($source);

        $ns = 'Selection\Model';

        if ('\\' !== $ns[0]) {
            $ns = '\\'.$ns;
        }

        $queryClass   = sprintf("%s\\%sImageQuery", $ns, $object);
        $filterMethod = sprintf("filterBy%sId", $object);

        // xxxImageQuery::create()
        $method = new \ReflectionMethod($queryClass, 'create');
        $search = $method->invoke(null); // Static !

        // $query->filterByXXX(id)
        if (! is_null($object_id)) {
            $method = new \ReflectionMethod($queryClass, $filterMethod);
            $method->invoke($search, $object_id);
        }

        $orders  = $this->getOrder();

        // Results ordering
        foreach ($orders as $order) {
            switch ($order) {
                case "alpha":
                    $search->addAscendingOrderByColumn('i18n_TITLE');
                    break;
                case "alpha-reverse":
                    $search->addDescendingOrderByColumn('i18n_TITLE');
                    break;
                case "manual-reverse":
                    $search->orderByPosition(Criteria::DESC);
                    break;
                case "manual":
                    $search->orderByPosition(Criteria::ASC);
                    break;
                case "random":
                    $search->clearOrderByColumns();
                    $search->addAscendingOrderByColumn('RAND()');
                    break(2);
                    break;
            }
        }

        return $search;
    }
}
