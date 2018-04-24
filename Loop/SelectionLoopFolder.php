<?php

namespace Selection\Loop;


use Thelia\Core\Template\Element\BaseLoop;
use Thelia\Core\Template\Element\LoopResult;
use Thelia\Core\Template\Element\LoopResultRow;
use Thelia\Core\Template\Element\PropelSearchLoopInterface;
use Thelia\Core\Template\Loop\Argument\Argument;
use Thelia\Core\Template\Loop\Argument\ArgumentCollection;
use Thelia\Model\Folder;
use Thelia\Model\FolderI18nQuery;
use Thelia\Model\FolderQuery;

class SelectionLoopFolder extends BaseLoop implements PropelSearchLoopInterface
{
    /**
     * @return ArgumentCollection
     */
    protected function getArgDefinitions()
    {
        return new ArgumentCollection(
            Argument::createIntListTypeArgument('folder_id'),
            Argument::createAnyTypeArgument('folder_title')
        );
    }

    /**
     * this method returns a Propel ModelCriteria
     *
     * @return \Propel\Runtime\ActiveQuery\ModelCriteria
     */
    public function buildModelCriteria()
    {
        $search = FolderQuery::create();

        if (null !== $folder_id = $this->getFolderId()) {
            $search->filterById();
        }

        if (null !== $folder_title = $this->getFolderTitle()) {
            $title = FolderI18nQuery::create();
            $title->filterByTitle();
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
        foreach ($loopResult->getResultDataCollection() as $folder) {

            /** @var Folder $folder */
            $loopResultRow = new LoopResultRow($folder);
            $lang = $this->request->getSession()->get('thelia.current.lang');
            $loopResultRow
                ->set('folder_id', $folder->getId())
                ->set('folder_title', $folder->getTranslation($lang->getLocale())->getTitle());

            $loopResult->addRow($loopResultRow);
        }

        return $loopResult;
    }
}
