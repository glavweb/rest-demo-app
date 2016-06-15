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
use AppBundle\Entity\Tag;

/**
 * Class TagApiControllerTest
 *
 * @author Andrey Nilov <nilov@glavweb.ru>
 */
class TagApiControllerTest extends RestTestCase
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
     * Test get tag
     */
    public function testGetTag()
    {
        /** @var Tag $tag */
        $objects = $this->loadTestFixtures(['@AppBundle/DataFixtures/ORM/Test/Tag/CrudData.yml']);
        $tag = $objects['tag-1'];

        $this->restItemTestCase('/api/tags/' .  $tag->getId(), [
            'name' => 'Some name',
        ]);

        $this->restScopeTestCase('/api/tags/' .  $tag->getId(), [
            'view' => $this->getScopeConfig('tag/view.yml')
        ]);
    }

    /**
     * Test get collection of tags
     */
    public function testGetTags()
    {
        $objects = $this->loadTestFixtures(['@AppBundle/DataFixtures/ORM/Test/Tag/CrudData.yml']);

        // Test scope
        $this->restScopeTestCase('/api/tags', [
            'list' => $this->getScopeConfig('tag/list.yml')
        ], true);

        // Test filters
        $listFilterCaseHandler = new ListFilterCaseHandler([
            'tag-1' => $objects['tag-1']
        ]);
        
        $listFilterCaseHandler->addCase('name', '=Some name', 'tag-1', true);

        $this->restListFilterTestCase('/api/tags', $listFilterCaseHandler->getCases());
    }

}