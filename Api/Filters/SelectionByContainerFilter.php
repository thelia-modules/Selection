<?php

namespace Selection\Api\Filters;

use ApiPlatform\Metadata\Operation;
use Propel\Runtime\ActiveQuery\ModelCriteria;
use Selection\Model\SelectionContainerAssociatedSelectionQuery;
use Thelia\Api\Bridge\Propel\Filter\AbstractFilter;
use Selection\Model\Map\SelectionContainerAssociatedSelectionTableMap;

class SelectionByContainerFilter extends AbstractFilter
{

    protected function filterProperty(
        string        $property,
                      $value,
        ModelCriteria $query,
        string        $resourceClass,
        Operation     $operation = null,
        array         $context = []
    ): void
    {
        if ($property !== "container_id"){
            return;
        }
        $selectionIds = SelectionContainerAssociatedSelectionQuery::create()
            ->filterBySelectionContainerId(explode(',',$value))
            ->select(SelectionContainerAssociatedSelectionTableMap::SELECTION_ID)
            ->find()
            ->toArray();

        $query->filterById($selectionIds);
    }

    public function getDescription(string $resourceClass): array
    {
        return [
            'container_id' => [
                'type' => 'array',
                'required' => false,
                'description' => 'Filters the resources to return only those whose container_id is included in the provided list.',
                'schema' => [
                    'type' => 'array',
                    'items' => [
                        'type' => 'integer',
                    ],
                ],
            ],
        ];
    }
}
