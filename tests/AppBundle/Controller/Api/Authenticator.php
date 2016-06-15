<?php

/*
 * This file is part of the "rest demo app" package.
 *
 * (c) GLAVWEB <info@glavweb.ru>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tests\AppBundle\Controller\Api;

use Glavweb\RestBundle\Test\Authenticate\AuthenticateResponse;
use Glavweb\RestBundle\Test\Authenticate\AuthenticatorInterface;
use Symfony\Component\BrowserKit\Client;

/**
 * Class Authenticator
 *
 * @author Andrey Nilov <nilov@glavweb.ru>
 */
class Authenticator implements AuthenticatorInterface
{
    /**
     * Sign in URI
     */
    const SING_IN_URI = '/api/sign-in';

    /**
     * @var Client
     */
    private $client;

    /**
     * @var string
     */
    private $username;

    /**
     * @var string
     */
    private $password;

    /**
     * Authenticator constructor.
     *
     * @param Client $client
     * @param string $username
     * @param string $password
     */
    public function __construct(Client $client, $username, $password)
    {
        $this->client = $client;
        $this->username = $username;
        $this->password = $password;
    }

    /**
     * @return AuthenticateResponse
     */
    public function authenticate()
    {
        $this->client->request('POST', self::SING_IN_URI, [
            'username' => $this->username,
            'password' => $this->password
        ], [], ['HTTP_ACCEPT' => 'application/json']);
        $loginResponse = json_decode($this->client->getResponse()->getContent());

        $authenticateHeaders = [
            'HTTP_TOKEN'    => (isset($loginResponse->Token) ? $loginResponse->Token : null),
            'HTTP_EXPIREAT' => (isset($loginResponse->ExpireAt) ? $loginResponse->ExpireAt : null),
            'HTTP_USERNAME' => (isset($loginResponse->Username) ? $loginResponse->Username : null)
        ];

        return new AuthenticateResponse([], $authenticateHeaders);
    }

    /**
     * @return string
     */
    public function getCacheKey()
    {
        return md5($this->username . '_' . $this->password);
    }
}