<?php

namespace Lexxpavlov\SettingsBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Form\Extension\Core\ChoiceList\SimpleChoiceList;

class SettingValueType extends AbstractType
{
    private $basePath;
    private $jsPath;
    private $htmlWidget;

    private $booleanChoices = array('Off', 'On');

    public function __construct($htmlWidget, $basePath, $jsPath)
    {
        $this->htmlWidget = $htmlWidget;
        $this->basePath = $basePath;
        $this->jsPath = $jsPath;
    }

    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $choiceList = new SimpleChoiceList($this->booleanChoices);

        $view->vars = array_replace($view->vars, array(
            'multiple' => false,
            'expanded' => false,
            'empty_value' => null,
            'preferred_choices' => null,
            'choices' => $choiceList->getRemainingViews(),
            'html_widget' => $this->htmlWidget,
        ));
        if ($this->htmlWidget == 'ckeditor') {
            $view->vars = array_replace($view->vars, array(
                'enable' => true,
                'autoload' => true,
                'base_path' => $this->basePath,
                'js_path' => $this->jsPath,
                'config' => array(),
                'plugins' => array(),
                'styles' => array(),
                'templates' => array(),
            ));
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getParent()
    {
        return 'text';
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'setting_value';
    }
}
