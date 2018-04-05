<?php
/**
 * Created by PhpStorm.
 * User: tpradatos
 * Date: 05/04/2018
 * Time: 11:56
 */

namespace Selection\Form;

use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Thelia\Core\Translation\Translator;
use Thelia\Form\BaseForm;
use Symfony\Component\Validator\Constraints;

class SelectionFolderUpdateForm extends BaseForm
{
    protected function buildForm()
    {
        $this->formBuilder
            ->add(
                'folder_id',
                HiddenType::class,
                array(
                    "constraints"   => array(
                        new Constraints\NotBlank()
                    ),
                    "label"         => Translator::getInstance()->trans('Folder reference'),
                    "required"      => false,

                )
            )
            ->add(
                'folder_title',
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
                'folder_chapo',
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
                'folder_description',
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
                'folder_postscriptum',
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
        return "admin_selection_folder_update";
    }
}