<?php

/*
 * This file is part of the "rest demo app" package.
 *
 * (c) GLAVWEB <info@glavweb.ru>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace AppBundle\Admin;

use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Admin\AdminInterface;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Route\RouteCollection;
use AppBundle\Entity\Movie;
use Knp\Menu\ItemInterface as MenuItemInterface;

/**
 * Class MovieAdmin
 *
 * @author Andrey Nilov <nilov@glavweb.ru>
 */
class MovieAdmin extends AbstractAdmin
{
    /**
     * The base route pattern used to generate the routing information
     *
     * @var string
     */
    protected $baseRoutePattern = 'movie';

    /**
     * The base route name used to generate the routing information
     *
     * @var string
     */
    protected $baseRouteName = 'movie';

    /**
     * The number of result to display in the list.
     *
     * @var int
     */
    protected $maxPerPage = 20;

    /**
     * Predefined per page options.
     *
     * @var array
     */
    protected $perPageOptions = [20, 40, 60, 120, 180];

    /**
     * {@inheritdoc}
     */
    public function configure()
    {
        $this->formOptions['translation_domain'] = $this->getTranslationDomain();
    }

    /**
     * @param RouteCollection $collection
     */
    protected function configureRoutes(RouteCollection $collection)
    {
        $collection->remove('show');
    }

    /**
     * @param MenuItemInterface $menu
     * @param $action
     * @param AdminInterface $childAdmin
     * @return mixed|void
     */
    protected function configureTabMenu(MenuItemInterface $menu, $action, AdminInterface $childAdmin = null)
    {
        if ($childAdmin !== null || !in_array($action, array('edit', 'show'))) {
            return;
        }

        /** @var Movie $movie */
        $movie = $this->getSubject();

        $router = $this->getConfigurationPool()->getContainer()->get('router');
        $menu->addChild($this->trans('form.label_sessions'), array(
            'uri' => $router->generate('movie_movie_child_session_list', array('id' => $movie->getId()))
        ));

        $menu->addChild($this->trans('form.label_comments'), array(
            'uri' => $router->generate('movie_movie_child_comment_list', array('id' => $movie->getId()))
        ));
    }

    /**
     * @param DatagridMapper $datagridMapper
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('name')
            ->add('group')
            ->add('tags')
            ->add('articles')
        ;
    }

    /**
     * @param ListMapper $listMapper
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->add('name')
            ->add('group')
            ->add('tags')
            ->add('_action', 'actions', array(
                'actions' => array(
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
        $formMapper->with('Common', array('class' => 'col-md-6', 'name' => 'Common'))->end();
        $formMapper->with('Collection', array('class' => 'col-md-6', 'name' => 'Collection'))->end();

        $formMapper
            ->with('Common')
                ->add('name')
                ->add('group')
                ->add('detail', 'sonata_type_admin')
            ->end()

            ->with('Collection')
                ->add('tags', 'sonata_type_model', array(
                    'multiple' => true,
                    'required' => false
                ))
                ->add('articles', 'sonata_type_model', array(
                    'multiple' => true,
                    'required' => false,
                    'by_reference' => false,
                    'btn_add' => false
                ))
            ->end()
        ;
    }
}
