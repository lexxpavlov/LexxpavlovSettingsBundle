<?php

namespace Lexxpavlov\SettingsBundle\Form\Type;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SettingValueType extends AbstractType
{
    /** @var ContainerInterface */
    private $container;

    /** @var string */
    private $htmlWidget;

    /**
     * @param ContainerInterface $container
     * @param string $htmlWidget
     */
    public function __construct(ContainerInterface $container, $htmlWidget)
    {
        $this->container = $container;
        $this->htmlWidget = $htmlWidget;
    }

    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $choiceViewClass = Kernel::MAJOR_VERSION == 2 && Kernel::MINOR_VERSION == 6
            ? 'Symfony\Component\Form\Extension\Core\View\ChoiceView'
            : 'Symfony\Component\Form\ChoiceList\View\ChoiceView';
        $choiceList = array(
            new $choiceViewClass('off', 'off', "Off"),
            new $choiceViewClass('on', 'on', "On"),
        );

        $view->vars = array_replace($view->vars, array(
            'required' => false,
            'multiple' => false,
            'expanded' => false,
            'empty_data' => null,
            'attr' => array('data-lexxpavlov-settings'=>'true', 'data-sonata-select2'=>'false'),
            'preferred_choices' => null,
            'choices' => $choiceList,
            'choice_translation_domain' => 'messages',
            'placeholder' => null,
            'html_widget' => $this->htmlWidget,
        ));
        if ($this->htmlWidget == 'ckeditor') {
            if ($this->container->has('ivory_ck_editor.form.type')) {
                $resolver = new OptionsResolver();
                $builder = $this->container->get('form.factory')->createBuilder();

                $ckeditorType = $this->container->get('ivory_ck_editor.form.type');
                if (method_exists($ckeditorType, 'configureOptions')) {
                    $ckeditorType->configureOptions($resolver);
                } else {
                    $ckeditorType->setDefaultOptions($resolver);
                }
                $ckeditorType->buildForm($builder, $resolver->resolve());
                $ckeditorType->buildView($view, $builder->getForm(), []);
            } else {
                $view->vars = array_replace($view->vars, array(
                    'base_path' => $this->container->getParameter('lexxpavlov_settings.ckeditor.base_path'),
                    'js_path' => $this->container->getParameter('lexxpavlov_settings.ckeditor.js_path'),
                ));
            }
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
        return $this->getBlockPrefix();
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'setting_value';
    }
}
