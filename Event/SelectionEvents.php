<?php

namespace Selection\Event;

class SelectionEvents
{
    public const BEFORE_CREATE_SELECTION = 'action.selection.before.create';
    public const AFTER_CREATE_SELECTION = 'action.selection.after.create';
    public const SELECTION_CREATE = 'action.selection.create';

    public const BEFORE_UPDATE_SELECTION = 'action.selection.before.update';
    public const AFTER_UPDATE_SELECTION = 'action.selection.after.update';
    public const SELECTION_UPDATE = 'action.selection.update';

    public const BEFORE_DELETE_SELECTION = 'action.selection.before.delete';
    public const AFTER_DELETE_SELECTION = 'action.selection.after.delete';
    public const SELECTION_DELETE = 'action.selection.delete';

    public const SELECTION_UPDATE_SEO = 'action.selection.update.seo';
    public const SELECTION_TOGGLE_VISIBILITY = 'action.toggle.selection.visibility';
    public const SELECTION_UPDATE_POSITION = 'action.selection.update.position';
    public const RELATED_PRODUCT_UPDATE_POSITION = 'action.selection.relatedProduct.update.position';

    //CONTAINER EVENTS

    public const SELECTION_CONTAINER_CREATE = 'action.selection.container.create';
    public const SELECTION_CONTAINER_DELETE = 'action.selection.container.delete';
    public const SELECTION_CONTAINER_UPDATE = 'action.selection.container.update';
    public const SELECTION_CONTAINER_UPDATE_POSITION = 'action.selection.container.update.position';
    public const SELECTION_CONTAINER_UPDATE_SEO = 'action.selection.container.update.seo';
    public const SELECTION_CONTAINER_TOGGLE_VISIBILITY = 'action.selection.container.visibility';

    public const BEFORE_CREATE_SELECTION_CONTAINER = 'action.selection.container.before.create';
    public const AFTER_CREATE_SELECTION_CONTAINER = 'action.selection.container.after.create';
    public const BEFORE_UPDATE_SELECTION_CONTAINER = 'action.selection.container.before.update';
    public const AFTER_UPDATE_SELECTION_CONTAINER = 'action.selection.container.after.update';
    public const BEFORE_DELETE_SELECTION_CONTAINER = 'action.selection.container.before.delete';
    public const AFTER_DELETE_SELECTION_CONTAINER = 'action.selection.container.after.delete';


}
