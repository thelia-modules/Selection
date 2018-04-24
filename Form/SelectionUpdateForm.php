<?php

namespace Selection\Form;

use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Thelia\Core\Translation\Translator;
use Thelia\Form\BaseForm;
use Symfony\Component\Validator\Constraints;

class SelectionUpdateForm extends BaseForm
{
    /**
     *  Form build for add and update a selection
     */
    protected function buildForm()
    {
        $this->formBuilder
            ->add(
                'selection_id',
                'text',
                array(
                    "constraints"   => array(
                        new Constraints\NotBlank()
                    ),
                "label"         => 'Selection reference',
                "required"      => false,
                "read_only"     => true,
                )
            )
            ->add(
                'selection_title',
                'text',
                array(
                    "constraints"   => array(
                        new Constraints\NotBlank()
                    ),
                "label"         => Translator::getInstance()->trans('Title'),
                "required"      => false,
                )
            )
            ->add(
                'selection_chapo',
                TextareaType::class,
                array(
                    'attr'          => array('class' => 'tinymce'),
                    "constraints"   => array(
                        new Constraints\NotBlank()
                    ),
                "label"         =>Translator::getInstance()->trans('Summary'),
                "required"      => false,
                )
            )
            ->add(
                'selection_description',
                TextareaType::class,
                array(
                    'attr'          => array('class' => 'tinymce'),
                    "constraints"   => array(
                        new Constraints\NotBlank()
                    ),
                "label"         =>Translator::getInstance()->trans('Description'),
                "required"      => false,
                )
            )
            ->add(
                'selection_postscriptum',
                TextareaType::class,
                array(
                    'attr'          => array('class' => 'tinymce'),
                    "constraints"   => array(
                        new Constraints\NotBlank()
                    ),
                "label"         => Translator::getInstance()->trans('Conclusion'),
                "required"      => false,
                )
            )
            ->add(
                'save_mode',
                SubmitType::class,
                array(
                    'attr'          => array('class' => 'save'),
                    'label'         =>'save',
                    )
            )
            ->add(
                'save_mode',
                SubmitType::class,
                array(
                    'attr'          => array('class' => 'save_and_close'),
                    'label'         =>'save_and_close'
                    )
            );
    }

    /**
     * @return string the name of the form. This name need to be unique.
     */
    public function getName()
    {
        return "admin_selection_update";
    }
}
