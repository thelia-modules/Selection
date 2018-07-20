<?php

namespace Selection\Model;

use Selection\Model\Base\SelectionContainerQuery as BaseSelectionContainerQuery;
use Thelia\Log\Tlog;
use Thelia\Model\Lang;
use Thelia\Model\Tools\ModelCriteriaTools;


/**
 * Skeleton subclass for performing query and update operations on the 'selection_container' table.
 *
 *
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 */
class SelectionContainerQuery extends BaseSelectionContainerQuery
{
    /**
     * @param $lang Lang
     * @return SelectionContainer[]
     */
    public static function getAll($lang)
    {
        try {
            $containerQuery = SelectionContainerQuery::create();
            /* manage translations */
            ModelCriteriaTools::getI18n(
                false,
                $lang->getId(),
                $containerQuery,
                $lang->getLocale(),
                array('TITLE', 'CHAPO', 'DESCRIPTION', 'POSTSCRIPTUM', 'META_TITLE', 'META_DESCRIPTION', 'META_KEYWORDS'),
                null,
                'ID',
                false
            );
            $containers = $containerQuery->find();
            if (empty($containers)) {
                return [];
            }
            return $containers;
        } catch (\Exception $e) {
            Tlog::getInstance()->error($e->getMessage());
        }
        return [];
    }
} // SelectionContainerQuery
