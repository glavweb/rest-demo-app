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
use AppBundle\Entity\Article;

/**
 * Class ArticleApiControllerTest
 *
 * @author Andrey Nilov <nilov@glavweb.ru>
 */
class ArticleApiControllerTest extends RestTestCase
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
     * Test get article
     */
    public function testGetArticle()
    {
        /** @var Article $article */
        $objects = $this->loadTestFixtures(['@AppBundle/DataFixtures/ORM/Test/Article/CrudData.yml']);
        $article = $objects['article-1'];

        $this->restItemTestCase('/api/articles/' .  $article->getId(), [                                    
            'type' => 'news',                                    
            'name' => 'Some name',                                    
            'body' => 'Some body',                                    
            'publish' => 1,                                    
            'publishAt' => (new \DateTime('2016-06-15 06:12'))->format('c'),
        ]);

        $this->restScopeTestCase('/api/articles/' .  $article->getId(), [
            'view' => $this->getScopeConfig('article/view.yml')
        ]);
    }

    /**
     * Test get collection of articles
     */
    public function testGetArticles()
    {
        $objects = $this->loadTestFixtures(['@AppBundle/DataFixtures/ORM/Test/Article/CrudData.yml']);

        // Test scope
        $this->restScopeTestCase('/api/articles', [
            'list' => $this->getScopeConfig('article/list.yml')
        ], true);

        // Test filters
        $listFilterCaseHandler = new ListFilterCaseHandler([
            'article-1' => $objects['article-1']
        ]);
        
        $listFilterCaseHandler->addCase('type', '=news', 'article-1', true);
        $listFilterCaseHandler->addCase('name', '=Some name', 'article-1', true);
        $listFilterCaseHandler->addCase('body', '=Some body', 'article-1', true);
        $listFilterCaseHandler->addCase('publish', '=1', 'article-1', true);
        $listFilterCaseHandler->addCase('publishAt', '=2016-06-15 06:12', 'article-1', true);

        $this->restListFilterTestCase('/api/articles', $listFilterCaseHandler->getCases());
    }

    /**
     * Test types of article
     */
    public function testGetConstantType()
    {
        $this->loadTestFixtures(['@AppBundle/DataFixtures/ORM/Test/Article/CrudData.yml']);

        $this->sendQueryRestRequest('/api/articles/constant/type');
        $this->assertStatusCode(200, $this->client);

        $this->assertDataContains($this->getResponseData(), [
            'News' => 'news',
            'Photo report' => 'photo_report',
            'Article' => 'article',
        ]);
    }

}