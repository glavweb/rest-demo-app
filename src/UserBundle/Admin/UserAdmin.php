<?php

namespace UserBundle\Admin;

use FOS\UserBundle\Model\UserManagerInterface;
use Glavweb\CoreBundle\Form\Type\FormStaticControlRawType;
use Glavweb\RestBundle\Form\SecurityRolesType;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Show\ShowMapper;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use UserBundle\Entity\User;

/**
 * Class UserAdmin
 * @package UserBundle\Admin
 */
class UserAdmin extends AbstractAdmin
{
    /**
     * The base route pattern used to generate the routing information
     *
     * @var string
     */
    protected $baseRoutePattern = 'user';

    /**
     * The base route name used to generate the routing information
     *
     * @var string
     */
    protected $baseRouteName = 'user';

    /**
     * The number of result to display in the list.
     *
     * @var int
     */
    protected $maxPerPage = 20;

    /**
     * Default values to the datagrid.
     *
     * @var array
     */
    protected $datagridValues = [
        '_page'       => 1,
        '_per_page'   => 20,
    ];

    /**
     * Predefined per page options.
     *
     * @var array
     */
    protected $perPageOptions = [20, 40, 60, 120, 180];

    /**
     * @var array
     */
    protected $listModes = [
        'list' => [
            'class' => 'fa fa-list fa-fw',
        ]
    ];

    /**
     * @var UserManagerInterface
     */
    protected $userManager;

    /**
     * {@inheritdoc}
     */
    public function getFormBuilder()
    {
        $this->formOptions['data_class'] = $this->getClass();

        $options = $this->formOptions;
        $options['validation_groups'] = (!$this->getSubject() || is_null($this->getSubject()->getId())) ? 'Registration' : 'Profile';

        $formBuilder = $this->getFormContractor()->getFormBuilder($this->getUniqid(), $options);
        $this->defineFormBuilder($formBuilder);

        return $formBuilder;
    }

    /**
     * {@inheritdoc}
     */
    public function getExportFields()
    {
        // avoid security field to be exported
        return array_filter(parent::getExportFields(), function ($v) {
            return !in_array($v, ['password', 'salt']);
        });
    }

    /**
     * {@inheritdoc}
     */
    public function configure()
    {
        $this->formOptions['translation_domain'] = $this->getTranslationDomain();
    }

    /**
     * {@inheritdoc}
     */
    protected function configureDatagridFilters(DatagridMapper $filterMapper)
    {
        $filterMapper
            ->add('username')
            ->add('email')
            ->add('groups')
            ->add('enabled')
            ->add('locked')
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('username')
            ->add('email')
            ->add('groups')
            ->add('enabled', null, ['editable' => true])
            ->add('locked', null, ['editable' => true])
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        /** @var User $user */
        $user = $this->getSubject();
        $authorizationChecker = $this->getConfigurationPool()->getContainer()->get('security.authorization_checker');

        $container      = $this->getConfigurationPool()->getContainer();
        $uploaderHelper = $container->get('vich_uploader.templating.helper.uploader_helper');

        $avatarPreview = $this->trans('form.avatar_not_loaded');
        $avatarExists  = $user && $user->getId() && $user->getAvatar();
        if ($avatarExists) {
            $avatarPreview = '<img src="' . $uploaderHelper->asset($user, 'avatarFile') . '" style="max-width:150px;">';
        };

        $formMapper
            ->tab('User')
                ->with('General', ['class' => 'col-md-7', 'name' => 'user.general'])->end()
                ->with('Profile', ['class' => 'col-md-5', 'name' => 'user.profile'])->end()
            ->end()
        ;

        if ($authorizationChecker->isGranted('ROLE_ADMIN')) {
            $formMapper
                ->tab('Security')
                    ->with('Status', ['class' => 'col-md-6', 'name' => 'security.status'])->end()
                    ->with('Groups', ['class' => 'col-md-6', 'name' => 'security.groups'])->end()
                ->end()
                ->tab('Additional roles')
                    ->with('Additional roles', ['class' => 'col-md-12', 'name' => 'additional_roles.additional_roles'])->end()
                ->end()
            ;
        }

        $formMapper
            ->tab('User')
                ->with('General')
                    ->add('username')
                    ->add('email')
                    ->add('plainPassword', 'text', [
                        'required' => (!$this->getSubject() || is_null($this->getSubject()->getId())),
                    ])
                ->end()


                ->with('Profile')
                    ->add('firstname', null, ['required' => false])
                    ->add('lastname', null, ['required' => false])

                    ->add('avatarPreview', FormStaticControlRawType::class, [
                        'data'     => $avatarPreview,
                        'mapped'   => false,
                        'required' => false,
                    ])
                ->end()
            ->end()
        ;

        if ($avatarExists) {
            $formMapper->tab('User')->with('Profile')->add('avatarRemove', CheckboxType::class, [
                'mapped'   => false,
                'required' => false,
            ])->end()->end();
        }

        $formMapper
            ->tab('User')
                ->with('Profile')
                    ->add('avatarFile', 'file', [
                        'label'    => false,
                        'required' => false
                    ])
                ->end()
            ->end()
        ;

        if ($authorizationChecker->isGranted('ROLE_ADMIN') && $this->getSubject() && !$this->getSubject()->hasRole('ROLE_SUPER_ADMIN')) {
            $formMapper
                ->tab('Security')
                    ->with('Status')
                        ->add('locked', null, ['required' => false])
                        ->add('expired', null, ['required' => false])
                        ->add('enabled', null, ['required' => false])
                        ->add('credentialsExpired', null, ['required' => false])
                    ->end()
                    ->with('Groups')
                        ->add('groups', 'sonata_type_model', [
                            'label'    => false,
                            'required' => false,
                            'expanded' => true,
                            'multiple' => true,
                            'class'    => 'UserBundle:Group'
                        ])
                        ->end()
                ->end()
                ->tab('Additional roles')
                    ->with('Additional roles', ['class' => 'col-md-12 header-hidden'])
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

    /**
     * {@inheritdoc}
     */
    protected function configureShowFields(ShowMapper $showMapper)
    {
        $authorizationChecker = $this->getConfigurationPool()->getContainer()->get('security.authorization_checker');

        $showMapper
            ->with('General', ['class' => 'col-md-6', 'name' => 'user.general'])
                ->add('username')
                ->add('email')
            ->end()
            ->with('Profile', ['class' => 'col-md-6', 'name' => 'user.profile'])
                ->add('firstname')
                ->add('lastname')
            ->end()
        ;

        if ($authorizationChecker->isGranted('ROLE_ADMIN')) {
            $showMapper
                ->with('Groups', ['class' => 'col-md-12', 'name' => 'security.groups'])
                    ->add('groups')
                ->end()
            ;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function preUpdate($user)
    {
        /** @var User $user */
        $this->getUserManager()->updateCanonicalFields($user);
        $this->getUserManager()->updatePassword($user);

        $form = $this->getForm();
        $avatarRemove = $form->has('avatarRemove') && $form->get('avatarRemove')->getData();
        if ($avatarRemove){
            $user->setAvatar(null);
        }
    }

    /**
     * @param UserManagerInterface $userManager
     */
    public function setUserManager(UserManagerInterface $userManager)
    {
        $this->userManager = $userManager;
    }

    /**
     * @return UserManagerInterface
     */
    public function getUserManager()
    {
        return $this->userManager;
    }
}
