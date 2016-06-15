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
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Route\RouteCollection;
use AppBundle\DBAL\Types\ArticleType;

/**
 * Class ArticleAdmin
 *
 * @author Andrey Nilov <nilov@glavweb.ru>
 */
class ArticleAdmin extends AbstractAdmin
{
    /**
     * The base route pattern used to generate the routing information
     *
     * @var string
     */
    protected $baseRoutePattern = 'article';

    /**
     * The base route name used to generate the routing information
     *
     * @var string
     */
    protected $baseRouteName = 'article';

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
            ->add('type', 'doctrine_orm_choice', array(), 'choice', array(
                'choices'  => ArticleType::getChoices(),
                'multiple' => true,
            ))
            ->add('name')
            ->add('body')
            ->add('publish')
            ->add('publishAt')
            ->add('movies')
        ;
    }

    /**
     * @param ListMapper $listMapper
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->add('name')
            ->add('publish', null, ['editable' => true])
            ->add('publishAt')
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
        $formMapper->with('Common', array('class' => 'col-md-4', 'name' => 'Common'))->end();
        $formMapper->with('Body', array('class' => 'col-md-8', 'name' => 'Body'))->end();

        $formMapper
            ->with('Common')
                ->add('type')
                ->add('name')
                ->add('publishAt', 'sonata_type_date_picker', [
                    'dp_use_current' => false,
                    'format'         => 'yyyy-MM-dd',
                    'dp_language'    => 'en'
                ])
                ->add('publish')
            ->end()
            ->with('Body')
                ->add('body', null, [
                    'label' => false,
                    'attr' => [
                        'rows' => 11
                    ]
                ])
            ->end()
        ;
    }
}
