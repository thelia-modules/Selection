<?php
/**
 * Created by PhpStorm.
 * User: audreymartel
 * Date: 11/07/2018
 * Time: 16:57
 */

namespace Selection\Form;


use Selection\Model\SelectionContainerQuery;
use Selection\Model\SelectionQuery;
use Selection\Selection;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Thelia\Core\Translation\Translator;
use Thelia\Form\BaseForm;

class SelectionContainerCreateForm extends BaseForm
{
    use CreationCommonFieldsTrait;

    protected function buildForm()
    {
        $this->addCommonFields();
    }

    public function checkDuplicateCode($value, ExecutionContextInterface $context)
    {
        if (SelectionContainerQuery::create()->filterByCode($value)->count() > 0) {
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
    public function getName()
    {
        return "admin_selection_contianer_create";
    }
}
