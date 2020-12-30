<?php
/**
 * Created by PhpStorm.
 * User: audreymartel
 * Date: 09/07/2018
 * Time: 12:39
 */

namespace Selection\Loop;

use Propel\Runtime\ActiveQuery\Criteria;
use Propel\Runtime\Exception\PropelException;
use Selection\Model\Map\SelectionContainerAssociatedSelectionTableMap;
use Selection\Model\SelectionContainer;
use Selection\Model\SelectionContainerAssociatedSelectionQuery;
use Selection\Model\SelectionContainerQuery;
use Selection\Model\SelectionI18nQuery;
use Thelia\Core\Template\Element\BaseI18nLoop;
use Thelia\Core\Template\Element\LoopResult;
use Thelia\Core\Template\Element\LoopResultRow;
use Thelia\Core\Template\Element\PropelSearchLoopInterface;
use Thelia\Core\Template\Loop\Argument\Argument;
use Thelia\Core\Template\Loop\Argument\ArgumentCollection;
use Thelia\Log\Tlog;
use Thelia\Type;
use Thelia\Type\BooleanOrBothType;
use Thelia\Type\TypeCollection;

/**
 * Class SelectionContainerLoop
 * @package Selection\Loop
 * @method int[] getExclude()
 * @method int[] getId()
 * @method int getSelectionId()
 * @method string[] getExcludeCode()
 * @method string[] getCode()
 * @method string getTitle()
 * @method int[] getPosition()
 * @method bool|string getVisible()

 */
class SelectionContainerLoop extends BaseI18nLoop implements PropelSearchLoopInterface
{
    /***
     * @return ArgumentCollection
     */
    protected function getArgDefinitions()
    {
        return new ArgumentCollection(
            Argument::createIntListTypeArgument('id'),
            Argument::createAnyListTypeArgument('code'),
            Argument::createAnyListTypeArgument('exclude_code'),
            Argument::createIntTypeArgument('selection_id'),
            Argument::createBooleanTypeArgument('need_selection_count'),
            Argument::createBooleanOrBothTypeArgument('visible', true),
            Argument::createAnyTypeArgument('title'),
            Argument::createIntListTypeArgument('position'),
            Argument::createIntListTypeArgument('exclude'),
            new Argument(
                'order',
                new TypeCollection(
                    new Type\EnumListType(array(
                        'id', 'id_reverse',
                        'code', 'code_reverse',
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

    public function buildModelCriteria()
    {
        $search = SelectionContainerQuery::create();

        /* manage translations */
        $this->configureI18nProcessing($search, array('TITLE', 'CHAPO', 'DESCRIPTION', 'POSTSCRIPTUM', 'META_TITLE', 'META_DESCRIPTION'));

        if (null !== $code = $this->getCode()) {
            $search->filterByCode($code, Criteria::IN);
        }

        if (null !== $excludeCode = $this->getExcludeCode()) {
            $search->filterByCode($excludeCode, Criteria::NOT_IN);
        }

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
            try {
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
            } catch (PropelException $e) {
                Tlog::getInstance()->error($e->getMessage());
            }
        }

        $visible = $this->getVisible();
        if (BooleanOrBothType::ANY !== $visible) {
            $search->filterByVisible($visible ? 1 : 0);
        }

        if (null !== $selectionId = $this->getSelectionId()) {
            $search->innerJoinSelectionContainerAssociatedSelection(SelectionContainerAssociatedSelectionTableMap::TABLE_NAME);
            $search->where(SelectionContainerAssociatedSelectionTableMap::SELECTION_ID . Criteria::EQUAL . $selectionId);
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
                case "code":
                    $search->orderByCode(Criteria::ASC);
                    break;
                case "code_reverse":
                    $search->orderByCode(Criteria::DESC);
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
     */
    public function parseResults(LoopResult $loopResult)
    {
        /** @noinspection PhpUndefinedMethodInspection */
        $needSelectionCount = $this->getNeedSelectionCount() === null || !$this->getNeedSelectionCount();
        /** @var SelectionContainer $selectionContainer */
        foreach ($loopResult->getResultDataCollection() as $selectionContainer) {
            $loopResultRow = new LoopResultRow($selectionContainer);

            /** @noinspection PhpUndefinedMethodInspection */
            $loopResultRow
                ->set("SELECTION_CONTAINER_ID", $selectionContainer->getId())
                ->set("SELECTION_CONTAINER_URL", $this->getReturnUrl() ? $selectionContainer->getUrl($this->locale) : null)
                ->set("SELECTION_CONTAINER_CODE", $selectionContainer->getCode())
                ->set("SELECTION_CONTAINER_TITLE", $selectionContainer->geti18n_TITLE())
                ->set("SELECTION_CONTAINER_META_TITLE", $selectionContainer->geti18n_META_TITLE())
                ->set("SELECTION_CONTAINER_POSITION", $selectionContainer->getPosition())
                ->set("SELECTION_CONTAINER_VISIBLE", $selectionContainer->getVisible())
                ->set("SELECTION_CONTAINER_DESCRIPTION", $selectionContainer->geti18n_DESCRIPTION())
                ->set("SELECTION_CONTAINER_META_DESCRIPTION", $selectionContainer->geti18n_META_DESCRIPTION())
                ->set("SELECTION_CONTAINER_POSTSCRIPTUM", $selectionContainer->geti18n_POSTSCRIPTUM())
                ->set("SELECTION_CONTAINER_CHAPO", $selectionContainer->geti18n_CHAPO());

            if ($needSelectionCount) {
                $associatedSelectionsQuery = SelectionContainerAssociatedSelectionQuery::create();
                $associatedSelectionsQuery->filterBySelectionContainerId($selectionContainer->getId());
                $childCount = $associatedSelectionsQuery->find()->count();
                $loopResultRow->set("SELECTION_COUNT", $childCount);
            }

            $loopResult->addRow($loopResultRow);
        }
        return $loopResult;
    }
}
