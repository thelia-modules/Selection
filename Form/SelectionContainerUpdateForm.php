<?php
/**
 * Created by PhpStorm.
 * User: audreymartel
 * Date: 10/07/2018
 * Time: 12:19
 */

namespace Selection\Form;


use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Thelia\Core\Translation\Translator;
use Thelia\Form\BaseForm;
use Symfony\Component\Validator\Constraints;

class SelectionContainerUpdateForm extends BaseForm
{

    /**
     *  Form build for add and update a selection
     */
    protected function buildForm()
    {
        $this->formBuilder
            ->add(
                'selection_container_id',
                TextType::class,
                array(
                    "constraints"   => array(
                        new Constraints\NotBlank()
                    ),
                    "label"         => Translator::getInstance()->trans('Selection reference'),
                    "required"      => false,
                    "read_only"     => true,
                )
            )
            ->add(
                'selection_container_title',
                TextType::class,
                array(
                    "constraints"   => array(
                        new Constraints\NotBlank()
                    ),
                    "label"         => Translator::getInstance()->trans('Title'),
                    "required"      => false,
                )
            )
            ->add(
                'selection_container_chapo',
                TextareaType::class,
                array(
                    'attr'          => array('class' => 'tinymce'),
                    "constraints"   => array(
                    ),
                    "label"         =>Translator::getInstance()->trans('Summary'),
                    "required"      => false,
                )
            )
            ->add(
                'selection_container_description',
                TextareaType::class,
                array(
                    'attr'          => array('class' => 'tinymce'),
                    "constraints"   => array(
                    ),
                    "label"         =>Translator::getInstance()->trans('Description'),
                    "required"      => false,
                )
            )
            ->add(
                'selection_container_postscriptum',
                TextareaType::class,
                array(
                    'attr'          => array('class' => 'tinymce'),
                    "constraints"   => array(
                    ),
                    "label"         => Translator::getInstance()->trans('Conclusion'),
                    "required"      => false,
                )
            )
           ;

    }

    /**
     * @return string the name of the form. This name need to be unique.
     */
    public function getName()
    {
        return "admin_selection_container_update";
    }
}
