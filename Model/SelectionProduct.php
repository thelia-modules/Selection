<?php

namespace Selection\Model;

use Selection\Model\Base\SelectionProduct as BaseSelectionProduct;
use Thelia\Model\Tools\PositionManagementTrait;

class SelectionProduct extends BaseSelectionProduct
{
    use PositionManagementTrait;

    /**
     * @inheritdoc
     */
    protected function addCriteriaToPositionQuery(SelectionProductQuery $query)
    {
        $query->filterBySelectionId($this->getSelectionId());
    }

}
