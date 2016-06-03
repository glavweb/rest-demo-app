<?php

namespace ExampleBundle\Admin;

use ExampleBundle\DBAL\Types\ExampleType;
use ExampleBundle\Entity\Entity;
use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Admin\AdminInterface;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;
use Knp\Menu\ItemInterface as MenuItemInterface;

class EntityAdmin extends Admin
{
    /**
     * The base route pattern used to generate the routing information
     *
     * @var string
     */
    protected $baseRoutePattern = 'entity';

    /**
     * The base route name used to generate the routing information
     *
     * @var string
     */
    protected $baseRouteName = 'entity';

    /**
     * @param MenuItemInterface $menu
     * @param $action
     * @param AdminInterface $childAdmin
     * @return mixed|void
     */
    protected function configureSideMenu(MenuItemInterface $menu, $action, AdminInterface $childAdmin = null)
    {
        if ($childAdmin !== null || !in_array($action, array('edit', 'show'))) {
            return;
        }

        $admin = $this->isChild() ? $this->getParent() : $this;
        $id = $admin->getRequest()->get('id');

        // Child entity
        $menu->addChild('ChildEntityMenuName', array(
            'uri' => $admin->generateUrl('child_entity.list', array('id' => $id))
        ));

        // Custom page
        $router = $this->getConfigurationPool()->getContainer()->get('router');
        $menu->addChild('CustomPageMenuName', array(
            'uri' => $router->generate('custom_page', array('entity' => $id))
        ));
    }

    /**
     * @param DatagridMapper $datagridMapper
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('field')
            ->add('status', 'doctrine_orm_choice', array(), 'choice', array(
                'choices' => ExampleType::getChoices(),
                'expanded' => true,
                'multiple' => true,
                'translation_domain' => 'entity'
            ))

        ;
    }

    /**
     * @param ListMapper $listMapper
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->add('field')
            ->add('status', 'choice', array(
                'choices'   => TestType::getChoices(),
                'catalogue' => 'entity',
            ))
            ->add('_action', 'actions', array(
                'actions' => array(
                    'show' => array(),
                    'edit' => array(),
                    'delete' => array(),
                )
            ))
        ;
    }

    /**
     * @param FormMapper $formMapper
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        /** @var Entity $entity */
        $entity         = $this->getSubject();
        $container      = $this->getConfigurationPool()->getContainer();
        $uploaderHelper = $container->get('vich_uploader.templating.helper.uploader_helper');

        $imagePreview = 'Не загружено';
        if ($entity && $entity->getId() && $entity->getImage()) {
            $imagePreview = '<img src="' . $uploaderHelper->asset($entity, 'imageFile') . '" style="max-width:150px;">';
        };

        $formMapper->with('Common', array('class' => 'col-md-6'))->end();
        $formMapper->with('Image', array('class' => 'col-md-6'))->end();
        $formMapper->with('Collection', array('class' => 'col-md-12'))->end();

        $formMapper
            ->with('Common')
                ->add('field')
                ->add('type', 'sonata_type_translatable_choice', array(
                    'choice_list' => new SimpleChoiceList(TestType::getChoices()),
                    'translation_domain' => 'entity'
                ))
                ->add('datetime', 'sonata_type_datetime_picker', array(
                    'dp_use_current' => false,
                    'dp_use_seconds' => false,
                    'format'         => 'dd.MM.yyyy H:m',
                    'dp_language'    => 'ru'
                ))
                ->add('date', 'sonata_type_date_picker', array(
                    'dp_use_current' => false,
                    'format'         => 'dd.MM.yyyy',
                    'dp_language'    => 'ru'
                ))
            ->end()

            ->with('Image')
                ->add('imagePreview', 'bs_static_raw', array(
                    'data'     => $imagePreview,
                    'mapped'   => false,
                    'required' => false,
                    'label'    => 'Изображение'
                ))
                ->add('imageFile', 'file', array(
                    'label'    => false,
                    'required' => false,
                    'help'     => 'Допустимый размер от 100*150 px',
                ))
            ->end()

            ->with('Collection')
                ->add('oneToManyFields', 'sonata_type_collection',
                    array(
                        'required'     => false,
                        'type_options' => array(
                            'delete'   => true,
                            'required' => true,
                        ),
                    ),
                    array(
                        'edit'         => 'inline',
                        'inline'       => 'table',
                        'allow_delete' => true,
                    )
                )
            ->end()
        ;
    }

    /**
     * @param ShowMapper $showMapper
     */
    protected function configureShowFields(ShowMapper $showMapper)
    {
        $showMapper
            ->add('field')
        ;
    }

    /**
     * @param mixed $entity
     */
    public function postPersist($entity)
    {
        $this->preUpdate($entity);

        $em = $this->getConfigurationPool()->getContainer()->get('doctrine')->getManager();
        $em->flush();
    }

    /**
     * @param mixed $entity
     */
    public function preUpdate($entity)
    {
        /** @var Entity $entity */

        $fields = $entity->getOneToManyFields();
        foreach ($fields as $field) {
            $field->setEntity($entity);
        }
    }
}
