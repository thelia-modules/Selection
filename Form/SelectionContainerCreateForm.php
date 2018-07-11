<?php
/**
 * Created by PhpStorm.
 * User: audreymartel
 * Date: 11/07/2018
 * Time: 16:57
 */

namespace Selection\Form;


use Thelia\Form\BaseForm;

class SelectionContainerCreateForm extends BaseForm
{
    use CreationCommonFieldsTrait;

    protected function buildForm()
    {
        $this->addCommonFields();
    }

    /**
     * @return string the name of the form. This name need to be unique.
     */
    public function getName()
    {
        return "admin_selection_contianer_create";
    }
}
