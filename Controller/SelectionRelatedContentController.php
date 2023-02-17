<?php

namespace Selection\Controller;

use Propel\Runtime\ActiveQuery\Criteria;
use Propel\Runtime\ActiveQuery\Join;
use Selection\Model\Map\SelectionContentTableMap;
use Selection\Model\SelectionContent;
use Selection\Model\SelectionContentQuery;
use Selection\Selection;
use Thelia\Controller\Admin\BaseAdminController;
use Thelia\Core\HttpFoundation\Request;
use Thelia\Model\Content;
use Thelia\Model\ContentFolder;
use Thelia\Model\ContentFolderQuery;
use Thelia\Model\ContentQuery;
use Thelia\Model\Map\ContentTableMap;

class SelectionRelatedContentController extends BaseAdminController
{
    protected $currentRouter = Selection::ROUTER;

    /**
     * Return content id & title
     *
     * @return \Thelia\Core\HttpFoundation\Response
     */
    public function getContentRelated(Request $request)
    {
        $folderId = $request->get('folderID');

        $contentCategory = ContentFolderQuery::create();
        $lang = $request->getSession()->get('thelia.current.lang');

        $result = array();

        if ($folderId !== null) {
            $contentCategory->filterByFolderId($folderId)->find();

            if ($contentCategory !== null) {
                /** @var ContentFolder $item */
                foreach ($contentCategory as $item) {
                    $content = ContentQuery::create()
                        ->filterById($item->getContentId())
                        ->findOne();

                    $result[] =
                        [
                            'id' => $content->getId(),
                            'title' => $content->getTranslation($lang->getLocale())->getTitle()
                        ];
                }
            }
        }
        return $this->jsonResponse(json_encode($result));
    }

    /**
     * Add content to current selection
     *
     * @return \Thelia\Core\HttpFoundation\Response
     * @throws \Propel\Runtime\Exception\PropelException
     */
    public function addContentRelated(Request $request)
    {
        $contentId = $request->get('contentID');
        $selectionID = $request->get('selectionID');

        $contentRelated = new SelectionContent();

        if ($contentId !== null) {
            $SelectionContent = SelectionContentQuery::create()
                ->filterBySelectionId($selectionID)
                ->filterByContentId($contentId)
                ->findOne();

            if (is_null($SelectionContent)) {
                $contentRelated->setSelectionId($selectionID);
                $contentRelated->setContentId($contentId);

                $position = SelectionContentQuery::create()
                    ->filterBySelectionId($selectionID)
                    ->orderByPosition(Criteria::DESC)
                    ->select('position')
                    ->findOne();
                if (null === $position) {
                    $contentRelated->setPosition(1);
                } else {
                    $contentRelated->setPosition($position+1);
                }
                $contentRelated->save();
            }

            $search = ContentQuery::create();
            $selectionContentRelated = new Join(
                ContentTableMap::ID,
                SelectionContentTableMap::CONTENT_ID,
                Criteria::INNER_JOIN
            );

            $search->addJoinObject($selectionContentRelated, 'selectionContentRelated');
            $search->addJoinCondition(
                'selectionContentRelated',
                SelectionContentTableMap::SELECTION_ID.'='.$selectionID
            );
            $search->find();
        }
        return $this->render('related/contentRelated', ['selection_id' => $selectionID]);
    }

    /**
     * Show content related to a selection
     *
     * @param null $p
     * @return array|\Thelia\Core\HttpFoundation\Response
     * @throws \Propel\Runtime\Exception\PropelException
     */
    public function showContent(Request $request, $p = null)
    {
        $selectionID = $request->get('selectionID');
        $lang = $request->getSession()->get('thelia.current.lang');

        $search = ContentQuery::create();
        $selectionContentRelated = new Join(
            ContentTableMap::ID,
            SelectionContentTableMap::CONTENT_ID,
            Criteria::INNER_JOIN
        );

        $search->addJoinObject($selectionContentRelated, 'selectionContentRelated');
        $search->addJoinCondition(
            'selectionContentRelated',
            SelectionContentTableMap::SELECTION_ID.'='.$selectionID
        );
        $search->find();

        /** @var Content $row */
        foreach ($search as $row) {
            $selectionContentPos = SelectionContentQuery::create()
                ->filterBySelectionId($selectionID)
                ->filterByContentId($row->getId())
                ->findOne();

            $result = [
                'id' => $row->getId() ,
                'title' => $row->getTranslation($lang->getLocale())->getTitle(),
                'position' => $selectionContentPos->getPosition()
            ];
        }

        if ($p === null) {
            return $this->render('related/contentRelated', ['selection_id' => $selectionID]);
        } else {
            return $result;
        }
    }
}
