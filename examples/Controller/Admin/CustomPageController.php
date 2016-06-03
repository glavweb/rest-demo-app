<?php

namespace ExampleBundle\Controller\Admin;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sonata\AdminBundle\Controller\CoreController;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class CustomPageController
 * @package ExampleBundle\Controller\Admin
 */
class CustomPageController extends CoreController
{
    /**
     * @Route("/custom-admin-page", name="custom_admin_page")
     *
     * @param Request $request
     * @param Project $project
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction(Request $request)
    {
        return $this->render('ExampleBundle:Admin:custom_page.html.twig', array(
            'base_template' => $this->getBaseTemplate(),
            'admin_pool'    => $this->container->get('sonata.admin.pool'),
            'blocks'        => $this->container->getParameter('sonata.admin.configuration.dashboard_blocks'),
        ));
    }
}
