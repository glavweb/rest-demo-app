<?php

/*
 * This file is part of the "rest demo app" package.
 *
 * (c) GLAVWEB <info@glavweb.ru>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace UserBundle\Security\Authorization;

use Doctrine\Bundle\DoctrineBundle\Registry;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Authentication\Token\PreAuthenticatedToken;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Http\Authentication\SimplePreAuthenticatorInterface;
use UserBundle\Entity\User;

/**
 * Class ApiTokenAuthenticator
 *
 * @author Andrey Nilov <nilov@glavweb.ru>
 */
class ApiTokenAuthenticator implements SimplePreAuthenticatorInterface
{
    /**
     * @var Registry
     */
    private $doctrine;

    /**
     * @param Registry $doctrine
     */
    public function __construct(Registry $doctrine)
    {
        $this->doctrine = $doctrine;
    }

    /**
     * @param Request $request
     * @param string  $providerKey
     * @return PreAuthenticatedToken
     */
    public function createToken(Request $request, $providerKey)
    {
        $credentials = array(
            'token'    => $this->getValueByHeaderOrCookie($request, 'Token'),
            'username' => $this->getValueByHeaderOrCookie($request, 'Username'),
            'expireAt' => $this->getValueByHeaderOrCookie($request, 'ExpireAt'),
        );

        return new PreAuthenticatedToken(
            'anon.',
            $credentials,
            $providerKey
        );
    }

    /**
     * @param TokenInterface        $token
     * @param UserProviderInterface $userProvider
     * @param string                $providerKey
     * @return PreAuthenticatedToken
     */
    public function authenticateToken(TokenInterface $token, UserProviderInterface $userProvider, $providerKey)
    {
        $em = $this->doctrine->getManager();
        $userRepository = $em->getRepository('UserBundle:User');

        $credentials = $token->getCredentials();

        if ($credentials['token']) {
            $user = $userRepository->findOneByApiToken($credentials['token']);

            if ($user) {
                return new PreAuthenticatedToken(
                    $user,
                    $credentials,
                    $providerKey,
                    $user->getRoles()
                );
            }
        }

        return new PreAuthenticatedToken(
            'anon.',
            $credentials,
            $providerKey
        );
    }

    /**
     * @param Request                 $request
     * @param AuthenticationException $exception
     * @return Response
     */
    public function onAuthenticationFailure(Request $request, AuthenticationException $exception)
    {
        return new Response("Authentication Failed.", 403);
    }

    /**
     * @param TokenInterface $token
     * @param string         $providerKey
     * @return bool
     */
    public function supportsToken(TokenInterface $token, $providerKey)
    {
        return $token instanceof PreAuthenticatedToken && $token->getProviderKey() === $providerKey;
    }

    /**
     * @param Request $request
     * @param string  $name
     * @return array|mixed|string
     */
    private function getValueByHeaderOrCookie(Request $request, $name)
    {
        if ($request->headers->has($name)) {
            return $request->headers->get($name);
        }

        if ($request->cookies->has($name)) {
            return $request->cookies->get($name);
        }
    }
}
