<?php

namespace Lexxpavlov\SettingsBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Lexxpavlov\SettingsBundle\DBAL\SettingsType;

class Settings extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $valueType = method_exists('Symfony\Component\Form\AbstractType', 'getBlockPrefix')
            ? 'Lexxpavlov\SettingsBundle\Form\Type\SettingValueType'
            : 'setting_value';
        $builder
            ->add('category', 'entity', array('class' => 'LexxpavlovSettingsBundle:Category', 'property_path' => 'name', 'required' => false))
            ->add('name', 'text')
            ->add('type', 'choice', array('choices' => SettingsType::getChoices()))
            ->add('value', $valueType)
            ->add('comment', 'textarea', array('required' => false))
            ->add('save', 'submit')
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $this->configureOptions($resolver);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Lexxpavlov\\SettingsBundle\\Entity\\Settings',
        ));
    }

    public function getName()
    {
        return 'lexxpavlov_settings';
    }
}
