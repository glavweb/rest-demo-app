<?php

/*
 * This file is part of the "rest demo app" package.
 *
 * (c) GLAVWEB <info@glavweb.ru>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace AppBundle\Filter;

use Doctrine\Bundle\DoctrineBundle\Registry;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Class Configurator
 *
 * @author Andrey Nilov <nilov@glavweb.ru>
 */
class Configurator
{
    /**
     * @var Registry
     */
    private $em;

    /**
     * @var TokenStorageInterface
     */
    protected $tokenStorage;

    /**
     * @var AuthorizationCheckerInterface
     */
    private $authorizationChecker;

    /**
     * @param EntityManagerInterface $em
     * @param TokenStorageInterface $tokenStorage
     * @param AuthorizationCheckerInterface $authorizationChecker
     */
    public function __construct(EntityManagerInterface $em, TokenStorageInterface $tokenStorage, AuthorizationCheckerInterface $authorizationChecker)
    {
        $this->em                   = $em;
        $this->tokenStorage         = $tokenStorage;
        $this->authorizationChecker = $authorizationChecker;
    }

    /**
     * onKernelRequest
     */
    public function onKernelRequest()
    {
        /** @var MovieCommentFilter $movieCommentFilter */
        $movieCommentFilter = $this->em->getFilters()->getFilter('movie_comment_filter');
        $movieCommentFilter->setTokenStorage($this->tokenStorage);
        $movieCommentFilter->setAuthorizationChecker($this->authorizationChecker);
    }
}