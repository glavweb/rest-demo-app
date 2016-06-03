<?php

namespace Tests\AppBundle\Controller\Api;

use Codeception\Util\JsonType;
use Glavweb\RestBundle\Dts\Dts;
use Glavweb\RestBundle\Dts\DtsYamlLoader;
use Glavweb\RestBundle\Test\RestTestCase;
use AppBundle\Entity\Article;
use Symfony\Component\Config\FileLocator;

/**
 * Class ArticleApiControllerTest
 * @package AppBundle\Tests\Controller\Api
 */
class ArticleApiControllerTest extends RestTestCase
{
    /**
     * @var bool
     */
    protected $useStored = true;

    /**
     * @param array  $fixtureFiles
     * @param bool   $append
     * @param bool|  $useCache
     * @param string $fixtureCacheKey
     * @param string $username
     * @param string $password
     * @return mixed
     */
    protected function loadFixturesAndAuthenticate($fixtureFiles = [], $append = false, $useCache = true, $fixtureCacheKey = '', $username = 'admin', $password = 'qwerty')
    {
        $fixtureFiles = array_merge([], $fixtureFiles);

        if ($useCache) {
            $objects = $this->loadCachedFixtureFiles($fixtureFiles, $fixtureCacheKey, $append);
        } else {
            $objects = $this->loadFixtureFiles($fixtureFiles, $append);
        }

        $authenticator = new Authenticator($this->client, $username, $password);
        $this->authenticate($authenticator, $useCache);

        return $objects;
    }
    
    /**
     * Test get article
     */
    public function testGetArticle()
    {
        /** @var Article $article */
        $objects = $this->loadFixturesAndAuthenticate(['@AppBundle/DataFixtures/ORM/Test/Article/CrudData.yml']);
        $article = $objects['article-1'];

        $guesser = $this->getContainer()->get('glavweb_rest.test.view_action_guesser')
            ->setModel($article)
            ->setRoute('api_article_get_article', ['article' => $article->getId()])
            ->setSkipValues([
                'image',
                'imageMobile',
                'createdAt',
                'galleryItems'
            ])
        ;

        $this->assertViewRestActionByGuesser($guesser);
    }

    /**
     * Test get collection of articles
     */
    public function testGetArticles()
    {
        /** @var Article $article */
        $objects = $this->loadFixturesAndAuthenticate(['@AppBundle/DataFixtures/ORM/Test/Article/CrudData.yml']);
        $article = $objects['article-1'];

        $guesser = $this->getContainer()->get('glavweb_rest.test.list_action_guesser')
            ->setModels([
                'article-1' => $objects['article-1'],
                'article-2' => $objects['article-2'],
            ])
            ->setRoute('api_article_get_articles')
            ->setSkipValues([
                'image',
                'imageMobile',
            ])
            ->setFilters([
                'name' => ['=Some name 1', 'article-1'],
                'slug' => ['=Some slug 2', 'article-2'],
                'lead' => '=Some lead',
                'body' => '=Some body',
                'type' => '=article',
                'view' => '=panel_default',
                'font' => '=playfair',
                'color' => '=cyan',
                'isPublish' => '=1'
            ])
        ;

        $this->assertListRestActionByGuesser($guesser);
    }
}