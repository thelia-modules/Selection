<?php
/**
 * Created by PhpStorm.
 * User: tpradatos
 * Date: 05/04/2018
 * Time: 12:54
 */

namespace Selection\Event;

class SelectionFolderEvents
{
    const SELECTION_FOLDER_CREATE      = 'action.selection.folder.create';

    const SELECTION_FOLDER_UPDATE      = 'action.selection.folder.update';

    const SELECTION_FOLDER_DELETE      = 'action.selection.folder.delete';

    const SELECTION_FOLDER_UPDATE_SEO  = 'action.selection.folder.updateSeo';

    const SELECTION_FOLDER_TOGGLE_VISIBILITY  = 'action.folder.toggleSelectionVisibility';
}