<?php

namespace Selection\Form;


use Thelia\Form\BaseForm;

class SelectionCreateForm extends BaseForm
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
        return "admin_selection_create";
    }
}
