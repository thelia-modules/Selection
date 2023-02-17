<?php

namespace Selection\Form;

use Propel\Runtime\ActiveQuery\Criteria;
use Propel\Runtime\Exception\PropelException;
use Selection\Model\SelectionContainerQuery;
use Selection\Model\SelectionQuery;
use Selection\Selection;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Validator\Constraints;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Thelia\Core\Translation\Translator;
use Thelia\Form\BaseForm;
use Thelia\Log\Tlog;
use Thelia\Model\Lang;

class SelectionUpdateForm extends BaseForm
{
    /**
     * @var []
     */
    private $containersArray;

    /**
     *  Form build for add and update a selection
     */
    protected function buildForm()
    {
        $this->initContainers();
        $this->formBuilder
            ->add(
                'selection_id',
                TextType::class,
                array(
                    "constraints"   => array(
                        new Constraints\NotBlank()
                    ),
                "label"         =>  Translator::getInstance()->trans('Selection reference', [], Selection::DOMAIN_NAME),
                "required"      => false,
                "disabled"     => true,
                )
            )
            ->add(
                'selection_code',
                TextType::class,
                array(
                    "constraints"   => array(
                        new Constraints\NotBlank(),
                        new Constraints\Callback([
                            "methods" => [
                                [$this, "checkDuplicateCode"],
                            ]
                        ]),
                    ),
                "label"         =>  Translator::getInstance()->trans('Selection code', [], Selection::DOMAIN_NAME),
                )
            )
            ->add(
                'selection_container',
                ChoiceType::class,
                [
                    'choices' => $this->containersArray,
                    'multiple' => false,
                    'expanded' => false,
                    'choice_label' => function ($key, $index, $value) {
                        return $key;
                    },
                    'choice_value' => function ($key) {
                        if (array_key_exists($key, $this->containersArray)) {
                            return $this->containersArray[$key];
                        }
                        return '0';
                    },

                    "label" => Translator::getInstance()->trans('Container', [], Selection::DOMAIN_NAME),
                    'required' => false,
                    'empty_data' => null,
                ]
            )
            ->add(
                'selection_title',
                TextType::class,
                [
                    "constraints"   => [],
                    "label"         => Translator::getInstance()->trans('Title', [], Selection::DOMAIN_NAME),
                    "required"      => false,
                ]
            )
            ->add(
                'selection_chapo',
                TextareaType::class,
                array(
                    'attr'          => array('class' => 'tinymce'),
                    "constraints"   => [],
                    "label"         =>Translator::getInstance()->trans('Summary', [], Selection::DOMAIN_NAME),
                "required"      => false,
                )
            )
            ->add(
                'selection_description',
                TextareaType::class,
                array(
                    'attr'          => array('class' => 'tinymce'),
                    "constraints"   => [],
                    "label"         =>Translator::getInstance()->trans('Description', [], Selection::DOMAIN_NAME),
                "required"      => false,
                )
            )
            ->add(
                'selection_postscriptum',
                TextareaType::class,
                array(
                    'attr'          => array('class' => 'tinymce'),
                    "constraints"   => [],
                    "label"         => Translator::getInstance()->trans('Conclusion', [], Selection::DOMAIN_NAME),
                "required"      => false,
                )
            );

        //instead of option value, symfony take option key!
        //these 2 event listeners are a hack
        $this->formBuilder->addEventListener(
            FormEvents::SUBMIT,
            function (FormEvent $event) {
                $data = $event->getData();
                $selectionContainerWrongValue = $data['selection_container'];
                $selectionContainerValue = $this->containersArray[$selectionContainerWrongValue];
                $data['selection_container_id'] = $selectionContainerValue;
                $event->setData($data);
            }
        );

        $this->formBuilder->addEventListener(
            FormEvents::PRE_SET_DATA,
            function (FormEvent $event) {
                $data = $event->getData();
                if (!array_key_exists('selection_container', $data)) {
                    return;
                }
                $key = array_search($data['selection_container'], $this->containersArray);
                $data['selection_container'] = $key;
                $event->setData($data);
            }
        );
    }

    public function checkDuplicateCode($value, ExecutionContextInterface $context)
    {
        $data = $context->getRoot()->getData();

        $count = SelectionQuery::create()
            ->filterById($data['selection_id'], Criteria::NOT_EQUAL)
            ->filterByCode($value)->count();

        if ($count > 0) {
            $context->addViolation(
                Translator::getInstance()->trans(
                    "A selection with code %code already exists. Please enter a different code.",
                    array('%code' => $value)
                )
            );
        }
    }

    /**
     * @return string the name of the form. This name need to be unique.
     */
    public static function getName(): string
    {
        return "admin_selection_update";
    }

    private function initContainers()
    {
        $lang = $this->request->getSession() ? $this->request->getSession()->getLang(true) : $this->request->lang = Lang::getDefaultLanguage();
        $containers = SelectionContainerQuery::getAll($lang);
        $this->containersArray = [];
        $this->containersArray[Translator::getInstance()->trans('None', [], Selection::DOMAIN_NAME)] = null; //because placeholder is not working
        foreach ($containers as $container) {
            try {
                $this->containersArray[$container->getVirtualColumn("i18n_TITLE")] = $container->getId();
            } catch (PropelException $e) {
                Tlog::getInstance()->error($e->getMessage());
            }
        }
    }
}
