<?php

declare(strict_types=1);

namespace Selection\Api\Extension;

use ApiPlatform\Metadata\Operation;
use Propel\Runtime\ActiveQuery\ModelCriteria;
use Selection\Api\Resource\Selection;
use Symfony\Component\HttpFoundation\RequestStack;
use Thelia\Api\Bridge\Propel\Extension\QueryCollectionExtensionInterface;

/**
 * Adds a `container_code` query parameter on the selection collection,
 * filtering selections by the code of the container they belong to.
 *
 * GET /api/front/selections?container_code=MAIN
 */
final readonly class SelectionContainerCodeExtension implements QueryCollectionExtensionInterface
{
    public function __construct(
        private RequestStack $requestStack,
    ) {
    }

    public function applyToCollection(
        ModelCriteria $query,
        string $resourceClass,
        ?Operation $operation = null,
        array $context = [],
    ): void {
        if (Selection::class !== $resourceClass) {
            return;
        }

        $containerCode = $this->requestStack->getCurrentRequest()?->query->get('container_code');

        if (null === $containerCode || '' === $containerCode) {
            return;
        }

        $query
            ->useSelectionContainerAssociatedSelectionQuery()
                ->useSelectionContainerQuery()
                    ->filterByCode($containerCode)
                ->endUse()
            ->endUse();
    }
}
