<?php
/**
 * Created by PhpStorm.
 * User: audreymartel
 * Date: 11/07/2018
 * Time: 16:53
 */

namespace Selection\Form;

use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Validator\Constraints;

trait CreationCommonFieldsTrait
{
    protected function addCommonFields()
    {
        /** @noinspection PhpUndefinedMethodInspection */
        $this->formBuilder
        ->add(
            'title',
            TextType::class,
            array(
                "constraints"   => array(
                    new Constraints\NotBlank()
                ),
                "label"         => 'Title',
            )
        )
        ->add(
            'chapo',
            TextareaType::class,
            array(
                "constraints"   => array(),
                "label"         =>'Summary',
            )
        )
        ->add(
            'description',
            TextareaType::class,
            array(
                'attr'          => array('class' => 'tinymce'),
                "constraints"   => array(),
                "label"         =>'Description',
            )
        )
        ->add(
            'postscriptum',
            TextareaType::class,
            array(
                "constraints"   => array(),
                "label"         =>'Conclusion',
            )
        )
        ->add(
            'save',
            SubmitType::class,
            array(
                'attr'          => array('class' => 'save'),
                'label'         =>'Save'
            )
        );
    }
}