<?php

namespace Lexxpavlov\SettingsBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EntityType;
use Lexxpavlov\SettingsBundle\DBAL\SettingsType;

class Settings extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $valueType = method_exists('Symfony\Component\Form\AbstractType', 'getBlockPrefix')
            ? 'Lexxpavlov\SettingsBundle\Form\Type\SettingValueType'
            : 'setting_value';
        $builder
            ->add('category', EntityType::class, array('class' => 'LexxpavlovSettingsBundle:Category', 'property_path' => 'name', 'required' => false))
            ->add('name', TextType::class)
            ->add('type', ChoiceType::class, array('choices' => SettingsType::getChoices()))
            ->add('value', $valueType)
            ->add('comment', TextareaType::class, array('required' => false))
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
