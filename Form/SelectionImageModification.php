<?php

namespace Selection\Form;

use Thelia\Form\Image\ImageModification;

class SelectionImageModification extends ImageModification
{
    public static function getName(): string
    {
        return 'selection_image_modification';
    }
}
