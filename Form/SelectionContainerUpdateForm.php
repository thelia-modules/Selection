<?php

namespace Selection\Form;

use Propel\Runtime\ActiveQuery\Criteria;
use Selection\Model\SelectionContainerQuery;
use Selection\Selection;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Validator\Constraints;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Thelia\Core\Translation\Translator;
use Thelia\Form\BaseForm;

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
                    "label"         => Translator::getInstance()->trans('Selection reference', [], Selection::DOMAIN_NAME),
                    "required"      => false,
                    "disabled"     => true,
                )
            )
            ->add(
                'selection_container_code',
                TextType::class,
                array(
                    "constraints"   => array(
                        new Constraints\NotBlank(),
                        new Constraints\Callback([$this, "checkDuplicateCode"]),
                    ),
                    "label"         => Translator::getInstance()->trans('Selection code', [], Selection::DOMAIN_NAME),
                )
            )
            ->add(
                'selection_container_title',
                TextType::class,
                array(
                    "constraints"   => array(
                        new Constraints\NotBlank()
                    ),
                    "label"         => Translator::getInstance()->trans('Title', [], Selection::DOMAIN_NAME),
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
                    "label"         =>Translator::getInstance()->trans('Summary', [], Selection::DOMAIN_NAME),
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
                    "label"         =>Translator::getInstance()->trans('Description', [], Selection::DOMAIN_NAME),
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
                    "label"         => Translator::getInstance()->trans('Conclusion', [], Selection::DOMAIN_NAME),
                    "required"      => false,
                )
            )
           ;
    }

    public function checkDuplicateCode($value, ExecutionContextInterface $context)
    {
        $data = $context->getRoot()->getData();

        $count = SelectionContainerQuery::create()
            ->filterById($data['selection_container_id'], Criteria::NOT_EQUAL)
            ->filterByCode($value)->count();

        if ($count > 0) {
            $context->addViolation(
                Translator::getInstance()->trans(
                    "A selection container with code %code already exists. Please enter a different code.",
                    ['%code' => $value],
                    Selection::DOMAIN_NAME
                )
            );
        }
    }

    /**
     * @return string the name of the form. This name need to be unique.
     */
    public static function getName(): string
    {
        return "admin_selection_container_update";
    }
}
