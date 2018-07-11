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
    const BEFORE_CREATE_SELECTION = 'action.selection.before.create';
    const AFTER_CREATE_SELECTION = 'action.selection.after.create';
    const SELECTION_CREATE = 'action.selection.create';

    const BEFORE_UPDATE_SELECTION = 'action.selection.before.update';
    const AFTER_UPDATE_SELECTION = 'action.selection.after.update';
    const SELECTION_UPDATE = 'action.selection.update';

    const BEFORE_DELETE_SELECTION = 'action.selection.before.delete';
    const AFTER_DELETE_SELECTION = 'action.selection.after.delete';
    const SELECTION_DELETE = 'action.selection.delete';

    const SELECTION_UPDATE_SEO = 'action.selection.update.seo';
    const SELECTION_TOGGLE_VISIBILITY = 'action.toggle.selection.visibility';
    const SELECTION_UPDATE_POSITION = 'action.selection.update.position';
    const RELATED_PRODUCT_UPDATE_POSITION = 'action.selection.relatedProduct.update.position';

    //CONTAINER EVENTS

    const SELECTION_CONTAINER_CREATE = 'action.selection.container.create';
    const SELECTION_CONTAINER_DELETE = 'action.selection.container.delete';
    const SELECTION_CONTAINER_UPDATE = 'action.selection.container.update';
    const SELECTION_CONTAINER_UPDATE_POSITION = 'action.selection.container.update.position';
    const SELECTION_CONTAINER_UPDATE_SEO = 'action.selection.container.update.seo';
    const SELECTION_CONTAINER_TOGGLE_VISIBILITY = 'action.selection.container.visibility';

    const BEFORE_CREATE_SELECTION_CONTAINER = 'action.selection.container.before.create';
    const AFTER_CREATE_SELECTION_CONTAINER = 'action.selection.container.after.create';
    const BEFORE_UPDATE_SELECTION_CONTAINER = 'action.selection.container.before.update';
    const AFTER_UPDATE_SELECTION_CONTAINER = 'action.selection.container.after.update';
    const BEFORE_DELETE_SELECTION_CONTAINER = 'action.selection.container.before.delete';
    const AFTER_DELETE_SELECTION_CONTAINER = 'action.selection.container.after.delete';


}
