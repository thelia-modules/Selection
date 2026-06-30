<?php

namespace Selection\Controller;

use Propel\Runtime\ActiveQuery\Criteria;
use Propel\Runtime\ActiveQuery\Join;
use Selection\Model\Map\SelectionContentTableMap;
use Selection\Model\SelectionContent;
use Selection\Model\SelectionContentQuery;
use Selection\Selection;
use Symfony\Component\HttpFoundation\Response;
use Thelia\Controller\Admin\BaseAdminController;
use Thelia\Core\HttpFoundation\Request;
use Thelia\Model\Content;
use Thelia\Model\ContentFolder;
use Thelia\Model\ContentFolderQuery;
use Thelia\Model\ContentQuery;
use Thelia\Model\Lang;
use Thelia\Model\Map\ContentTableMap;
use Twig\Environment;

class SelectionRelatedContentController extends BaseAdminController
{
    protected string $currentRouter = Selection::ROUTER;

    public function __construct(private readonly Environment $twig)
    {
    }

    /**
     * Reproduce the selection_content_related loop in PHP, ordered by position.
     */
    private function getRelatedContentRows($selectionID, string $locale): array
    {
        $rows = [];
        $related = SelectionContentQuery::create()
            ->filterBySelectionId($selectionID)
            ->orderByPosition(Criteria::ASC)
            ->find();

        foreach ($related as $item) {
            $content = ContentQuery::create()->findPk($item->getContentId());
            if (null === $content) {
                continue;
            }
            $rows[] = [
                'id' => $item->getContentId(),
                'title' => $content->setLocale($locale)->getTitle(),
                'position' => $item->getPosition(),
            ];
        }

        return $rows;
    }

    private function renderRelatedContents($selectionID, string $locale): Response
    {
        return new Response($this->twig->render(
            '@SelectionModule/backOffice/default-twig/related/contentRelated.html.twig',
            [
                'selection_id' => $selectionID,
                'locale' => $locale,
                'rows' => $this->getRelatedContentRows($selectionID, $locale),
            ]
        ));
    }

    /**
     * Return content id & title
     *
     * @return \Thelia\Core\HttpFoundation\Response
     */
    public function getContentRelated(Request $request)
    {
        $folderId = $request->attributes->get('folderID', $request->query->get('folderID', $request->request->get('folderID')));

        $contentCategory = ContentFolderQuery::create();
        $lang = $request->hasSession() ? $request->getSession()->get('thelia.current.lang') : Lang::getDefaultLanguage();

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
        $contentId = $request->attributes->get('contentID', $request->query->get('contentID', $request->request->get('contentID')));
        $selectionID = $request->attributes->get('selectionID', $request->query->get('selectionID', $request->request->get('selectionID')));

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
                ContentTableMap::COL_ID,
                SelectionContentTableMap::COL_CONTENT_ID,
                Criteria::INNER_JOIN
            );

            $search->addJoinObject($selectionContentRelated, 'selectionContentRelated');
            $search->addJoinCondition(
                'selectionContentRelated',
                SelectionContentTableMap::COL_SELECTION_ID.'='.$selectionID
            );
            $search->find();
        }
        return $this->renderRelatedContents($selectionID, $this->getCurrentEditionLocale());
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
        $selectionID = $request->attributes->get('selectionID', $request->query->get('selectionID', $request->request->get('selectionID')));
        $lang = $request->hasSession() ? $request->getSession()->get('thelia.current.lang') : Lang::getDefaultLanguage();

        $search = ContentQuery::create();
        $selectionContentRelated = new Join(
            ContentTableMap::COL_ID,
            SelectionContentTableMap::COL_CONTENT_ID,
            Criteria::INNER_JOIN
        );

        $search->addJoinObject($selectionContentRelated, 'selectionContentRelated');
        $search->addJoinCondition(
            'selectionContentRelated',
            SelectionContentTableMap::COL_SELECTION_ID.'='.$selectionID
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
            return $this->renderRelatedContents($selectionID, $lang->getLocale());
        } else {
            return $result;
        }
    }
}
