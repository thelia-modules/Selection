<?php

namespace Selection\Loop;

use Propel\Runtime\ActiveQuery\Criteria;
use Propel\Runtime\Exception\PropelException;
use Selection\Model\Map\SelectionContainerAssociatedSelectionTableMap;
use Selection\Model\Selection;
use Selection\Model\SelectionI18nQuery;
use Selection\Model\SelectionQuery;
use Thelia\Core\Template\Element\BaseI18nLoop;
use Thelia\Core\Template\Element\LoopResult;
use Thelia\Core\Template\Element\LoopResultRow;
use Thelia\Core\Template\Element\PropelSearchLoopInterface;
use Thelia\Core\Template\Loop\Argument\Argument;
use Thelia\Core\Template\Loop\Argument\ArgumentCollection;
use Thelia\Type;
use Thelia\Type\BooleanOrBothType;
use Thelia\Type\TypeCollection;

/**
 * Class SelectionLoop
 *
 * @package Thelia\Core\Template\Loop
 *
 * {@inheritdoc}
 * @method int[] getExclude()
 * @method int[] getId()
 * @method string getTitle()
 * @method int[] getPosition()
 * @method bool|string getVisible()
 */
class SelectionLoop extends BaseI18nLoop implements PropelSearchLoopInterface
{
    public $countable = true;
    public $timestampable = false;
    public $versionable = false;

    /***
     * @return ArgumentCollection
     */
    protected function getArgDefinitions()
    {
        return new ArgumentCollection(
            Argument::createIntListTypeArgument('id'),
            Argument::createIntTypeArgument('container_id'),
            Argument::createBooleanTypeArgument('without_container'),
            Argument::createBooleanOrBothTypeArgument('visible', true),
            Argument::createAnyTypeArgument('title'),
            Argument::createIntListTypeArgument('position'),
            Argument::createIntListTypeArgument('exclude'),
            new Argument(
                'order',
                new TypeCollection(
                    new Type\EnumListType(array(
                        'id', 'id_reverse',
                        'alpha', 'alpha_reverse',
                        'manual', 'manual_reverse',
                        'visible', 'visible_reverse',
                        'created', 'created_reverse',
                        'updated', 'updated_reverse',
                        'random'
                        ))
                ),
                'manual'
            )
        );
    }

    /**
     * @return \Propel\Runtime\ActiveQuery\ModelCriteria|SelectionQuery
     * @throws \Propel\Runtime\Exception\PropelException
     */
    public function buildModelCriteria()
    {
        $search = SelectionQuery::create();

        /* manage translations */
        $this->configureI18nProcessing($search, array('TITLE', 'CHAPO', 'DESCRIPTION', 'POSTSCRIPTUM', 'META_TITLE', 'META_DESCRIPTION'));

        if (null !== $exclude = $this->getExclude()) {
            $search->filterById($exclude, Criteria::NOT_IN);
        }

        if (null !== $id = $this->getId()) {
            $search->filterById($id, Criteria::IN);
        }

        if (null !== $position = $this->getPosition()) {
            $search->filterByPosition($position, Criteria::IN);
        }

        if (null !== $title = $this->getTitle()) {
            //find all selections that match exactly this title and find with all locales.
            $search2 = SelectionI18nQuery::create()
                ->filterByTitle($title, Criteria::LIKE)
                ->select('id')
                ->find();

            if ($search2) {
                $search->filterById(
                    $search2,
                    Criteria::IN
                );
            }
        }

        $visible = $this->getVisible();
        if (BooleanOrBothType::ANY !== $visible) {
            $search->filterByVisible($visible ? 1 : 0);
        }


        $search->leftJoinSelectionContainerAssociatedSelection(SelectionContainerAssociatedSelectionTableMap::TABLE_NAME);
        /** @noinspection PhpUndefinedMethodInspection */
        $wantedContainerId = $this->getContainerId();
        /** @noinspection PhpUndefinedMethodInspection */
        $withoutContainer = $this->getWithoutContainer();
        if (null !== $wantedContainerId) {
            $search->leftJoinSelectionContainerAssociatedSelection(SelectionContainerAssociatedSelectionTableMap::TABLE_NAME);
            $search->where(SelectionContainerAssociatedSelectionTableMap::SELECTION_CONTAINER_ID . Criteria::EQUAL . $wantedContainerId);
        } else if (null !== $withoutContainer && $withoutContainer) {
            $search->leftJoinSelectionContainerAssociatedSelection(SelectionContainerAssociatedSelectionTableMap::TABLE_NAME);
            $search->where(SelectionContainerAssociatedSelectionTableMap::SELECTION_ID . Criteria::ISNULL);
        }

        /** @noinspection PhpUndefinedMethodInspection */
        $orders  = $this->getOrder();

        foreach ($orders as $order) {
            switch ($order) {
                case "id":
                    $search->orderById(Criteria::ASC);
                    break;
                case "id_reverse":
                    $search->orderById(Criteria::DESC);
                    break;
                case "alpha":
                    $search->addAscendingOrderByColumn('i18n_TITLE');
                    break;
                case "alpha_reverse":
                    $search->addDescendingOrderByColumn('i18n_TITLE');
                    break;
                case "manual":
                    $search->orderByPosition(Criteria::ASC);
                    break;
                case "manual_reverse":
                    $search->orderByPosition(Criteria::DESC);
                    break;
                case "visible":
                    $search->orderByVisible(Criteria::ASC);
                    break;
                case "visible_reverse":
                    $search->orderByVisible(Criteria::DESC);
                    break;
                case "created":
                    $search->addAscendingOrderByColumn('created_at');
                    break;
                case "created_reverse":
                    $search->addDescendingOrderByColumn('created_at');
                    break;
                case "updated":
                    $search->addAscendingOrderByColumn('updated_at');
                    break;
                case "updated_reverse":
                    $search->addDescendingOrderByColumn('updated_at');
                    break;
                case "random":
                    $search->clearOrderByColumns();
                    $search->addAscendingOrderByColumn('RAND()');
                    break;
                default:
                    $search->orderByPosition(Criteria::ASC);
            }
        }

        return $search;
    }

    /**
     * @param LoopResult $loopResult
     *
     * @return LoopResult
     * @throws PropelException
     */
    public function parseResults(LoopResult $loopResult)
    {
        foreach ($loopResult->getResultDataCollection() as $selection) {

            /** @var Selection $selection */
            $loopResultRow = new LoopResultRow($selection);
            /** @noinspection PhpUndefinedMethodInspection */
            $loopResultRow
                ->set("SELECTION_ID", $selection->getId())
                ->set("SELECTION_URL", $this->getReturnUrl() ? $selection->getUrl($this->locale) : null)
                ->set("SELECTION_TITLE", $selection->geti18n_TITLE())
                ->set("SELECTION_META_TITLE", $selection->geti18n_META_TITLE())
                ->set("SELECTION_POSITION", $selection->getPosition())
                ->set("SELECTION_VISIBLE", $selection->getVisible())
                ->set("SELECTION_DESCRIPTION", $selection->geti18n_DESCRIPTION())
                ->set("SELECTION_META_DESCRIPTION", $selection->geti18n_META_DESCRIPTION())
                ->set("SELECTION_POSTSCRIPTUM", $selection->geti18n_POSTSCRIPTUM())
                ->set("SELECTION_CHAPO", $selection->geti18n_CHAPO())
                ->set("SELECTION_CONTAINER_ID", $selection->getSelectionContainerAssociatedSelections()
                );

            $loopResult->addRow($loopResultRow);
        }
        return $loopResult;
    }
}
