<?php

namespace Selection\Api\Filters;

use ApiPlatform\Metadata\Operation;
use Propel\Runtime\ActiveQuery\ModelCriteria;
use Selection\Model\SelectionContainerAssociatedSelectionQuery;
use Thelia\Api\Bridge\Propel\Filter\AbstractFilter;
use Selection\Model\Map\SelectionContainerAssociatedSelectionTableMap;

class ContainerBySelectionFilter extends AbstractFilter
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
        if ($property !== "selection_id") {
            return;
        }

        $containerIds = SelectionContainerAssociatedSelectionQuery::create()
            ->filterBySelectionId(explode(',', $value))
            ->select(SelectionContainerAssociatedSelectionTableMap::SELECTION_CONTAINER_ID)
            ->find()
            ->toArray();

        $query->filterById($containerIds);
    }

    public function getDescription(string $resourceClass): array
    {
        return [
            'selection_id' => [
                'type' => 'array',
                'required' => false,
                'description' => 'Filters the containers to return only those linked to the provided selection_id(s).',
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
