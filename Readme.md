# Selection

Selection is a module which it make a selection, a list of product or content link together in the same themes.

## Compatibility 
* To use on Thelia 2.3.x, use tag 1.0

## Installation

### Manually

* Copy the module into ```<thelia_root>/local/modules/``` directory and be sure that the name of the module is Selection.
* Activate it in your thelia administration panel

### Composer

Add it in your main thelia composer.json file

```
composer require thelia/selection-module:~1.0
```

## Usage

Once activated, imputs will appear on tools sidebar and when you click on it you will reach an empty page or a page with
your selections. With a plus button you can add a new selection or click on your selection to edit it. 
## Hook

Selection have only one hook. This hook is serving to go on the principal panel of the module. 
It is located on the tools sidebar. 

## Loop

Use this loop to provide you all selection you have.

[selection_list]

This loop returns selections. 

### Input arguments

|Argument       |Description |
|---            |--- |
|**id**         | selection id |
|**title**      | selection title |
|**visible**    | selection status visible or not |
|**position**   | selection position |

### Output arguments

|Variable               |Description |
|---                    |--- |
|**SELECTION_ID**       | selection id |
|**SELECTION_TITLE**    | selection title |
|**SELECTION_VISIBLE**  | selection status visible or not |
|**SELECTION_POSITION** | selection position |

### Exemple
````
    {loop name="selection_list" type="selection_list" visible="*"}
        id          : {$SELECTION_ID}
        title       : {$SELECTION_TITLE}
        visible     : {$SELECTION_VISIBLE}
        position    : {$SELECTION_POSITION}
    {/loop}
````

[selection_image]

This loop returns image related to a selection. 

### Input arguments

Input Arguments are extended by Thelia\Core\Templates\Loop\Image

### Output arguments

Output Arguments are extended by Thelia\Core\Templates\Loop\Image


### Exemple
````
    {loop type="selection_image" name="selection_image" source="selection" source_id=$SELECTION_ID limit="1" visible="true"}
    {/loop}
````

