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
                'locale',
                'hidden',
                [
                    'constraints' => [ new Constraints\NotBlank() ],
                    'required'    => true,
                ]
            )
            ->add(
                'title',
                TextType::class,
                array(
                    "constraints" => [
                        new Constraints\NotBlank()
                    ],
                    "label" => Translator::getInstance()->trans('Title', [], Selection::DOMAIN_NAME)
                )
            )
            ->add(
                'code',
                TextType::class,
                array(
                    "constraints" => [
                        new Constraints\NotBlank(),
                        new Constraints\Callback([
                            "methods" => [
                                [$this, "checkDuplicateCode"],
                            ]
                        ]),
                    ],
                    "label" => Translator::getInstance()->trans('Code', [], Selection::DOMAIN_NAME)
                )
            )
            ->add(
                'chapo',
                TextareaType::class,
                [
                    'required' => false,
                    "constraints" => [],
                    "label" => Translator::getInstance()->trans('Summary', [], Selection::DOMAIN_NAME),
                ]
            )
            ->add(
                'description',
                TextareaType::class,
                [
                    'required' => false,
                    'attr' => ['class' => 'tinymce'],
                    "constraints" => [],
                    "label" => Translator::getInstance()->trans('Description', [], Selection::DOMAIN_NAME),
                ]
            )
            ->add(
                'postscriptum',
                TextareaType::class,
                [
                    'required' => false,
                    "constraints" => [],
                    "label" => Translator::getInstance()->trans('Conclusion', [], Selection::DOMAIN_NAME),
                ]
            );
    }
}
