# Selection

This module allows you to create a selection of products or contents of similar themes
(Best sellers, Best rated by women, .. for example). The selection will then be displayed as  list
of those products or contents.

## Compatibility
* To use this module on Thelia 2.3.x, use the tag 1.1.9

## Installation

### Manually

* Copy the module into ```<thelia_root>/local/modules/``` directory and be sure that the name of the module is Selection.
* Activate it in your thelia administration panel

### Composer

Add it in your main thelia composer.json file

```
composer require thelia/selection-module:~1.1.11
```

## Usage

Once activated, a new button called "Selection" will appear in the tool menu on the left sidebar of the admin panel.
Clicking on it will redirect you to the list of all the selections you've created so far.

Once on the page with all your selections you may :

- Create a new selection by clicking on the + button at the top right of the page.
- Toggle the visibility of your selection (whether people will see it or not) by clicking on the "Online" button in
front of the selection you wish to make visible or invisible.
- Edit an already created selection by clicking on its name or on the cog button then on the pencil button in front
of the selection you wish to edit.
- Delete a selection by clicking on the cog button then on the trash button in front of the selection you wish to delete.

You may then display your selections on your website through the front API (`/api/front/selections`).

## Hook

This module has a single hook in the back office, adding the Selection button to the tools menu of the sidebar on
the left, redirecting to the list of selection.
