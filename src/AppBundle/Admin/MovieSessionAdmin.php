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
use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Route\RouteCollection;
use Sonata\AdminBundle\Show\ShowMapper;
use AppBundle\Entity\MovieSession;

/**
 * Class MovieSessionAdmin
 *
 * @author Andrey Nilov <nilov@glavweb.ru>
 */
class MovieSessionAdmin extends AbstractAdmin
{
    /**
     * The base route pattern used to generate the routing information
     *
     * @var string
     */
    protected $baseRoutePattern = 'movie-session';

    /**
     * The base route name used to generate the routing information
     *
     * @var string
     */
    protected $baseRouteName = 'movie_session';

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
     * @param DatagridMapper $datagridMapper
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('name')
            ->add('movie')
        ;
    }

    /**
     * @param ListMapper $listMapper
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->add('name')
            ->add('movie')
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
        /** @var MovieSession $movieSession */
        // $movieSession = $this->getSubject();
        // $container = $this->getConfigurationPool()->getContainer();
        // $formMapper->with('Common', array('class' => 'col-md-6', 'name' => 'Common'))->end();
        // $formMapper->with('Collection', array('class' => 'col-md-6', 'name' => 'Collection'))->end();

        $formMapper
            ->add('name')
            ->add('movie')
        ;
    }

    /**
     * @param mixed $movieSession
     */
    public function prePersist($movieSession)
    {
        /** @var MovieSession $movieSession */
        $this->updateToManyFields($movieSession);
    }

    /**
     * @param mixed $movieSession
     */
    public function preUpdate($movieSession)
    {
        /** @var MovieSession $movieSession */
        $this->updateToManyFields($movieSession);
    }

    /**
     * @param MovieSession $movieSession
     */
    private function updateToManyFields(MovieSession $movieSession)
    {
    }
}
