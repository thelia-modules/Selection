<?php
/**
 * Created by PhpStorm.
 * User: mbruchet
 * Date: 21/03/2018
 * Time: 09:20
 */

namespace Selection\Event;

class SelectionEvents
{
    const SELECTION_CREATE      = 'action.selection.create';

    const SELECTION_UPDATE      = 'action.selection.update';

    const SELECTION_DELETE      = 'action.selection.delete';

    const SELECTION_UPDATE_SEO  = 'action.selection.updateSeo';

    const SELECTION_TOGGLE_VISIBILITY  = 'action.toggleSelectionVisibility';
}
