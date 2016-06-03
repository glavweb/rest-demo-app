<?php

namespace Tests\AppBundle\Controller\Api;

use Glavweb\RestBundle\Test\Authenticate\AuthenticateResponse;
use Glavweb\RestBundle\Test\Authenticate\AuthenticatorInterface;
use Symfony\Component\BrowserKit\Client;

/**
 * Class Authenticator
 * @package AppBundle\Tests\Controller\Api
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
        ], array(), array('HTTP_ACCEPT' => 'application/json'));
        $loginResponse = json_decode($this->client->getResponse()->getContent());
        var_dump($loginResponse); echo __CLASS__ . ': ' . __LINE__; exit;

        $authenticateHeaders = array(
            'token'    => (isset($loginResponse->apiToken) ? $loginResponse->apiToken : null),
            'expireAt' => (isset($loginResponse->createdAt) ? $loginResponse->createdAt : null),
            'username' => (isset($loginResponse->login) ? $loginResponse->login : null)
        );

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