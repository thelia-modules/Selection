# Selection

This module allows you to create a selection of products or contents of similar themes 
(Best sellers, Best rated by women, .. for example). The selection will then be displayed as  list
of those products or contents. 

## Compatibility 
* To use this module on Thelia 2.3.x, use the tag 1.1.2

## Installation

### Manually

* Copy the module into ```<thelia_root>/local/modules/``` directory and be sure that the name of the module is Selection.
* Activate it in your thelia administration panel

### Composer

Add it in your main thelia composer.json file

```
composer require thelia/selection-module:~1.1.2
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

You may then display your selection on your website by calling the selection_list loop.

## Hook

This module has a single hook in the back office, adding the Selection button to the tools menu of the sidebar on
the left, redirecting to the list of selection.

## Loop

[selection_list]

This loop returns a list of selections. You can use it to display the selections you've created in your website.

### Input arguments

|Variable       |Description |
|---            |--- |
|**id**         | A string containing the IDs of all the selections you wish to display. To get the ID of the current rewritten URL, use : $app->request->get('selection_id') in your template|
|**title**      | The title of the selection you wish to display |
|**visible**    | Whether your selection will be visible or not. Default : true |
|**position**   | The position of the selection you wish to display |
|**exclude**    | A string containing the IDs of all the selections you wish not to display |

### Output arguments

|Variable                   |Description |
|---                        |--- |
|**SELECTION_ID**           | The ID of the current Selection |
|**SELECTION_TITLE**        | The title of the current Selection |
|**SELECTION_DESCRIPTION**  | The description of the current Selection |
|**SELECTION_CHAPO**        | The chapo of the current Selection |
|**SELECTION_POSTSCRIPTUM** | The postscriptum of the current Selection |
|**SELECTION_VISIBLE**      | Whether the current selection is visible or not |
|**SELECTION_POSITION**     | The position of the current selection |
|**SELECTION_URL**          | The URL of the current selection |

### Exemple
````
    {loop name="selection_list" type="selection_list" visible=true id='1,4'}
        This selection id           : {$SELECTION_ID}
        This selection title        : {$SELECTION_TITLE}
        This selection status       : {$SELECTION_VISIBLE}
        This selection description  : {$SELECTION_DESCRIPTION}
        This selection chapo        : {$SELECTION_CHAPO}
        This selection url          : {$SELECTION_URL}
        This selection postscriptum : {$SELECTION_POSTSCRIPTUM}
        This selection position     : {$SELECTION_POSITION}
    {/loop}
````

[selection_image]

This loop returns the images related to a selection. 

### Input arguments

Input Arguments are extended by Thelia\Core\Templates\Loop\Image

### Output arguments

Output Arguments are extended by Thelia\Core\Templates\Loop\Image


### Exemple
````
    {loop type="selection_image" name="selection_image" source="selection" source_id=$SELECTION_ID limit="1" visible="true"}
    {/loop}
````

