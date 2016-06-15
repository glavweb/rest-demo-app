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

use Glavweb\RestBundle\Test\RestTestCase;
use Glavweb\RestBundle\Test\Handler\ListFilterCaseHandler;
use AppBundle\Entity\MovieSession;

/**
 * Class MovieSessionApiControllerTest
 *
 * @author Andrey Nilov <nilov@glavweb.ru>
 */
class MovieSessionApiControllerTest extends RestTestCase
{
    /**
     * @param array  $fixtureFiles
     * @param bool   $append
     * @param bool|  $useCache
     * @param string $fixtureCacheKey
     * @return mixed
     */
    protected function loadTestFixtures($fixtureFiles = [], $append = false, $useCache = true, $fixtureCacheKey = '')
    {
        $fixtureFiles = array_merge([
            '@UserBundle/DataFixtures/ORM/Base/UserData.yml'
        ], $fixtureFiles);

        if ($useCache) {
            $objects = $this->loadCachedFixtureFiles($fixtureFiles, $fixtureCacheKey, $append);
        } else {
            $objects = $this->loadFixtureFiles($fixtureFiles, $append);
        }

        return $objects;
    }
    
    /**
     * Test get movie session
     */
    public function testGetMovieSession()
    {
        /** @var MovieSession $movieSession */
        $objects = $this->loadTestFixtures(['@AppBundle/DataFixtures/ORM/Test/MovieSession/CrudData.yml']);
        $movieSession = $objects['movie-session-1'];

        $this->restItemTestCase('/api/movie-sessions/' .  $movieSession->getId(), [                                    
            'name' => 'Some name',
        ]);

        $this->restScopeTestCase('/api/movie-sessions/' .  $movieSession->getId(), [
            'view' => $this->getScopeConfig('movie_session/view.yml')
        ]);
    }

    /**
     * Test get collection of movie sessions
     */
    public function testGetMovieSessions()
    {
        $objects = $this->loadTestFixtures(['@AppBundle/DataFixtures/ORM/Test/MovieSession/CrudData.yml']);

        // Test scope
        $this->restScopeTestCase('/api/movie-sessions', [
            'list' => $this->getScopeConfig('movie_session/list.yml')
        ], true);

        // Test filters
        $listFilterCaseHandler = new ListFilterCaseHandler([
            'movie-session-1' => $objects['movie-session-1']
        ]);
        
        $listFilterCaseHandler->addCase('name', '=Some name', 'movie-session-1', true);

        $this->restListFilterTestCase('/api/movie-sessions', $listFilterCaseHandler->getCases());
    }
}