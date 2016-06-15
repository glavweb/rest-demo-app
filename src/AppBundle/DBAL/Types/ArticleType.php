<?php

/*
 * This file is part of the "rest demo app" package.
 *
 * (c) GLAVWEB <info@glavweb.ru>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace AppBundle\DBAL\Types;

use Fresh\DoctrineEnumBundle\DBAL\Types\AbstractEnumType;

/**
 * Class ArticleType
 *
 * @author Andrey Nilov <nilov@glavweb.ru>
 */
class ArticleType extends AbstractEnumType
{
    const NEWS         = 'news';
    const PHOTO_REPORT = 'photo_report';
    const ARTICLE      = 'article';

    /**
     * @var array
     */
    protected static $choices = array(
        self::NEWS         => 'News',
        self::PHOTO_REPORT => 'Photo report',
        self::ARTICLE      => 'Article'
    );
}