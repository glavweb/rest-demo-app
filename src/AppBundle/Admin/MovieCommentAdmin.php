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
use AppBundle\Entity\MovieComment;
use Knp\Menu\ItemInterface as MenuItemInterface;

/**
 * Class MovieCommentAdmin
 *
 * @author Andrey Nilov <nilov@glavweb.ru>
 */
class MovieCommentAdmin extends AbstractAdmin
{
    /**
     * The base route pattern used to generate the routing information
     *
     * @var string
     */
    protected $baseRoutePattern = 'movie-comment';

    /**
     * The base route name used to generate the routing information
     *
     * @var string
     */
    protected $baseRouteName = 'movie_comment';

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

        /** @var MovieComment $movieComment */
        $movieComment = $this->getSubject();

        $router = $this->getConfigurationPool()->getContainer()->get('router');
        $menu->addChild($this->trans('form.label_images'), array(
            'uri' => $router->generate('movie_comment_movie_comment_image_list', array('id' => $movieComment->getId()))
        ));
    }

    /**
     * @param DatagridMapper $datagridMapper
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('body')
            ->add('publish')
            ->add('author')
            ->add('movie')
        ;
    }

    /**
     * @param ListMapper $listMapper
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->add('movie')
            ->add('body')
            ->add('publish', null, ['editable' => true])
            ->add('author')
            ->add('createdAt')
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
        /** @var MovieComment $movieComment */
        $movieComment = $this->getSubject();
        $isNew = !$movieComment || !$movieComment->getId();

        if ($isNew) {
            $movieComment->setPublish(true);
        }

        $formMapper->with('Common', array('class' => 'col-md-4', 'name' => 'Common'))->end();
        $formMapper->with('Body', array('class' => 'col-md-8', 'name' => 'Body'))->end();

        $formMapper
            ->with('Common')
                ->add('movie')
                ->add('author')
                ->add('publish')
            ->end()
        ;

        if (!$isNew) {
            $formMapper
                ->with('Common')
                    ->add('createdAt', 'datetime', array(
                        'widget' => 'single_text',
                        'format' => 'yyyy-MM-dd H:m',
                        'disabled' => true,
                    ))
                ->end()
            ;
        }

        $formMapper
            ->with('Body')
                ->add('body', null, [
                    'label' => false,
                    'attr'  => ['rows' => 10]
                ])
            ->end()
        ;
    }
}
