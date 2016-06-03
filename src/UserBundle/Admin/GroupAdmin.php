<?php

namespace UserBundle\Admin;

use Glavweb\RestBundle\Form\SecurityRolesType;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;

/**
 * Class GroupAdmin
 * @package UserBundle\Admin
 */
class GroupAdmin extends AbstractAdmin
{
    /**
     * The base route pattern used to generate the routing information
     *
     * @var string
     */
    protected $baseRoutePattern = 'group';

    /**
     * The base route name used to generate the routing information
     *
     * @var string
     */
    protected $baseRouteName = 'group';

    /**
     * @var array
     */
    protected $listModes = [
        'list' => [
            'class' => 'fa fa-list fa-fw',
        ]
    ];

    /**
     * @var array
     */
    protected $formOptions = [
        'validation_groups' => 'Registration',
    ];

    /**
     * {@inheritdoc}
     */
    public function getNewInstance()
    {
        $class = $this->getClass();

        return new $class('', []);
    }

    /**
     * {@inheritdoc}
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('name')
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('name')
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->tab('Group')
                ->with('General', ['class' => 'col-md-6 header-hidden'])
                    ->add('name')
                ->end()
            ->end()

            ->tab('Security')
                ->with('Roles', ['class' => 'col-md-12 header-hidden'])
                    ->add('roles', SecurityRolesType::class, [
                        'label'    => false,
                        'expanded' => true,
                        'multiple' => true,
                        'required' => false,
                    ])
                ->end()
            ->end()
        ;
    }
}
