<?php

namespace Lexxpavlov\SettingsBundle\Admin;

use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Show\ShowMapper;

use Lexxpavlov\SettingsBundle\DBAL\SettingsType;
use Lexxpavlov\SettingsBundle\Entity\Settings;
use Lexxpavlov\SettingsBundle\Form\Type\SettingValueType;

class SettingsAdmin extends AbstractAdmin
{
    public function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('name')
            ->add('category')
            ->add('type', 'choice', array('choices' => SettingsType::getReadableValues(), 'catalogue' => 'messages'))
            ->add('value', null, array('template' => 'LexxpavlovSettingsBundle:Admin:list_value.html.twig'))
            ->add('comment')
        ;
    }

    public function configureFormFields(FormMapper $formMapper)
    {
        $valueType = method_exists('Symfony\Component\Form\AbstractType', 'getBlockPrefix')
            ? 'Lexxpavlov\SettingsBundle\Form\Type\SettingValueType'
            : 'setting_value';
        $formMapper
            ->add('name')
            ->add('category', 'sonata_type_model_list')
            ->add('type', 'choice', array(
                'choices' => SettingsType::getChoices(),
                'attr' => array('data-sonata-select2'=>'false'),
            ))
            ->add('value', $valueType)
            ->add('comment')
        ;
    }

    public function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('category')
            ->add('name')
            ->add('type', null, array(), 'choice', array('choices' => SettingsType::getChoices()))
        ;
    }

    public function configureShowFields(ShowMapper $showMapper)
    {
        $showMapper
            ->add('name')
            ->add('type')
            ->add('value')
            ->add('comment')
        ;
    }

    /**
     * @param Settings $object
     */
    public function postPersist($object)
    {
        $this->clearCache($object);
    }

    /**
     * @param Settings $object
     */
    public function postUpdate($object)
    {
        $this->clearCache($object);
    }

    /**
     * @param Settings $object
     */
    public function preRemove($object)
    {
        $this->clearCache($object);
    }

    public function getFormTheme()
    {
        return array_merge(
            parent::getFormTheme(),
            array('LexxpavlovSettingsBundle:Form:setting_value_edit.html.twig')
        );
    }

    /**
     * @param Settings $object
     */
    private function clearCache(Settings $object)
    {
        /** @var \Lexxpavlov\SettingsBundle\Service\Settings $settings */
        $settings = $this->getConfigurationPool()->getContainer()->get('lexxpavlov_settings.settings');
        $settings->clearCache($object->getName());
        if ($object->getCategory()) {
            $settings->clearGroupCache($object->getCategory()->getName());
        }
    }
}
