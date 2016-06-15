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
use Glavweb\RestBundle\Util\FileUtil;
use AppBundle\Entity\Image;

/**
 * Class ImageApiControllerTest
 *
 * @author Andrey Nilov <nilov@glavweb.ru>
 */
class ImageApiControllerTest extends RestTestCase
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
     * Test get image
     */
    public function testGetImage()
    {
        /** @var Image $image */
        $objects = $this->loadFixturesAndAuthenticate(['@AppBundle/DataFixtures/ORM/Test/Image/CrudData.yml']);
        $image = $objects['image-1'];

        $this->restItemTestCase('/api/images/' .  $image->getId(), [                                    
            'name' => 'Some name',                                    
            'image' => 'test_image.jpg',
        ]);

        $this->restScopeTestCase('/api/images/' .  $image->getId(), [
            'view' => $this->getScopeConfig('image/view.yml')
        ]);
    }

    /**
     * Test get collection of images
     */
    public function testGetImages()
    {
        $objects = $this->loadFixturesAndAuthenticate(['@AppBundle/DataFixtures/ORM/Test/Image/CrudData.yml']);

        // Test scope
        $this->restScopeTestCase('/api/images', [
            'list' => $this->getScopeConfig('image/list.yml')
        ], true);

        // Test filters
        $listFilterCaseHandler = new ListFilterCaseHandler([
            'image-1' => $objects['image-1']
        ]);
        
        $listFilterCaseHandler->addCase('name', '=Some name', 'image-1', true);

        $this->restListFilterTestCase('/api/images', $listFilterCaseHandler->getCases());
    }

    /**
     * Test create image
     */
    public function testCreateImage()
    {
        $this->loadFixturesAndAuthenticate(['@AppBundle/DataFixtures/ORM/Test/Image/CrudData.yml']);

        // Create
        $files = [
            'imageFile' => $this->fileFaker->getFakeUploadedImageJpeg('new_test_image.jpg', 120, 120)
        ];
        $this->sendCreateRestRequest('/api/images', [
            'name' => 'new Some name',
        ], $files);
        $this->assertStatusCode(201, $this->client);

        // Test in DB
        $this->assertLastEntityFromDb(Image::class, [
            'name' => 'new Some name',
        ]);
    }

    /**
     * Test update image
     */
    public function testUpdateImage()
    {
        /** @var Image $image */
        $objects = $this->loadFixturesAndAuthenticate(['@AppBundle/DataFixtures/ORM/Test/Image/CrudData.yml']);
        $image = $objects['image-1'];

        // Update
        $this->sendUpdateRestRequest('/api/images/' . $image->getId(), [
            'name' => 'update Some name',
        ]);
        $this->assertStatusCode(204, $this->client);

        // Test in DB
        $this->assertEntityFromDb(Image::class, $image->getId(), [
            'name' => 'update Some name',
        ]);
    }

    /**
     * Test upload image for image
     */
    public function testUploadImageFileImage()
    {
        /** @var Image $image */
        $objects = $this->loadFixturesAndAuthenticate(['@AppBundle/DataFixtures/ORM/Test/Image/CrudData.yml']);
        $image = $objects['image-1'];

        $this->restUploadFileTestCase('/api/images/' . $image->getId() . '/file/image', [
            // Test min size validators
            [
                'type'     => 'image',
                'fileName' => 'image.jpg',
                'width'    => 120 - 1,
                'height'   => 120 - 1,
                'status'   => 400
            ],

            // Test max size validators
            [
                'type'     => 'image',
                'fileName' => 'image.jpg',
                'width'    => 1200 + 1,
                'height'   => 1200 + 1,
                'status'   => 400
            ],

            // Test upload executed file
            [
                'type'   => 'php',
                'status' => 400
            ],

            // Test content type
            [
                'type'     => 'image',
                'fileName' => 'image.jpg',
                'width'    => 120,
                'height'   => 120,
                'status'   => 204,
                'after'    => function () use ($image) {
                    $uploaderHelper = $this->getContainer()->get('vich_uploader.templating.helper.uploader_helper');

                    $doctrine = $this->getContainer()->get('doctrine');
                    $doctrine->getManager()->clear(Image::class);
                    $image = $this->getRepository(Image::class)->find($image->getId());

                    $this->assertTrue((bool)$image);

                    $imageUrl = $uploaderHelper->asset($image, 'imageFile');
                    $this->assertContentTypeFile($imageUrl, 'image/jpeg');
                }
            ],
        ]);
    }

    /**
     * Test delete image for image
     */
    public function testDeleteImageFileImage()
    {
        $uploaderHelper = $this->getContainer()->get('vich_uploader.templating.helper.uploader_helper');

        /** @var Image $image */
        $objects = $this->loadFixturesAndAuthenticate(['@AppBundle/DataFixtures/ORM/Test/Image/CrudData.yml']);
        $image = $objects['image-1'];

        $this->assertTrue((bool)$image->getImage());

        $imageUrl = $this->getAbsoluteUri($uploaderHelper->asset($image, 'imageFile'));
        $this->assertTrue(FileUtil::isFile($imageUrl));

        $this->sendDeleteRestRequest('/api/images/' . $image->getId() . '/file/image');

        // Test in DB
        $doctrine = $this->getContainer()->get('doctrine');
        $doctrine->getManager()->clear(Image::class);
        $image = $this->getRepository(Image::class)->find($image->getId());

        $this->assertFalse((bool)$image->getImage());
        $this->assertFalse(FileUtil::isFile($imageUrl));
    }

    /**
     * Test delete image
     */
    public function testDeleteImage()
    {
        /** @var Image $image */
        $objects = $this->loadFixturesAndAuthenticate(['@AppBundle/DataFixtures/ORM/Test/Image/CrudData.yml']);
        $image = $objects['image-1'];

        $this->restDeleteTestCase('/api/images', Image::class, $image->getId());
    }

}