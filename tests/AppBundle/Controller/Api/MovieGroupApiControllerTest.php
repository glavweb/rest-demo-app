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
use AppBundle\Entity\MovieGroup;

/**
 * Class MovieGroupApiControllerTest
 *
 * @author Andrey Nilov <nilov@glavweb.ru>
 */
class MovieGroupApiControllerTest extends RestTestCase
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
     * Test get movie group
     */
    public function testGetMovieGroup()
    {
        /** @var MovieGroup $movieGroup */
        $objects = $this->loadTestFixtures(['@AppBundle/DataFixtures/ORM/Test/MovieGroup/CrudData.yml']);
        $movieGroup = $objects['movie-group-1'];

        $this->restItemTestCase('/api/movie-groups/' .  $movieGroup->getId(), [                                    
            'name' => 'Some name',
        ]);

        $this->restScopeTestCase('/api/movie-groups/' .  $movieGroup->getId(), [
            'view' => $this->getScopeConfig('movie_group/view.yml')
        ]);
    }

    /**
     * Test get collection of movie groups
     */
    public function testGetMovieGroups()
    {
        $objects = $this->loadTestFixtures(['@AppBundle/DataFixtures/ORM/Test/MovieGroup/CrudData.yml']);

        // Test scope
        $this->restScopeTestCase('/api/movie-groups', [
            'list' => $this->getScopeConfig('movie_group/list.yml')
        ], true);

        // Test filters
        $listFilterCaseHandler = new ListFilterCaseHandler([
            'movie-group-1' => $objects['movie-group-1']
        ]);
        
        $listFilterCaseHandler->addCase('name', '=Some name', 'movie-group-1', true);

        $this->restListFilterTestCase('/api/movie-groups', $listFilterCaseHandler->getCases());
    }

}