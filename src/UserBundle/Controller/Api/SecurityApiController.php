<?php

namespace UserBundle\Controller\Api;

use Doctrine\ORM\EntityRepository;
use Glavweb\RestBundle\Controller\GlavwebRestController;
use UserBundle\Entity\User;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Request\ParamFetcherInterface;
use FOS\RestBundle\View\View;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class SecurityApiController
 * @package UserBundle\Controller\Api
 * 
 * @Rest\NamePrefix("api_user_security_")
 */
class SecurityApiController extends GlavwebRestController
{
    /**
     * Sign in users by pair username:password.
     *
     * @ApiDoc(
     *     views={"default", "user"},
     *     section="Security API",
     *     resource=true,
     *     resourceDescription="Sign in users by pair username:password.",
     *     statusCodes={
     *         200="Returned when successful",
     *         400="Returned when an error has occurred",
     *     }
     * )
     *
     * @Rest\Route("/sign-in", requirements={
     *     "_format": "json|xml"
     * })
     *
     * @Rest\RequestParam(name="username", requirements="\w+", nullable=false, description="Username")
     * @Rest\RequestParam(name="password",                     nullable=false, description="Password")
     *
     * @Rest\View(serializerEnableMaxDepthChecks=true)
     *
     * @param ParamFetcherInterface $paramFetcher
     * @return View
     */
    public function postUserSignInAction(ParamFetcherInterface $paramFetcher)
    {
        $username = $paramFetcher->get('username');
        $password = $paramFetcher->get('password');

        /** @var EntityRepository $repository */
        $repository = $this->getDoctrine()->getManager()->getRepository('UserBundle:User');

        /** @var User $user */
        $user = $repository->findOneBy(['username' => $username]);

        if (!$user || !$this->validatePassword($user, $password)) {
            return new View(array('message' => 'User not found.'), 400);
        }

        if (!$user->isEnabled()) {
            return new View(array('message' => 'Account is disabled.'), 400);
        }

        if (!$user->isAccountNonLocked()) {
            return new View(array('message' => 'Account is locked.'), 400);
        }

        if (!$user->isAccountNonExpired()) {
            return new View(array('message' => 'Account is expired.'), 400);
        }

        if (!$user->isCredentialsNonExpired()) {
            return new View(array('message' => 'Credentials is expired.'), 400);
        }

        $this->generateToken($user);
        $this->getDoctrine()->getManager()->flush();

        $headers = array(
            'Token'    => $user->getApiToken(),
            'ExpireAt' => $user->getApiTokenExpireAt()->format('c'),
            'Username' => $user->getUsername(),
        );

        $view = new View(
            array_merge(array(
                'user' => $user->getId()
            ), $headers),
            200,
            $headers
        );

        $this->setAuthInfoInCookie($headers, $view->getResponse(), $user->getApiTokenExpireAt());

        return $view;
    }

    /**
     * Sign out user by token.
     *
     * @ApiDoc(
     *     views={"default", "user"},
     *     section="Security API",
     *     resource=true,
     *     resourceDescription="Sign out user by token.",
     * )
     *
     * @Rest\Route("/sign-out", requirements={
     *     "_format": "json|xml"
     * })
     *
     * @return View
     * @throws \RuntimeException
     */
    public function deleteSignOutAction()
    {
        $user = $this->getUser();

        if (!$user instanceof User) {
            throw new \RuntimeException('User not found.');
        }

        $user->setApiToken(null);
        $user->setApiTokenExpireAt(null);
        $this->getDoctrine()->getManager()->flush();

        return new View(array(), 200);
    }

    /**
     * Validate token.
     *
     * @ApiDoc(
     *     views={"default", "user"},
     *     section="Security API",
     *     resource=true,
     *     resourceDescription="Validate token.",
     * )
     *
     * @Rest\Route("/validate-token", requirements={
     *     "_format": "json|xml"
     * })
     *
     * @param Request $request
     * @return View
     */
    public function getValidateTokenAction(Request $request)
    {
        $token = '';
        if ($request->headers->has('Token')) {
            $token = $request->headers->get('Token');

        } elseif ($request->cookies->has('Token')) {
            $token = $request->cookies->get('Token');
        }

        $em = $this->getDoctrine()->getManager();
        $repository = $em->getRepository('UserBundle:User');

        // user would be found because if token not valid we get error earlier.
        /** @var User $user */
        $user = $repository->findOneBy(array('apiToken' => $token));

        if (!$user) {
            return new View(array('message' => 'Invalid token.'), 400);
        }

        if (!$user->isEnabled()) {
            return new View(array('message' => 'Account is disabled.'), 400);
        }

        if (!$user->isAccountNonLocked()) {
            return new View(array('message' => 'Account is locked.'), 400);
        }

        if (!$user->isAccountNonExpired()) {
            return new View(array('message' => 'Account is expired.'), 400);
        }

        if (!$user->isCredentialsNonExpired()) {
            return new View(array('message' => 'Credentials is expired.'), 400);
        }

        $headers = array(
            'Token'    => $user->getApiToken(),
            'ExpireAt' => $user->getApiTokenExpireAt()->format('c'),
            'Username' => $user->getUsername()
        );

        $view =  new View($headers, 200, $headers);

        return $view;
    }

    /**
     * @param array     $headers
     * @param Response  $response
     * @param \DateTime $apiTokenExpireAt
     */
    private function setAuthInfoInCookie(array $headers, Response $response, \DateTime $apiTokenExpireAt)
    {
        foreach ($headers as $name => $value) {
            $response->headers->setCookie(new Cookie($name, $value, $apiTokenExpireAt, '/', null, false, false));
        }
    }

    /**
     * @param User   $user
     * @param string $password
     * @return bool
     */
    private function validatePassword(User $user, $password)
    {
        $encoder = $this->get('security.encoder_factory')->getEncoder($user);

        return $encoder->isPasswordValid($user->getPassword(), $password, $user->getSalt());
    }

    /**
     * @param User $user
     * @param int $lifetime
     */
    private function generateToken(User $user, $lifetime = 86400)
    {
        $token = sha1(random_bytes(40));

        $expireAt = new \DateTime();
        $lifetime = $lifetime - $expireAt->getOffset(); // GMT
        $expireAt->add(new \DateInterval('PT' . (int)$lifetime . 'S'));

        $user->setApiToken($token);
        $user->setApiTokenExpireAt($expireAt);
    }
}