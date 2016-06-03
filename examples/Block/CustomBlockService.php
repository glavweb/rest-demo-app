<?php

namespace ExampleBundle\Block;

use Sonata\BlockBundle\Block\BaseBlockService;
use Sonata\BlockBundle\Block\BlockContextInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Class CustomBlockService
 * @package ExampleBundle\Block
 */
class CustomBlockService extends BaseBlockService
{
    /**
     * @param BlockContextInterface $blockContext
     * @param Response $response
     * @return Response
     */
    public function execute(BlockContextInterface $blockContext, Response $response = null)
    {
        $settings     = $blockContext->getSettings();
        $customOption = $settings['custom_option'];

        return $this->renderResponse($blockContext->getTemplate(), array(
            'block'        => $blockContext->getBlock(),
            'settings'     => $blockContext->getSettings()
        ));
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultSettings(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'template'     => 'ExampleBundle:Block:custom.html.twig',
            'customOption' => null,
        ));
    }
}