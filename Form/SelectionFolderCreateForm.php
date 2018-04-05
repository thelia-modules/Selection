<?php
/**
 * Created by PhpStorm.
 * User: tpradatos
 * Date: 04/04/2018
 * Time: 12:52
 */

namespace Selection\Form;

use Thelia\Core\Translation\Translator;
use Thelia\Form\BaseForm;
use Symfony\Component\Validator\Constraints;

class SelectionFolderCreateForm extends BaseForm
{
    protected function buildForm()
    {
        $this->formBuilder
            ->add(
                'selection_folder_title',
                'text',
                array(
                    "constraints"   => array(
                        new Constraints\NotBlank(),
                    ),
                    "label"         => Translator::getInstance()->trans('Folder Title'),
                )
            );
    }

    public function getName()
    {
        return "admin_selection_folder_create";
    }
}