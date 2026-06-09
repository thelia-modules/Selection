<?php

namespace Selection\Controller;

use Propel\Runtime\ActiveQuery\Criteria;
use Selection\Model\Map\SelectionContainerAssociatedSelectionTableMap;
use Selection\Model\SelectionContainerAssociatedSelectionQuery;
use Selection\Model\SelectionContainerQuery;
use Selection\Model\SelectionQuery;
use Symfony\Component\HttpFoundation\Response;
use Thelia\Controller\Admin\BaseAdminController;
use Thelia\Core\Event\UpdatePositionEvent;
use Thelia\Core\HttpFoundation\Request;
use Twig\Environment;

class SelectionController extends BaseAdminController
{
    public function __construct(private readonly Environment $twig)
    {
    }

    /**
     * Show the default template: selection list (containers + selections).
     */
    public function viewAction(Request $request): Response
    {
        $locale = $this->getCurrentEditionLocale();

        return new Response($this->twig->render(
            '@SelectionModule/backOffice/default-twig/selection-list.html.twig',
            [
                'selection_order' => $this->getAttributeSelectionOrder(),
                'selection_container_order' => $this->getAttributeContainerOrder(),
                'containers' => $this->getContainerRows($locale),
                'selections' => $this->getSelectionRows($locale, null),
                'selected_container_id' => null,
            ]
        ));
    }

    /**
     * Reproduce the selection_container loop in PHP (ordered by position).
     */
    public function getContainerRows(string $locale): array
    {
        $rows = [];
        $containers = SelectionContainerQuery::create()->orderByPosition(Criteria::ASC)->find();

        foreach ($containers as $container) {
            $container->setLocale($locale);
            $rows[] = [
                'id' => $container->getId(),
                'code' => $container->getCode(),
                'title' => $container->getTitle(),
                'position' => $container->getPosition(),
                'visible' => $container->getVisible(),
            ];
        }

        return $rows;
    }

    /**
     * Reproduce the selection_list loop in PHP for a given container (or those without container).
     */
    public function getSelectionRows(string $locale, ?int $containerId): array
    {
        $rows = [];
        $search = SelectionQuery::create()->orderByPosition(Criteria::ASC);
        $search->leftJoinSelectionContainerAssociatedSelection(SelectionContainerAssociatedSelectionTableMap::TABLE_NAME);

        if (null !== $containerId) {
            $search->where(SelectionContainerAssociatedSelectionTableMap::COL_SELECTION_CONTAINER_ID . Criteria::EQUAL . $containerId);
        } else {
            $search->where(SelectionContainerAssociatedSelectionTableMap::COL_SELECTION_ID . Criteria::ISNULL);
        }

        foreach ($search->find() as $selection) {
            $selection->setLocale($locale);
            $rows[] = [
                'id' => $selection->getId(),
                'code' => $selection->getCode(),
                'title' => $selection->getTitle(),
                'position' => $selection->getPosition(),
                'visible' => $selection->getVisible(),
            ];
        }

        return $rows;
    }

    private function getAttributeSelectionOrder()
    {
        return $this->getListOrderFromSession(
            'selection',
            'selection_order',
            'manual'
        );
    }

    private function getAttributeContainerOrder()
    {
        return $this->getListOrderFromSession(
            'selectioncontainer',
            'selection_container_order',
            'manual'
        );
    }

    protected function createUpdatePositionEvent(Request $request, $positionChangeMode, $positionValue)
    {
        return new UpdatePositionEvent(
            $request->query->get('selection_id', $request->request->get('selection_id')),
            $positionChangeMode,
            $positionValue
        );
    }
}
