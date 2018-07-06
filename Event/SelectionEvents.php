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
    const BEFORE_CREATE_SELECTION                 = 'action.selection.before.create';
    const AFTER_CREATE_SELECTION                  = 'action.selection.after.create';
    const SELECTION_CREATE                        = 'action.selection.create';

    const BEFORE_UPDATE_SELECTION                 = 'action.selection.before.update';
    const AFTER_UPDATE_SELECTION                  = 'action.selection.after.update';
    const SELECTION_UPDATE                        = 'action.selection.update';

    const BEFORE_DELETE_SELECTION            = 'action.selection.before.delete';
    const AFTER_DELETE_SELECTION            = 'action.selection.after.delete';
    const SELECTION_DELETE                  = 'action.selection.delete';

    const SELECTION_UPDATE_SEO              = 'action.selection.updateSeo';

    const SELECTION_UPDATE_POSITION         = 'action.selection.updatePosition'; // Update a related product position in a selection

    const SELECTION_TOGGLE_VISIBILITY       = 'action.toggleSelectionVisibility';

    const RELATED_PRODUCT_UPDATE_POSITION   = 'action.selection.relatedProduct.updatePosition';
}
