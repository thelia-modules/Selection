<?php

namespace Selection\Form;


use Selection\Model\SelectionQuery;
use Selection\Selection;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Thelia\Core\Translation\Translator;
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

    public function checkDuplicateCode($value, ExecutionContextInterface $context): void
    {
        if (SelectionQuery::create()->filterByCode($value)->count() > 0) {
            $context->addViolation(
                Translator::getInstance()->trans(
                    "A selection with code %code already exists. Please enter a different code.",
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
        return "admin_selection_create";
    }
}
