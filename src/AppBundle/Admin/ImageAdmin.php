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

use Glavweb\CoreBundle\Form\Type\FormStaticControlRawType;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Route\RouteCollection;
use AppBundle\Entity\Image;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;

/**
 * Class ImageAdmin
 *
 * @author Andrey Nilov <nilov@glavweb.ru>
 */
class ImageAdmin extends AbstractAdmin
{
    /**
     * The base route pattern used to generate the routing information
     *
     * @var string
     */
    protected $baseRoutePattern = 'image';

    /**
     * The base route name used to generate the routing information
     *
     * @var string
     */
    protected $baseRouteName = 'image';

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
            ->add('author')
        ;
    }

    /**
     * @param ListMapper $listMapper
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->add('image', null, [
                'template' => 'admin/image/list_field_image.html.twig'
            ])
            ->add('name')
            ->add('author')
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
        /** @var Image $image */
        $image = $this->getSubject();
        $isNew = !$image || !$image->getId();

        $container = $this->getConfigurationPool()->getContainer();
        $uploaderHelper = $container->get('vich_uploader.templating.helper.uploader_helper');

        $formMapper->with('Common', array('class' => 'col-md-8', 'name' => 'Common'))->end();
        $formMapper->with('Image', array('class' => 'col-md-4', 'name' => 'Image'))->end();

        $imagePreview = $this->trans('form.image_not_loaded');
        $imageExists  = $image && $image->getId() && $image->getImage();
        if ($imageExists) {
            $imagePreview = '<img src="' . $uploaderHelper->asset($image, 'imageFile') . '" style="max-width:150px;">';
        };

        $formMapper
            ->with('Common')
                ->add('name')
                ->add('author')
            ->end()
        ;

        if (!$isNew) {
            $formMapper
                ->with('Common')
                    ->add('updatedAt', 'datetime', array(
                        'widget' => 'single_text',
                        'format' => 'yyyy-MM-dd H:m',
                        'disabled' => true,
                    ))
                ->end()
            ;
        }

        $formMapper
            ->with('Image')
                ->add('imagePreview', FormStaticControlRawType::class, [
                    'data'     => $imagePreview,
                    'mapped'   => false,
                    'required' => false,
                    'label'    => false
                ])
            ->end()
        ;

        if ($imageExists) {
            $formMapper
                ->with('Image')
                    ->add('imageRemove', CheckboxType::class, [
                        'mapped'   => false,
                        'required' => false,
                    ])
                ->end()
            ;
        }

        $formMapper
            ->with('Image')
                ->add('imageFile', 'file', [
                    'label'    => false,
                    'required' => false
                ])
            ->end()
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function preUpdate($image)
    {
        /** @var Image $image */
        $form = $this->getForm();
        $imageRemove = $form->has('imageRemove') && $form->get('imageRemove')->getData();
        if ($imageRemove){
            $image->setImage(null);
        }
    }
}
