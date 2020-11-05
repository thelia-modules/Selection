<?php

namespace Selection\Form;


use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Validator\Constraints\NotBlank;
use Thelia\Form\BaseForm;

class SelectionCreateForm extends BaseForm
{
    use CreationCommonFieldsTrait;

    protected function buildForm()
    {
        $this->addCommonFields();

        $this->formBuilder->add(
            'container_id',
            HiddenType::class,
            array(
                "required" => false
            )
        );

    }

    /**
     * @return string the name of the form. This name need to be unique.
     */
    public function getName()
    {
        return "admin_selection_create";
    }
}
