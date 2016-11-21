<?php

namespace Lexxpavlov\SettingsBundle\Admin;

use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Show\ShowMapper;

use Lexxpavlov\SettingsBundle\Entity\Category;

class CategoryAdmin extends AbstractAdmin
{
    public function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('name')
            ->add('comment')
        ;
    }

    public function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->add('name')
            ->add('comment')
        ;
    }

    public function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('name')
        ;
    }

    public function configureShowFields(ShowMapper $showMapper)
    {
        $showMapper
            ->add('name')
            ->add('comment')
        ;
    }

    /**
     * @param Category $object
     */
    public function postPersist($object)
    {
        $this->clearCache($object);
    }

    /**
     * @param Category $object
     */
    public function postUpdate($object)
    {
        $this->clearCache($object);
    }

    /**
     * @param Category $object
     */
    public function preRemove($object)
    {
        $this->clearCache($object);
    }

    /**
     * @param Category $object
     */
    private function clearCache(Category $object)
    {
        /** @var \Lexxpavlov\SettingsBundle\Service\Settings $settings */
        $settings = $this->getConfigurationPool()->getContainer()->get('lexxpavlov_settings.settings');
        $settings->clearGroupCache($object->getName());
    }
}
