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
use AppBundle\Entity\Movie;

/**
 * Class MovieApiControllerTest
 *
 * @author Andrey Nilov <nilov@glavweb.ru>
 */
class MovieApiControllerTest extends RestTestCase
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
     * Test get movie
     */
    public function testGetMovie()
    {
        /** @var Movie $movie */
        $objects = $this->loadTestFixtures(['@AppBundle/DataFixtures/ORM/Test/Movie/CrudData.yml']);
        $movie = $objects['movie-1'];

        $this->restItemTestCase('/api/movies/' .  $movie->getId(), [                                    
            'name' => 'Some name',
        ]);

        $this->restScopeTestCase('/api/movies/' .  $movie->getId(), [
            'view' => $this->getScopeConfig('movie/view.yml')
        ]);
    }

    /**
     * Test get collection of movies
     */
    public function testGetMovies()
    {
        $objects = $this->loadTestFixtures(['@AppBundle/DataFixtures/ORM/Test/Movie/CrudData.yml']);

        // Test scope
        $this->restScopeTestCase('/api/movies', [
            'list' => $this->getScopeConfig('movie/list.yml')
        ], true);

        // Test filters
        $listFilterCaseHandler = new ListFilterCaseHandler([
            'movie-1' => $objects['movie-1']
        ]);
        
        $listFilterCaseHandler->addCase('name', '=Some name', 'movie-1', true);

        $this->restListFilterTestCase('/api/movies', $listFilterCaseHandler->getCases());
    }

}