<?php

namespace UserBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * Class UserBundle
 * @package UserBundle
 */
class UserBundle extends Bundle
{
    /**
     * {@inheritdoc}
     */
    public function getParent()
    {
        return 'FOSUserBundle';
    }
}