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

use AppBundle\Entity\Image;
use Glavweb\RestBundle\Test\RestTestCase;
use Glavweb\RestBundle\Test\Handler\ListFilterCaseHandler;
use AppBundle\Entity\MovieComment;

/**
 * Class MovieCommentApiControllerTest
 *
 * @author Andrey Nilov <nilov@glavweb.ru>
 */
class MovieCommentApiControllerTest extends RestTestCase
{
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
        $fixtureFiles = array_merge([
            '@UserBundle/DataFixtures/ORM/Base/UserData.yml'
        ], $fixtureFiles);

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
     * Test get movie comment
     */
    public function testGetMovieComment()
    {
        /** @var MovieComment $movieComment */
        $objects = $this->loadFixturesAndAuthenticate(['@AppBundle/DataFixtures/ORM/Test/MovieComment/CrudData.yml']);
        $movieComment = $objects['movie-comment-1'];

        $this->restItemTestCase('/api/movie-comments/' .  $movieComment->getId(), [                                    
            'body' => 'Some body',                                    
            'publish' => 1,                                    
        ]);

        $this->restScopeTestCase('/api/movie-comments/' .  $movieComment->getId(), [
            'view' => $this->getScopeConfig('movie_comment/view.yml')
        ]);
    }

    /**
     * Test get collection of movie comments
     */
    public function testGetMovieComments()
    {
        $objects = $this->loadFixturesAndAuthenticate(['@AppBundle/DataFixtures/ORM/Test/MovieComment/CrudData.yml']);

        // Test scope
        $this->restScopeTestCase('/api/movie-comments', [
            'list' => $this->getScopeConfig('movie_comment/list.yml')
        ], true);

        // Test filters
        $listFilterCaseHandler = new ListFilterCaseHandler([
            'movie-comment-1' => $objects['movie-comment-1']
        ]);
        
        $listFilterCaseHandler->addCase('body', '=Some body', 'movie-comment-1', true);
        $listFilterCaseHandler->addCase('publish', '=1', 'movie-comment-1', true);

        $this->restListFilterTestCase('/api/movie-comments', $listFilterCaseHandler->getCases());
    }

    /**
     * Test create movie comment
     */
    public function testCreateMovieComment()
    {
        $objects = $this->loadFixturesAndAuthenticate(['@AppBundle/DataFixtures/ORM/Test/MovieComment/CrudData.yml'], false, false);

        // Create
        $files = [];
        $this->sendCreateRestRequest('/api/movie-comments', [
            'body' => 'new Some body',
            'movie' => $objects['movie-1']->getId(),
        ], $files);
        $this->assertStatusCode(201, $this->client);

        // Test in DB
        $this->assertLastEntityFromDb(MovieComment::class, [
            'body' => 'new Some body',
            'movie' => $objects['movie-1']->getId(),
        ]);
    }

    /**
     * Test update movie comment
     */
    public function testUpdateMovieComment()
    {
        /** @var MovieComment $movieComment */
        $objects = $this->loadFixturesAndAuthenticate(['@AppBundle/DataFixtures/ORM/Test/MovieComment/CrudData.yml'], false, false);
        $movieComment = $objects['movie-comment-1'];

        // Update
        $this->sendUpdateRestRequest('/api/movie-comments/' . $movieComment->getId(), [
            'body' => 'update Some body',
        ]);
        $this->assertStatusCode(204, $this->client);

        // Test in DB
        $this->assertEntityFromDb(MovieComment::class, $movieComment->getId(), [
            'body' => 'update Some body',
        ]);
    }

    /**
     * Test link movie comment to image
     */
    public function testLinkMovieCommentImages()
    {
        /** @var MovieComment $movieComment */
        /** @var Image $image */
        $objects = $this->loadFixturesAndAuthenticate(['@AppBundle/DataFixtures/ORM/Test/MovieComment/CrudData.yml'], false, false);
        $movieComment = $objects['movie-comment-1'];
        $image = $objects['image-2'];

        // Test link
        $this->assertFalse($movieComment->getImages()->contains($image));

        // Send link request
        $this->sendLinkRestRequest('/api/movie-comments/' . $movieComment->getId() . '/images/' . $image->getId());
        $this->assertStatusCode(204, $this->client);

        // Test link
        $doctrine = $this->getContainer()->get('doctrine');
        $doctrine->getManager()->clear(MovieComment::class);
        $movieComment = $this->getRepository(MovieComment::class)->find($movieComment->getId());

        $this->assertTrue($movieComment->getImages()->contains($image));
    }

    /**
     * Test unlink image from movie comment
     */
    public function testUnlinkMovieCommentImages()
    {
        /** @var MovieComment $movieComment */
        /** @var Image $image */
        $objects = $this->loadFixturesAndAuthenticate(['@AppBundle/DataFixtures/ORM/Test/MovieComment/CrudData.yml'], false, false);
        $movieComment = $objects['movie-comment-1'];
        $image = $objects['image-1'];

        // Test link
        $this->assertTrue($movieComment->getImages()->contains($image));

        // Send link request
        $this->sendUnlinkRestRequest('/api/movie-comments/' . $movieComment->getId() . '/images/' . $image->getId());
        $this->assertStatusCode(204, $this->client);

        // Test link
        $doctrine = $this->getContainer()->get('doctrine');
        $doctrine->getManager()->clear(MovieComment::class);
        $movieComment = $this->getRepository(MovieComment::class)->find($movieComment->getId());

        $this->assertFalse($movieComment->getImages()->contains($image));
    }

    /**
     * Test delete movie comment
     */
    public function testDeleteMovieComment()
    {
        /** @var MovieComment $movieComment */
        $objects = $this->loadFixturesAndAuthenticate(['@AppBundle/DataFixtures/ORM/Test/MovieComment/CrudData.yml'], false, false);
        $movieComment = $objects['movie-comment-1'];

        $this->restDeleteTestCase('/api/movie-comments', MovieComment::class, $movieComment->getId());
    }

}