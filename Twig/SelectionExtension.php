<?php

declare(strict_types=1);

namespace Selection\Twig;

use Thelia\Api\Service\DataAccess\DataAccessService;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

final class SelectionExtension extends AbstractExtension
{
    public function __construct(
        private readonly DataAccessService $dataAccessService,
    ) {
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('getSelection', [$this, 'getSelection']),
            new TwigFunction('getSelectionContainer', [$this, 'getSelectionContainer'])
        ];
    }

    /**
     * Fetch selections from the Thelia API front endpoint.
     *
     * Accepts the same filters as GET /api/front/selections:
     *   getSelection({code: 'HOME'})
     *   getSelection({container_code: 'MAIN'})
     *   getSelection({id: 1})
     *
     * @param array<string, mixed> $params
     */
    public function getSelection(array $params = []): array|object|null
    {
        return $this->dataAccessService->resources('/api/front/selections', $params);
    }

    public function getSelectionContainer(array $params = []): array|object|null
    {
        return $this->dataAccessService->resources('/api/front/selection-containers', $params);
    }
}
