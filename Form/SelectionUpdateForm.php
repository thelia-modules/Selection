<?php

namespace Selection\Form;

use Propel\Runtime\Exception\PropelException;
use Selection\Model\SelectionContainerQuery;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Validator\Constraints;
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
                "label"         =>  Translator::getInstance()->trans('Selection reference'),
                "required"      => false,
                "read_only"     => true,
                )
            )
            ->add(
                'selection_container',
                ChoiceType::class,
                [
                    'choices' => $this->containersArray,
//                    'placeholder' => true, //NOT WORK
                    'multiple' => false,
                    'expanded' => false,
                    'choice_label' => function($key,
                        /** @noinspection PhpUnusedParameterInspection */
                        $index,
                        /** @noinspection PhpUnusedParameterInspection */
                        $value) {
                        return $key;
                    },
                    'choice_value' => function($key) {
                        if (array_key_exists($key, $this->containersArray)) {
                            return $this->containersArray[$key];
                        }
                        return '0';
                    },

                    "label" => Translator::getInstance()->trans('Container'),
                    'required' => false,
                    'empty_data' => null,
                ]
            )
            ->add(
                'selection_title',
                TextType::class,
                [
                    "constraints"   => [],
                    "label"         => Translator::getInstance()->trans('Title'),
                    "required"      => false,
                ]
            )
            ->add(
                'selection_chapo',
                TextareaType::class,
                array(
                    'attr'          => array('class' => 'tinymce'),
                    "constraints"   => [],
                    "label"         =>Translator::getInstance()->trans('Summary'),
                "required"      => false,
                )
            )
            ->add(
                'selection_description',
                TextareaType::class,
                array(
                    'attr'          => array('class' => 'tinymce'),
                    "constraints"   => [],
                    "label"         =>Translator::getInstance()->trans('Description'),
                "required"      => false,
                )
            )
            ->add(
                'selection_postscriptum',
                TextareaType::class,
                array(
                    'attr'          => array('class' => 'tinymce'),
                    "constraints"   => [],
                    "label"         => Translator::getInstance()->trans('Conclusion'),
                "required"      => false,
                )
            );

        //instead of option value, symfony take option key!
        //these 2 event listeners are a hack
        $this->formBuilder->addEventListener(
            FormEvents::SUBMIT,
            function (FormEvent $event)
            {
                $data = $event->getData();
                 $selectionContainerWrongValue = $data['selection_container'];
                $selectionContainerValue = $this->containersArray[$selectionContainerWrongValue];
                $data['selection_container_id'] = $selectionContainerValue;
                $event->setData($data);
            }
        );

        $this->formBuilder->addEventListener(
            FormEvents::PRE_SET_DATA,
            function (FormEvent $event)
            {
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

    /**
     * @return string the name of the form. This name need to be unique.
     */
    public function getName()
    {
        return "admin_selection_update";
    }

    private function initContainers()
    {
        /** @noinspection PhpUndefinedMethodInspection */
        /** @noinspection PhpUndefinedFieldInspection */
        $lang = $this->request->getSession() ? $this->request->getSession()->getLang(true) : $this->request->lang = Lang::getDefaultLanguage();
        $containers = SelectionContainerQuery::getAll($lang);
        $this->containersArray = [];
        $this->containersArray['-'] = null; //because placeholder is not working
        foreach ($containers as $container) {
            try {
                $this->containersArray[$container->getVirtualColumn("i18n_TITLE")] = $container->getId();
            } catch (PropelException $e) {
                Tlog::getInstance()->error($e->getMessage());
            }
        }
    }
}
