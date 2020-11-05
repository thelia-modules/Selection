<?php
/**
 * Created by PhpStorm.
 * User: audreymartel
 * Date: 11/07/2018
 * Time: 16:53
 */

namespace Selection\Form;

use Selection\Selection;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Validator\Constraints;
use Thelia\Core\Translation\Translator;

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
                    "constraints" => array(
                        new Constraints\NotBlank()
                    ),
                    "label" => Translator::getInstance()->trans('Title', [], Selection::DOMAIN_NAME)
                )
            )
            ->add(
                'chapo',
                TextareaType::class,
                array(
                    'required' => false,
                    "constraints" => array(),
                    "label" => Translator::getInstance()->trans('Summary', [], Selection::DOMAIN_NAME),
                )
            )
            ->add(
                'description',
                TextareaType::class,
                array(
                    'required' => false,
                    'attr' => array('class' => 'tinymce'),
                    "constraints" => array(),
                    "label" => Translator::getInstance()->trans('Description', [], Selection::DOMAIN_NAME),
                )
            )
            ->add(
                'postscriptum',
                TextareaType::class,
                array(
                    'required' => false,
                    "constraints" => array(),
                    "label" => Translator::getInstance()->trans('Conclusion', [], Selection::DOMAIN_NAME),
                )
            );
    }
}
